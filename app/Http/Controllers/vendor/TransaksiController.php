<?php

namespace App\Http\Controllers\vendor;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Vendor\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Vendor\TransaksiItem;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor\TransaksiItemSpecifications;
use App\Http\Concerns\HasVendorContext;
use App\Http\Requests\StoreTransaksiRequest;
use App\Http\Requests\UpdateTransaksiRequest;
use App\Http\Responses\FlashMessage;
use App\Services\AuditLogService;
use App\Services\VoidTransactionService;
use App\Actions\Transaksi\CreateTransaksi;



class TransaksiController extends Controller
{
    use HasVendorContext;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate input
        $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,processing,quality_check,completed,cancelled',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        // Build transaction query
        $query = Transaksi::query();

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->withStatus($request->status);
        }

        // Apply date range filter
        $query->withinDateRange($request->start_date, $request->end_date);

        // Get transactions with pagination
        $transaksis = $query->with(['pelanggan', 'user', 'transaksiItem.produk'])
            ->latest('tanggal_dibuat')
            ->paginate(10)
            ->appends($request->query());

        // Status options for filter dropdown
        $statusOptions = [
            'pending' => 'Pending',
            'processing' => 'Diproses',
            'quality_check' => 'QC',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return view('transaksi.index', compact('transaksis', 'statusOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendor = $this->requireVendor();

        if (!$vendor) {
            return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $pelanggans = Pelanggan::where('vendor_id', $vendor->id)->get();

        if ($pelanggans->isEmpty()) {
            return FlashMessage::info(redirect()->route('vendor.customers.create'), 'Anda belum memiliki pelanggan. Silakan tambahkan pelanggan terlebih dahulu.');
        }

        $produks = Produk::where('vendor_id', $vendor->id)
            ->with(['spesifikasiProduk.spesifikasi', 'spesifikasiProduk.bahanSpesifikasiProduk'])
            ->get();

        if ($produks->isEmpty()) {
            return FlashMessage::info(redirect()->route('vendor.products.create'), 'Anda belum memiliki produk. Silakan tambahkan produk terlebih dahulu.');
        }

        $paymentMethods = [
            'cash' => 'Cash',
            'transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'e_wallet' => 'E-Wallet',
        ];

        return view('transaksi.create', compact('pelanggans', 'produks', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransaksiRequest $request)
    {
        // Get current vendor from session
        $vendor = $this->requireVendor();

        if (!$vendor) {
            return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $validated = $request->validated();

        // Begin transaction
        DB::beginTransaction();

        try {
            $transaksi = (new CreateTransaksi)->run([
                'vendor_id' => $vendor->id,
                'user_id' => Auth::id(),
                'pelanggan_id' => $validated['pelanggan_id'],
                'payment_method' => $validated['payment_method'],
                'estimasi_selesai' => $validated['estimasi_selesai'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'terbayar' => $request->input('terbayar'),
                'items' => $validated['items'],
            ]);

            DB::commit();

            AuditLogService::logCreated($transaksi, 'Transaksi baru dibuat: ' . $transaksi->kode);

            return FlashMessage::success(redirect()->route('transaksi.show', $transaksi->id), 'Transaksi berhasil dibuat dengan kode: ' . $transaksi->kode);
        } catch (\Exception $e) {
            DB::rollback();
            return FlashMessage::backError('Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vendor = $this->requireVendor();

        if (!$vendor) {
            return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)
            ->with([
                'pelanggan',
                'user',
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan'
            ])
            ->findOrFail($id);

        $this->authorize('view', $transaksi);

        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vendor = $this->requireVendor();

        if (!$vendor) {
            return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)
            ->with([
                'pelanggan',
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan'
            ])
            ->findOrFail($id);

        $pelanggans = Pelanggan::where('vendor_id', $vendor->id)->get();
        $produks = Produk::where('vendor_id', $vendor->id)
            ->with(['spesifikasiProduk.spesifikasi', 'spesifikasiProduk.bahanSpesifikasiProduk'])
            ->get();

        $paymentMethods = [
            'cash' => 'Cash',
            'transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'e_wallet' => 'E-Wallet',
        ];

        $statusOptions = [
            'pending' => 'Pending',
            'processing' => 'Diproses',
            'quality_check' => 'QC',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return view('transaksi.edit', compact('transaksi', 'pelanggans', 'produks', 'paymentMethods', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransaksiRequest $request, string $id)
    {
        $vendor = $this->requireVendor();

        if (!$vendor) {
            return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)->findOrFail($id);

        $this->authorize('update', $transaksi);

        $validated = $request->validated();

        // Begin transaction
        // Capture old values for audit log
        $oldValues = [
            'total_harga' => $transaksi->total_harga,
            'status' => $transaksi->status,
            'payment_method' => $transaksi->payment_method,
            'pelanggan_id' => $transaksi->pelanggan_id,
        ];

        DB::beginTransaction();

        try {
            // Calculate total price
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $totalPrice += $item['kuantitas'] * $item['harga_satuan'];
            }

            // Update transaction
            $progressMap = [
                'pending' => 0,
                'processing' => 25,
                'quality_check' => 80,
                'completed' => 100,
                'cancelled' => 0
            ];

            // Jika ada perubahan pada total atau pembayaran
            if ($request->has('terbayar')) {
                $terbayar = $request->input('terbayar');
                $kembali = max(0, $terbayar - $transaksi->total_harga);

                $transaksi->terbayar = $terbayar;
                $transaksi->kembali = $kembali;
            }

            $transaksi->update([
                'pelanggan_id' => $request->pelanggan_id,
                'total_harga' => $totalPrice,
                'status' => $request->status,
                'progress_percentage' => $progressMap[$request->status],
                'payment_method' => $request->payment_method,
                'estimasi_selesai' => $request->estimasi_selesai,
                'catatan' => $request->catatan
            ]);

            // Track existing items to determine which to delete
            $existingItemIds = $transaksi->transaksiItem->pluck('id')->toArray();
            $updatedItemIds = [];

            // Process transaction items
            foreach ($request->items as $itemData) {
                if (isset($itemData['id']) && !empty($itemData['id'])) {
                    // Update existing item
                    $item = TransaksiItem::findOrFail($itemData['id']);
                    $item->update([
                        'produk_id' => $itemData['produk_id'],
                        'kuantitas' => $itemData['kuantitas'],
                        'harga_satuan' => $itemData['harga_satuan']
                    ]);

                    $updatedItemIds[] = $item->id;

                    // Delete existing specifications
                    TransaksiItemSpecifications::where('transaksi_item_id', $item->id)->delete();
                } else {
                    // Create new item
                    $item = TransaksiItem::create([
                        'vendor_id' => $vendor->id,
                        'transaksi_id' => $transaksi->id,
                        'produk_id' => $itemData['produk_id'],
                        'kuantitas' => $itemData['kuantitas'],
                        'harga_satuan' => $itemData['harga_satuan']
                    ]);

                    $updatedItemIds[] = $item->id;
                }

                // Process specifications if provided
                if (isset($itemData['specifications']) && is_array($itemData['specifications'])) {
                    foreach ($itemData['specifications'] as $specId => $specData) {
                        if (empty($specData['value']) && !isset($specData['bahan_id'])) {
                            continue; // Skip empty specs
                        }

                        TransaksiItemSpecifications::create([
                            'vendor_id' => $vendor->id,
                            'transaksi_item_id' => $item->id,
                            'spesifikasi_produk_id' => $specId,
                            'bahan_id' => $specData['bahan_id'] ?? null,
                            'value' => $specData['value'] ?? null,
                            'input_type' => $specData['input_type'] ?? 'text',
                            'price' => $specData['price'] ?? 0
                        ]);
                    }
                }
            }

            // Delete items that were not updated
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($itemsToDelete)) {
                TransaksiItem::whereIn('id', $itemsToDelete)->delete();
            }

            DB::commit();

            AuditLogService::logUpdated($transaksi, $oldValues, 'Transaksi diperbarui: ' . $transaksi->kode);

            return FlashMessage::success(redirect()->route('transaksi.show', $transaksi->id), 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return FlashMessage::backError('Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * DEPRECATED: Menggunakan void approach alih-alih hard delete.
     * Redirect ke void action jika transaksi masih bisa di-void.
     */
    public function destroy(string $id)
    {
        $vendor = $this->requireVendor();

        if (!$vendor) {
            return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)->findOrFail($id);

        $this->authorize('delete', $transaksi);

        // Jika transaksi masih bisa di-void, redirect ke void action
        if ($transaksi->canBeVoided()) {
            return redirect()->route('vendor.transactions.void', $transaksi->id)
                ->with('warning', 'Disarankan menggunakan fitur Void untuk membatalkan transaksi agar stok dapat dikembalikan.');
        }

        // Jika sudah selesai atau sudah di-void, tidak bisa dihapus
        return FlashMessage::error(redirect()->route('vendor.transactions.show', $transaksi->id),
            'Transaksi ini tidak dapat dihapus. Status: ' . ucfirst($transaksi->status));
    }

    /**
     * Show void confirmation form for a transaction.
     */
    public function void(string $id)
    {
        $vendor = $this->requireVendor();

        if (!$vendor) {
            return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)
            ->with([
                'pelanggan',
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.bahan',
            ])
            ->findOrFail($id);

        // Cek apakah bisa di-void
        $voidService = app(VoidTransactionService::class);
        $voidCheck = $voidService->canBeVoided($transaksi);

        if (!$voidCheck['can_void']) {
            return FlashMessage::error(
                redirect()->route('vendor.transactions.show', $transaksi->id),
                $voidCheck['message']
            );
        }

        // Hitung jumlah yang akan di-refund (jika pembayaran online)
        $refundInfo = null;
        if ($transaksi->terbayar > 0 && in_array(strtolower($transaksi->payment_method ?? ''), ['xendit', 'online', 'qris', 'ewallet', 'bank_transfer'])) {
            $refundInfo = [
                'amount' => $transaksi->terbayar,
                'payment_method' => $transaksi->payment_method,
            ];
        }

        return view('transaksi.void', compact('transaksi', 'refundInfo'));
    }

    /**
     * Process void for a transaction.
     */
    public function confirmVoid(Request $request, string $id)
    {
        $vendor = $this->requireVendor();

        if (!$vendor) {
            return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)->findOrFail($id);

        // Validasi input
        $request->validate([
            'void_reason' => 'required|string|min:5|max:500',
            'reason_type' => 'required|in:customer_request,stock_issue,pricing_error,quality_issue,system_error,other',
        ], [
            'void_reason.required' => 'Alasan void wajib diisi.',
            'void_reason.min' => 'Alasan void minimal 5 karakter.',
            'void_reason.max' => 'Alasan void maksimal 500 karakter.',
            'reason_type.required' => 'Jenis alasan wajib dipilih.',
            'reason_type.in' => 'Jenis alasan tidak valid.',
        ]);

        // Gabungkan jenis alasan dengan teks
        $reasonLabels = [
            'customer_request' => 'Permintaan Pelanggan',
            'stock_issue' => 'Masalah Stok',
            'pricing_error' => 'Kesalahan Harga',
            'quality_issue' => 'Masalah Kualitas',
            'system_error' => 'Kesalahan Sistem',
            'other' => 'Lainnya',
        ];
        $fullReason = '[' . $reasonLabels[$request->reason_type] . '] ' . $request->void_reason;

        // Proses void
        $voidService = app(VoidTransactionService::class);
        $result = $voidService->voidTransaction($transaksi, $fullReason, Auth::user());

        if ($result['success']) {
            $message = 'Transaksi ' . $transaksi->kode . ' berhasil di-void.';
            if ($result['refund_status'] === 'refund_pending') {
                $message .= ' Refund sedang diproses via Xendit.';
            } elseif ($result['refund_status'] === 'refund_failed') {
                $message .= ' Catatan: Refund gagal diproses, silakan refund manual jika diperlukan.';
            }

            return FlashMessage::success(redirect()->route('vendor.transactions.show', $transaksi->id), $message);
        }

        return FlashMessage::backError(redirect()->route('vendor.transactions.void', $transaksi->id), $result['message']);
    }

    /**
     * Generate invoice for transaction
     */
    public function generateInvoice(string $id)
    {
        $vendor = $this->requireVendor();

        if (!$vendor) {
            return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)
            ->with([
                'pelanggan',
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan',
                'vendor'
            ])
            ->findOrFail($id);

        // Prepare logo as base64 data URI
        $logoBase64 = null;

        if ($transaksi->vendor && $transaksi->vendor->logo) {
            $logoPath = public_path('vendors_logo/' . $transaksi->vendor->logo);
            if (file_exists($logoPath)) {
                $logoBase64 = $this->getBase64Image($logoPath);
            }
        }

        // If vendor logo doesn't exist, use default logo
        if (!$logoBase64) {
            $defaultLogoPath = public_path('images/logo.png');
            if (file_exists($defaultLogoPath)) {
                $logoBase64 = $this->getBase64Image($defaultLogoPath);
            }
        }

        // Set PDF options
        $options = [
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'dpi' => 150,
            'fontDir' => storage_path('fonts/'),
            'fontCache' => storage_path('fonts/'),
            'chroot' => public_path(),
        ];

        $pdf = PDF::loadView('transaksi.invoice', compact('transaksi', 'logoBase64'))
            ->setOptions($options)
            ->setPaper([0, 0, 283.46, 283.46], 'portrait');

        return $pdf->stream("invoice-{$transaksi->kode}.pdf");
    }

    /**
     * Convert an image to base64 data URI
     */
    private function getBase64Image($path)
    {
        try {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        } catch (\Exception $e) {
            Log::error('Error converting image to base64: ' . $e->getMessage());
            return null;
        }
    }
}

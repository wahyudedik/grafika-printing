<?php

namespace App\Http\Controllers\Vendor;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Vendor\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Transaksi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Vendor\TransaksiItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Vendor\TransaksiItemSpecifications;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
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
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $pelanggans = Pelanggan::where('vendor_id', $vendor->id)->get();

        if ($pelanggans->isEmpty()) {
            return redirect()->route('pelanggan.create')
                ->with('info', 'Anda belum memiliki pelanggan. Silakan tambahkan pelanggan terlebih dahulu.');
        }

        $produks = Produk::where('vendor_id', $vendor->id)
            ->with(['spesifikasiProduk.spesifikasi', 'spesifikasiProduk.bahanSpesifikasiProduk'])
            ->get();

        if ($produks->isEmpty()) {
            return redirect()->route('produk.create')
                ->with('info', 'Anda belum memiliki produk. Silakan tambahkan produk terlebih dahulu.');
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
    public function store(Request $request)
    {
        // Get current vendor from session
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        // Validate main transaction data
        $validator = Validator::make($request->all(), [
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'payment_method' => 'required|string',
            'estimasi_selesai' => 'required|date',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.kuantitas' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Generate transaction code
            $transactionCode = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // Calculate total price
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $totalPrice += $item['kuantitas'] * $item['harga_satuan'];
            }

            // Create transaction
            $transaksi = Transaksi::create([
                'vendor_id' => $vendor->id,
                'kode' => $transactionCode,
                'user_id' => Auth::id(),
                'pelanggan_id' => $request->pelanggan_id,
                'total_harga' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'estimasi_selesai' => $request->estimasi_selesai,
                'tanggal_dibuat' => now(),
                'progress_percentage' => 0,
                'catatan' => $request->catatan
            ]);

            // Process transaction items
            foreach ($request->items as $itemData) {
                $item = TransaksiItem::create([
                    'vendor_id' => $vendor->id,
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $itemData['produk_id'],
                    'kuantitas' => $itemData['kuantitas'],
                    'harga_satuan' => $itemData['harga_satuan']
                ]);

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

            DB::commit();

            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil dibuat dengan kode: ' . $transactionCode);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
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

        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
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
    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)->findOrFail($id);

        // Validate main transaction data
        $validator = Validator::make($request->all(), [
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'payment_method' => 'required|string',
            'estimasi_selesai' => 'required|date',
            'status' => 'required|string|in:pending,processing,quality_check,completed,cancelled',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:transaksi_items,id',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.kuantitas' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Begin transaction
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

            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = Auth::user()->vendorUser->first();
        
        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        $transaksi = Transaksi::where('vendor_id', $vendor->id)->findOrFail($id);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Delete related records
            // First delete specifications
            TransaksiItemSpecifications::whereIn(
                'transaksi_item_id',
                $transaksi->transaksiItem->pluck('id')->toArray()
            )->delete();

            // Delete transaction items
            TransaksiItem::where('transaksi_id', $transaksi->id)->delete();

            // Delete the transaction
            $transaksi->delete();

            DB::commit();

            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Batch delete transactions
     */
    public function batchDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:transaksis,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Data transaksi tidak valid.');
        }

        $vendor = session('current_vendor');

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Get transactions for this vendor
            $transaksis = Transaksi::where('vendor_id', $vendor->id)
                ->whereIn('id', $request->ids)
                ->get();

            // Delete related records
            foreach ($transaksis as $transaksi) {
                // First delete specifications
                TransaksiItemSpecifications::whereIn(
                    'transaksi_item_id',
                    $transaksi->transaksiItem->pluck('id')->toArray()
                )->delete();

                // Delete transaction items
                TransaksiItem::where('transaksi_id', $transaksi->id)->delete();

                // Delete the transaction
                $transaksi->delete();
            }

            DB::commit();

            return redirect()->route('transaksi.index')
                ->with('success', count($transaksis) . ' transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Batch update transaction status
     */
    public function batchUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:transaksis,id',
            'action' => 'required|string|in:status',
            'status' => 'required_if:action,status|string|in:pending,processing,quality_check,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        $vendor = session('current_vendor');

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Get transactions for this vendor
            $transaksis = Transaksi::where('vendor_id', $vendor->id)
                ->whereIn('id', $request->ids)
                ->get();

            // Update status for each transaction
            $progressMap = [
                'pending' => 0,
                'processing' => 25,
                'quality_check' => 80,
                'completed' => 100,
                'cancelled' => 0
            ];

            foreach ($transaksis as $transaksi) {
                $transaksi->update([
                    'status' => $request->status,
                    'progress_percentage' => $progressMap[$request->status]
                ]);
            }

            DB::commit();

            return redirect()->route('transaksi.index')
                ->with('success', 'Status ' . count($transaksis) . ' transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate invoice for transaction
     */
    public function generateInvoice(string $id)
    {
        $vendor = Vendor::findOrFail(session('current_vendor_id'));

        if (!$vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor tidak ditemukan. Silakan pilih vendor terlebih dahulu.');
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

        $pdf = PDF::loadView('transaksi.invoice', compact('transaksi'));
        return $pdf->stream("invoice-{$transaksi->kode}.pdf");
    }
}

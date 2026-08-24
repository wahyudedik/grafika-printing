<?php

namespace App\Http\Controllers\vendor\pos;

use App\Http\Responses\FlashMessage;

use Carbon\Carbon;
use App\Models\Vendor\Bahan;
use Illuminate\Http\Request;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Transaksi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Vendor\EstimasiProduk;
use App\Http\Concerns\HasVendorContext;
use App\Services\StockService;
use App\Services\PriceCalculationService;
use App\Services\DiscountService;
use App\Notifications\VendorNewOrderNotification;
use App\Facades\Tenant;



class CheckoutController extends Controller
{
    use HasVendorContext;

    protected StockService $stockService;
    protected PriceCalculationService $priceCalcService;
    protected DiscountService $discountService;

    public function __construct(StockService $stockService, PriceCalculationService $priceCalcService, DiscountService $discountService)
    {
        $this->stockService = $stockService;
        $this->priceCalcService = $priceCalcService;
        $this->discountService = $discountService;
    }

    public function processCheckout(Request $request)
    {
        // Dapatkan vendor dari user yang sedang login
        $vendor = $this->requireVendor();

        $validatedData = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'payment_method' => 'required|in:cash,transfer,qris',
            'payment_amount' => 'required_if:payment_method,cash|nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            $cartItems = session('cart', []);

            if (empty($cartItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty. Please add items before checkout.'
                ], 400);
            }

            // Validate stock using StockService before creating transaction
            try {
                $this->stockService->validateStock($cartItems);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            // Batch load all products to avoid N+1 queries
            $productIds = collect($cartItems)->pluck('product_id')->unique();
            $products = Produk::with('estimasiProduk.alat')
                ->whereIn('id', $productIds)->keyBy('id');

            $totalTime = collect($cartItems)->sum(function ($item) use ($products) {
                $product = $products->get($item['product_id']);
                return $product ? $product->getEstimatedProductionTime($item['quantity']) : 0;
            });

            $latestPending = Transaksi::where('status', 'pending')
                ->where('vendor_id', $vendor->id)
                ->latest('estimasi_selesai')
                ->first();

            $startTime = $latestPending ? Carbon::parse($latestPending->estimasi_selesai) : now();
            $estimatedCompletion = $startTime->addMinutes($totalTime);

            // Hitung total harga menggunakan PriceCalculationService
            $cartSummary = $this->priceCalcService->calculateCartTotal($cartItems);
            $totalAmount = $cartSummary['subtotal'];
            $totalHpp = $cartSummary['hpp_total'];
            $totalLaba = $cartSummary['profit'];

            // Apply coupon discount if provided
            $discountData = null;
            $discountAmount = 0;
            if (!empty($validatedData['coupon_code'])) {
                $discountData = $this->discountService->validateCoupon($validatedData['coupon_code'], $totalAmount);
                if ($discountData['valid']) {
                    $discountAmount = $discountData['discount_amount'];
                }
            }

            // Total setelah diskon
            $totalAfterDiscount = max(0, $totalAmount - $discountAmount);

            // Hitung jumlah terbayar dan kembalian
            $paymentAmount = $validatedData['payment_method'] === 'cash' && isset($validatedData['payment_amount'])
                ? (float) $validatedData['payment_amount']
                : $totalAfterDiscount;

            $changeAmount = max(0, $paymentAmount - $totalAfterDiscount);

            // Generate unique transaction code: TRX-{Ymd}-{vendor_id}-{sequence}
            $todayCount = Transaksi::where('vendor_id', $vendor->id)
                ->whereDate('created_at', today())
                ->count();
            $sequence = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
            $transactionCode = 'TRX-' . date('Ymd') . '-' . $vendor->id . '-' . $sequence;

            $transaksi = Transaksi::create([
                'vendor_id' => $vendor->id,
                'kode' => $transactionCode,
                'user_id' => Auth::id(),
                'pelanggan_id' => $validatedData['pelanggan_id'],
                'total_harga' => $totalAfterDiscount,
                'diskon_total' => $discountAmount,
                'total_sebelum_diskon' => $totalAmount,
                'hpp_total' => $totalHpp,
                'laba_total' => $totalLaba - $discountAmount,
                'terbayar' => $paymentAmount,
                'kembali' => $changeAmount,
                'status' => 'pending',
                'payment_method' => $validatedData['payment_method'],
                'estimasi_selesai' => $estimatedCompletion,
                'tanggal_dibuat' => now(),
                'progress_percentage' => 0,
                'catatan' => $validatedData['catatan']
            ]);

            // Record discount to transaksi_discounts table
            if ($discountData && $discountData['valid'] && $discountAmount > 0) {
                $this->discountService->applyDiscountToTransaction(
                    $transaksi,
                    $discountData,
                    Auth::user()
                );
            }

            foreach ($cartItems as $item) {
                // Bug 4 Fix: Validasi quantity untuk mencegah division by zero
                if ($item['quantity'] <= 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Jumlah item tidak valid (quantity harus lebih dari 0).'
                    ], 400);
                }

                // Hitung HPP per item dari cart specs (single Bahan lookup per unique bahan)
                // Menggunakan data yang sama dengan calculateCartTotal() untuk menghindari duplikasi
                $hppTotalItem = 0;
                $bahanCache = [];
                foreach ($item['specifications'] as $spec) {
                    $bahanId = $spec['bahan_id'] ?? null;
                    if ($bahanId && !isset($bahanCache[$bahanId])) {
                        $bahanCache[$bahanId] = \App\Models\Vendor\Bahan::find($bahanId);
                    }
                    $bahan = $bahanCache[$bahanId] ?? null;
                    if ($bahan) {
                        $hppTotalItem += ($spec['input_type'] ?? '') === 'number'
                            ? (float) $bahan->hpp * (float) ($spec['value'] ?? 0) * $item['quantity']
                            : (float) $bahan->hpp * $item['quantity'];
                    }
                }

                $hargaSatuan = $item['quantity'] > 0 ? $item['total_price'] / $item['quantity'] : 0;
                $labaItem = ($item['total_price']) - $hppTotalItem;

                $transaksiItem = $transaksi->transaksiItem()->create([
                    'vendor_id' => $vendor->id,
                    'produk_id' => $item['product_id'],
                    'kuantitas' => $item['quantity'],
                    'harga_satuan' => $hargaSatuan,
                    'hpp_satuan' => $item['quantity'] > 0 ? $hppTotalItem / $item['quantity'] : 0,
                    'hpp_total' => $hppTotalItem,
                    'laba' => $labaItem,
                ]);

                // Buat spesifikasi item menggunakan bahan yang sudah di-cache (tanpa query ulang)
                foreach ($item['specifications'] as $specId => $spec) {
                    $bahanId = $spec['bahan_id'] ?? null;
                    $bahan = $bahanCache[$bahanId] ?? null;
                    $hppPrice = 0;
                    if ($bahan) {
                        $hppPrice = ($spec['input_type'] ?? '') === 'number'
                            ? (float) $bahan->hpp * (float) ($spec['value'] ?? 0) * $item['quantity']
                            : (float) $bahan->hpp * $item['quantity'];
                    }

                    $transaksiItem->transaksiItemSpecifications()->create([
                        'vendor_id' => $vendor->id,
                        'spesifikasi_produk_id' => $specId,
                        'bahan_id' => $spec['bahan_id'],
                        'value' => $spec['value'],
                        'input_type' => $spec['input_type'],
                        'price' => $spec['price'],
                        'hpp_price' => $hppPrice,
                    ]);
                }
            }

            // Decrement stock via StockService after transaction items are created
            $this->stockService->decrementStock($transaksi);

            // Update customer's last transaction timestamp
            $pelanggan = Pelanggan::find($validatedData['pelanggan_id']);
            if ($pelanggan) {
                $pelanggan->update([
                    'transaksi_terakhir' => now()
                ]);
            }

            DB::commit();
            session()->forget('cart');
            session()->forget('applied_coupon');

            // Send notification to vendor for cash payments (online payments get notified via webhook)
            if ($validatedData['payment_method'] === 'cash') {
                $vendorObj = Tenant::getVendorId() ? \App\Models\Vendor::find(Tenant::getVendorId()) : null;
                if ($vendorObj) {
                    $vendorObj->notify(new VendorNewOrderNotification($transaksi));
                }
            }

            // Check low stock after order
            $lowStockItems = $this->stockService->checkLowStock($vendor->id);
            if ($lowStockItems->isNotEmpty()) {
                $this->stockService->notifyLowStock($vendor, $lowStockItems);
            }

            // Redirect to payment options instead of invoice
            return response()->json([
                'success' => true,
                'paymentUrl' => route('vendor.pos.payment.options', [
                    'transaksi' => $transaksi->id
                ]),
                'invoiceUrl' => route('vendor.pos.invoice', [
                    'transaksi' => $transaksi->id
                ]),
                'downloadUrl' => route('vendor.pos.invoice.download', [
                    'transaksi' => $transaksi->id
                ]),
                'redirectUrl' => route('vendor.pos.payment.options', $transaksi->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show()
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = $this->requireVendor();
            $cartItems = session('cart', []);

            if (empty($cartItems)) {
                return FlashMessage::error(redirect()->route('vendor.pos.cart'), 'Your cart is empty. Please add items before checkout.');
            }

            $totalAmount = collect($cartItems)->sum('total_price');
            // Batch load all products to avoid N+1 queries
            $productIds = collect($cartItems)->pluck('product_id')->unique();
            $products = Produk::with('estimasiProduk.alat')
                ->whereIn('id', $productIds)->keyBy('id');

            $totalTime = collect($cartItems)->sum(function ($item) use ($products) {
                $product = $products->get($item['product_id']);
                return $product ? $product->getEstimatedProductionTime($item['quantity']) : 0;
            });

            $customers = Pelanggan::where('vendor_id', $vendor->id)->get();
            $products = Produk::where('vendor_id', $vendor->id)->get();

            return view('pos.checkout', compact(
                'cartItems',
                'totalAmount',
                'totalTime',
                'customers',
                'products'
            ));
        } catch (\Exception $e) {
            Log::error('Error displaying checkout: ' . $e->getMessage());
            return FlashMessage::error(redirect()->route('vendor.pos.cart'), 'Failed to load checkout page: ' . $e->getMessage());
        }
    }

    public function createCustomer(Request $request)
    {
        // Dapatkan vendor dari user yang sedang login
        $vendor = $this->requireVendor();

        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
                'no_telp' => 'required|string',
                'email' => 'nullable|email'
            ]);

            $customer = Pelanggan::create([
                'vendor_id' => $vendor->id,
                'kode' => 'PLG-' . date('YmdHis'),
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'no_telp' => $validated['no_telp'],
                'email' => $validated['email']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'customer' => $customer
                ]);
            }

            return FlashMessage::backSuccess('Customer created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating customer: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create customer: ' . $e->getMessage()
                ], 500);
            }

            return FlashMessage::backError('Failed to create customer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * AJAX: Validate and apply coupon code
     */
    public function applyCoupon(Request $request)
    {
        $vendor = $this->requireVendor();

        $validated = $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $cartItems = session('cart', []);
        if (empty($cartItems)) {
            return response()->json([
                'valid' => false,
                'message' => 'Keranjang kosong. Tambahkan item terlebih dahulu.',
            ], 400);
        }

        $cartSummary = $this->priceCalcService->calculateCartTotal($cartItems);
        $subtotal = $cartSummary['subtotal'];

        $result = $this->discountService->validateCoupon($validated['coupon_code'], $subtotal);

        if ($result['valid']) {
            session(['applied_coupon' => [
                'code' => $result['coupon']->code,
                'name' => $result['coupon']->name,
                'type' => $result['coupon']->type,
                'value' => $result['coupon']->value,
                'discount_amount' => $result['discount_amount'],
            ]]);
        }

        return response()->json($result);
    }

    /**
     * Remove applied coupon from session
     */
    public function removeCoupon()
    {
        session()->forget('applied_coupon');
        return response()->json(['success' => true, 'message' => 'Kupon berhasil dihapus.']);
    }

    private function calculateEstimatedCompletion($cartItems)
    {
        $equipmentSchedule = [];
        $maxCompletionTime = now(); // Start with current time as Carbon instance

        // Batch load all estimasi to avoid N+1 queries
        $estimasiMap = EstimasiProduk::whereIn('produk_id', collect($cartItems)->pluck('product_id')->unique())
            ->get()->keyBy('produk_id');

        foreach ($cartItems as $item) {
            $estimasiProduk = $estimasiMap->get($item['product_id']);
            if (!$estimasiProduk) continue;

            $alat = $estimasiProduk->alat;
            if (!$alat) continue;

            $productionTime = $estimasiProduk->calculateTotalProductionTime($item['quantity']);

            if (!isset($equipmentSchedule[$alat->id])) {
                $equipmentSchedule[$alat->id] = $alat->getNextAvailableSlot();
            }

            $startTime = $equipmentSchedule[$alat->id];
            $equipmentSchedule[$alat->id] = Carbon::parse($startTime)->addMinutes($productionTime);

            // Update max completion time
            if ($equipmentSchedule[$alat->id]->gt($maxCompletionTime)) {
                $maxCompletionTime = $equipmentSchedule[$alat->id];
            }
        }

        return $maxCompletionTime;
    }
}

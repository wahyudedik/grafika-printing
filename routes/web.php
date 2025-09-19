<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\vendor\AlatController;
use App\Http\Controllers\vendor\BahanController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\vendor\ProdukController;
use App\Http\Controllers\vendor\LaporanController;
use App\Http\Controllers\vendor\pos\PosController;
use App\Http\Controllers\vendor\PenggunaController;
use App\Http\Controllers\vendor\PelangganController;
use App\Http\Controllers\vendor\TransaksiController;
use App\Http\Controllers\vendor\pos\InvoiceController;
use App\Http\Controllers\vendor\SpesifikasiController;
use App\Http\Controllers\vendor\pos\CheckoutController;
use App\Http\Controllers\vendor\KategoriProdukController;

Route::get('/', function () {
    $auctions = \App\Models\Auction::with(['user'])
        ->where('status', 'active')
        ->where('deadline', '>', now())
        ->orderBy('created_at', 'desc')
        ->limit(6)
        ->get();

    return view('welcome', compact('auctions'));
})->name('welcome');


Route::middleware(['auth', 'verified', 'dev'])->group(function () {
    Route::get('/administrator', [UserDashboardController::class, 'devDashboard'])
        ->name('dev.dashboard');

    // users routes resource
    Route::resource('/administrator/users', UserController::class);

    // vendors routes resource
    Route::resource('/administrator/vendors', VendorController::class);

    // Auction management routes for admin
    Route::prefix('/administrator/auctions')->name('admin.auctions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'statistics'])->name('statistics');
        Route::get('/{auction}', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'show'])->name('show');
        Route::get('/{auction}/edit', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'edit'])->name('edit');
        Route::put('/{auction}', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'update'])->name('update');
        Route::get('/{auction}/bids', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'bids'])->name('bids');
        Route::post('/{auction}/approve', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'approve'])->name('approve');
        Route::post('/{auction}/reject', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'reject'])->name('reject');
        Route::post('/{auction}/close', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'close'])->name('close');
        Route::delete('/{auction}', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'destroy'])->name('destroy');
    });

    // Withdrawal management routes for admin
    Route::prefix('/administrator/withdrawals')->name('admin.withdrawals.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'statistics'])->name('statistics');
        Route::get('/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/approve', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'approve'])->name('approve');
        Route::post('/{withdrawal}/reject', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'reject'])->name('reject');
        Route::post('/{withdrawal}/complete', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'complete'])->name('complete');
    });

    // Pulse monitoring routes for admin
    Route::prefix('/administrator/pulse')->name('admin.pulse.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PulseController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\PulseController::class, 'statistics'])->name('statistics');
        Route::get('/performance', [\App\Http\Controllers\Admin\PulseController::class, 'performance'])->name('performance');
        Route::get('/activity', [\App\Http\Controllers\Admin\PulseController::class, 'activity'])->name('activity');
    });

    // Vendor revenue routes for admin
    Route::prefix('/administrator/vendor-revenue')->name('admin.vendor-revenue.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'index'])->name('index');
        Route::get('/{vendor}', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'show'])->name('show');
        Route::get('/api/statistics', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'statistics'])->name('statistics');
        Route::get('/api/monthly-data', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'monthlyData'])->name('monthly-data');
        Route::get('/api/vendor/{vendor}', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'vendorData'])->name('vendor-data');
    });
});

Route::middleware(['auth', 'verified', 'vendor', 'tenants'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'vendorDashboard'])
        ->name('dashboard');

    // vendor users routes resource
    Route::resource('/dashboard/pengguna', PenggunaController::class);

    // Alat routes resource
    Route::resource('/dashboard/alat', AlatController::class);

    // Bahan routes resource
    Route::resource('/dashboard/bahan', BahanController::class);
    Route::delete('/dashboard/bahan/wholesale-price/{id}', [BahanController::class, 'deleteWholesalePrice'])
        ->name('bahan.wholesale-price.delete');

    // Pelanggan routes resource
    Route::resource('/dashboard/pelanggan', PelangganController::class);

    // Spesifikasi input type bahan produk routes resource
    Route::resource('/dashboard/spesifikasi', SpesifikasiController::class);

    // Produk routes resource
    Route::resource('/dashboard/produk', ProdukController::class);

    // Kategori Produk routes resource
    Route::resource('/dashboard/kategori-produk', KategoriProdukController::class);

    // Transaksi routes resource
    Route::resource('/dashboard/transaksi', TransaksiController::class);
    Route::get('/dashboard/transaksi/{id}/invoice', [TransaksiController::class, 'generateInvoice'])
        ->name('transaksi.generateInvoice');

    // routes Laporan
    Route::get('/dashboard/penjualan-harian', [LaporanController::class, 'penjualanHarian'])
        ->name('laporan.penjualan-harian');
    Route::get('/dashboard/penjualan-bulanan', [LaporanController::class, 'penjualanBulanan'])
        ->name('laporan.penjualan-bulanan');
    Route::get('/dashboard/penjualan-tahunan', [LaporanController::class, 'penjualanTahunan'])
        ->name('laporan.penjualan-tahunan');
    Route::get('/dashboard/export/penjualan', [LaporanController::class, 'exportPenjualan'])
        ->name('laporan.export-penjualan');

    // pos routes
    Route::prefix('/dashboard/pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/category/{slug}', [PosController::class, 'category'])->name('category');
        Route::get('/search', [PosController::class, 'search'])->name('search');
        Route::get('/cart', [PosController::class, 'cart'])->name('cart');
        Route::post('/add-to-cart', [PosController::class, 'addToCart'])->name('addToCart');
        Route::get('/cart/remove/{index}', [PosController::class, 'removeItem'])->name('removeItem');
        Route::get('/cart/clear', [PosController::class, 'clearCart'])->name('clearCart');
        Route::post('/check-price', [PosController::class, 'checkPrice'])->name('checkPrice');
        Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
        Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::post('/customer/create', [CheckoutController::class, 'createCustomer'])->name('customer.create');
        Route::get('/invoice/{transaksi}', [InvoiceController::class, 'show'])->name('invoice.show');
        Route::get('/invoice/{transaksi}/download', [InvoiceController::class, 'download'])->name('invoice.download');
    });

    // profile route
    Route::get('/dashboard/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/dashboard/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/dashboard/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Auction bid routes for vendor
    Route::prefix('/dashboard/auctions')->name('vendor.auctions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\vendor\AuctionBidController::class, 'index'])->name('index');
        Route::get('/my-bids', [\App\Http\Controllers\vendor\AuctionBidController::class, 'myBids'])->name('my-bids');
        Route::get('/{auction}', [\App\Http\Controllers\vendor\AuctionBidController::class, 'show'])->name('show');
        Route::get('/{auction}/bid', [\App\Http\Controllers\vendor\AuctionBidController::class, 'create'])->name('bid');
        Route::post('/{auction}/bid', [\App\Http\Controllers\vendor\AuctionBidController::class, 'store'])->name('store-bid');
        Route::get('/bids/{bid}/edit', [\App\Http\Controllers\vendor\AuctionBidController::class, 'edit'])->name('edit-bid');
        Route::put('/bids/{bid}', [\App\Http\Controllers\vendor\AuctionBidController::class, 'update'])->name('update-bid');
        Route::delete('/bids/{bid}', [\App\Http\Controllers\vendor\AuctionBidController::class, 'destroy'])->name('destroy-bid');
    });

    // Order tracking routes for vendor
    Route::prefix('/dashboard/tracking')->name('vendor.tracking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderTrackingController::class, 'vendorIndex'])->name('index');
        Route::put('/{transaksi}', [\App\Http\Controllers\OrderTrackingController::class, 'updateStatus'])->name('update');
    });

    // Wallet routes for vendor
    Route::prefix('/dashboard/wallet')->name('vendor.wallet.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VendorWalletController::class, 'index'])->name('index');
        Route::get('/transactions', [\App\Http\Controllers\VendorWalletController::class, 'transactions'])->name('transactions');
        Route::get('/withdrawals', [\App\Http\Controllers\VendorWalletController::class, 'withdrawals'])->name('withdrawals');
        Route::get('/withdrawals/create', [\App\Http\Controllers\VendorWalletController::class, 'createWithdrawal'])->name('create-withdrawal');
        Route::post('/withdrawals', [\App\Http\Controllers\VendorWalletController::class, 'storeWithdrawal'])->name('store-withdrawal');
        Route::get('/withdrawals/{withdrawal}', [\App\Http\Controllers\VendorWalletController::class, 'showWithdrawal'])->name('show-withdrawal');
        Route::post('/withdrawals/{withdrawal}/cancel', [\App\Http\Controllers\VendorWalletController::class, 'cancelWithdrawal'])->name('cancel-withdrawal');
    });
});

Route::middleware(['auth', 'verified', 'user'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'userDashboard'])
        ->name('user.dashboard');

    // Profile routes for user
    Route::get('/user/profile', [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::patch('/user/profile', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::delete('/user/profile', [ProfileController::class, 'destroy'])->name('user.profile.destroy');

    // Auction routes for user
    Route::resource('auctions', \App\Http\Controllers\AuctionController::class);
    Route::get('/user/auctions', [\App\Http\Controllers\AuctionController::class, 'myAuctions'])->name('auctions.my');
    Route::post('/auctions/{auction}/close', [\App\Http\Controllers\AuctionController::class, 'closeAuction'])->name('auctions.close');

    // Order tracking routes for user
    Route::prefix('/user/tracking')->name('user.tracking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderTrackingController::class, 'index'])->name('index');
        Route::get('/{auction}', [\App\Http\Controllers\OrderTrackingController::class, 'show'])->name('show');
    });

    // Shipping invoice routes for user
    Route::prefix('/shipping')->name('shipping.')->group(function () {
        Route::post('/invoice/{transaksi}', [\App\Http\Controllers\ShippingInvoiceController::class, 'generateShippingInvoice'])->name('generate-invoice');
        Route::post('/payment/{transaksi}', [\App\Http\Controllers\ShippingInvoiceController::class, 'handleCODPayment'])->name('handle-payment');
        Route::post('/calculate', [\App\Http\Controllers\ShippingInvoiceController::class, 'calculateShippingCost'])->name('calculate-cost');
    });

    // Shipping calculator routes for vendor
    Route::prefix('/vendor/shipping')->name('vendor.shipping.')->group(function () {
        Route::get('/calculator', [\App\Http\Controllers\ShippingCalculatorController::class, 'index'])->name('calculator');
        Route::post('/calculate', [\App\Http\Controllers\ShippingCalculatorController::class, 'calculate'])->name('calculate');
        Route::get('/couriers', [\App\Http\Controllers\ShippingCalculatorController::class, 'getCouriers'])->name('couriers');
        Route::post('/service-types', [\App\Http\Controllers\ShippingCalculatorController::class, 'getServiceTypes'])->name('service-types');
        Route::post('/save/{transaksi}', [\App\Http\Controllers\ShippingCalculatorController::class, 'saveShipping'])->name('save');
    });

    // Vendor rating routes for user
    Route::prefix('/vendor/ratings')->name('vendor.ratings.')->group(function () {
        Route::get('/{auction}', [\App\Http\Controllers\VendorRatingController::class, 'create'])->name('create');
        Route::post('/{auction}', [\App\Http\Controllers\VendorRatingController::class, 'store'])->name('store');
    });
});

// API routes for RajaOngkir integration
Route::prefix('/api')->group(function () {
    Route::post('/calculate-shipping', [\App\Http\Controllers\OrderTrackingController::class, 'calculateShipping'])->name('api.calculate-shipping');
    Route::post('/track-shipment', [\App\Http\Controllers\OrderTrackingController::class, 'trackShipment'])->name('api.track-shipment');

    // Enhanced shipping calculator API
    Route::post('/shipping/calculate', [\App\Http\Controllers\ShippingCalculatorController::class, 'calculate'])->name('api.shipping.calculate');
    Route::get('/shipping/couriers', [\App\Http\Controllers\ShippingCalculatorController::class, 'getCouriers'])->name('api.shipping.couriers');
    Route::post('/shipping/service-types', [\App\Http\Controllers\ShippingCalculatorController::class, 'getServiceTypes'])->name('api.shipping.service-types');

    // Vendor withdrawal API
    Route::post('/withdrawal/calculate-fee', [\App\Http\Controllers\VendorWithdrawalController::class, 'calculateFee'])->name('api.withdrawal.calculate-fee');

    // Xendit API routes for testing
    Route::get('/xendit/test', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Xendit API is working',
            'config' => [
                'api_key_set' => !empty(config('services.xendit.api_key')),
                'webhook_token_set' => !empty(config('services.xendit.webhook_token')),
                'base_url' => config('services.xendit.base_url')
            ]
        ]);
    })->name('api.xendit.test');

    // Debug route untuk test payment creation
    Route::post('/xendit/debug/payment', function (Request $request) {
        try {
            $xenditService = app(\App\Services\XenditService::class);

            $testData = [
                'external_id' => 'test_' . time(),
                'amount' => 10000,
                'description' => 'Test Payment',
                'customer' => [
                    'given_names' => 'Test User',
                    'email' => 'test@example.com'
                ],
                'success_redirect_url' => 'https://grafika.noteds.com',
                'failure_redirect_url' => 'https://grafika.noteds.com'
            ];

            $result = $xenditService->createPaymentLink($testData);

            return response()->json([
                'success' => true,
                'result' => $result,
                'test_data' => $testData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('xendit.debug.payment');

    // Debug route untuk test auction close
    Route::post('/debug/auction/{auction}/close', function (Request $request, $auction) {
        try {
            $auction = \App\Models\Auction::findOrFail($auction);

            return response()->json([
                'success' => true,
                'auction' => [
                    'id' => $auction->id,
                    'status' => $auction->status,
                    'user_id' => $auction->user_id,
                    'auth_id' => Auth::id(),
                    'bids_count' => $auction->bids->count(),
                    'pending_bids' => $auction->bids->where('status', 'pending')->count()
                ],
                'redirect_url' => route('xendit.payment.show-page', ['auction' => $auction->id])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    })->name('debug.auction.close');

    // Debug route untuk test webhook
    Route::post('/debug/webhook', function (Request $request) {
        try {
            $webhookController = app(\App\Http\Controllers\XenditWebhookController::class);
            $result = $webhookController->handleWebhook($request);

            return response()->json([
                'success' => true,
                'webhook_response' => $result->getContent(),
                'status_code' => $result->getStatusCode(),
                'headers' => $result->headers->all()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('debug.webhook');

    // Debug route untuk test payment creation
    Route::post('/debug/payment/{auction}', function (Request $request, $auction) {
        try {
            $auction = \App\Models\Auction::findOrFail($auction);

            return response()->json([
                'success' => true,
                'auction' => [
                    'id' => $auction->id,
                    'title' => $auction->title,
                    'status' => $auction->status,
                    'winning_bid' => $auction->winning_bid,
                    'user_id' => $auction->user_id,
                    'auth_user_id' => Auth::id(),
                    'can_access' => $auction->user_id === Auth::id() && $auction->status === 'waiting_payment'
                ],
                'payment_route' => route('xendit.payment.create', $auction->id),
                'xendit_config' => [
                    'api_key_set' => !empty(config('services.xendit.api_key')),
                    'base_url' => config('services.xendit.base_url'),
                    'redirect_url' => config('services.xendit.redirect_url')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('debug.payment');

    // Simple test route untuk debug JavaScript
    Route::get('/debug/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'Test route working',
            'timestamp' => now(),
            'user' => Auth::user() ? Auth::user()->name : 'Not authenticated'
        ]);
    })->name('debug.test');
});

// Xendit Webhook route (no auth required, skip CSRF)
Route::post('/xendit/webhook', [\App\Http\Controllers\XenditWebhookController::class, 'handleWebhook'])
    ->middleware([\App\Http\Middleware\XenditWebhookMiddleware::class])
    ->name('xendit.webhook');

// Test webhook endpoint
Route::get('/xendit/webhook/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Xendit webhook endpoint is accessible',
        'url' => route('xendit.webhook'),
        'timestamp' => now()
    ]);
})->name('xendit.webhook.test');

// Xendit Payment routes (moved to API routes)
// Payment page route (still in web for view rendering)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/xendit/auctions/{auction}/payment', [\App\Http\Controllers\XenditPaymentController::class, 'showPaymentPage'])->name('xendit.payment.show-page');
    Route::post('/xendit/auctions/{auction}/payment', [\App\Http\Controllers\XenditPaymentController::class, 'createPaymentLink'])->name('xendit.payment.create');
});

// Public vendor profile route
Route::get('/vendor/{vendor}', [\App\Http\Controllers\VendorRatingController::class, 'show'])->name('vendor.profile');

// Vendor withdrawal routes
Route::middleware(['auth', 'vendor'])->prefix('/vendor/withdrawal')->name('vendor.withdrawal.')->group(function () {
    Route::get('/', [\App\Http\Controllers\VendorWithdrawalController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\VendorWithdrawalController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\VendorWithdrawalController::class, 'store'])->name('store');
    Route::get('/{withdrawal}', [\App\Http\Controllers\VendorWithdrawalController::class, 'show'])->name('show');
    Route::post('/{withdrawal}/cancel', [\App\Http\Controllers\VendorWithdrawalController::class, 'cancel'])->name('cancel');
    Route::get('/history', [\App\Http\Controllers\VendorWithdrawalController::class, 'history'])->name('history');
});

// Admin withdrawal management routes
Route::middleware(['auth', 'admin'])->prefix('/admin/withdrawal')->name('admin.withdrawal.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'index'])->name('index');
    Route::get('/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'show'])->name('show');
    Route::post('/{withdrawal}/approve', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'approve'])->name('approve');
    Route::post('/{withdrawal}/reject', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'reject'])->name('reject');
    Route::post('/{withdrawal}/complete', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'complete'])->name('complete');
    Route::post('/bulk-approve', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'bulkApprove'])->name('bulk-approve');
    Route::get('/statistics', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'statistics'])->name('statistics');
});

// Pulse dashboard route (public access for embedded iframe)
Route::get('/pulse/dashboard', function () {
    return view('vendor.pulse.dashboard');
})->name('pulse.dashboard');

// Vendor Bank Account Management Routes
Route::middleware(['auth', 'vendor'])->prefix('/vendor/bank-accounts')->name('vendor.bank-accounts.')->group(function () {
    Route::get('/', [\App\Http\Controllers\VendorBankAccountController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\VendorBankAccountController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\VendorBankAccountController::class, 'store'])->name('store');
    Route::get('/{type}/edit', [\App\Http\Controllers\VendorBankAccountController::class, 'edit'])->name('edit');
    Route::put('/{type}', [\App\Http\Controllers\VendorBankAccountController::class, 'update'])->name('update');
    Route::delete('/{type}', [\App\Http\Controllers\VendorBankAccountController::class, 'destroy'])->name('destroy');
});

// API Routes for Bank Account Management
Route::prefix('/api/vendor')->middleware(['auth', 'vendor'])->group(function () {
    Route::get('/banks', [\App\Http\Controllers\VendorBankAccountController::class, 'getBanks'])->name('api.vendor.banks');
    Route::get('/ewallet-providers', [\App\Http\Controllers\VendorBankAccountController::class, 'getEwalletProviders'])->name('api.vendor.ewallet-providers');
    Route::get('/account-details', [\App\Http\Controllers\VendorBankAccountController::class, 'getAccountDetails'])->name('api.vendor.account-details');
});

// CMS Management Routes (Admin/Dev only)
Route::prefix('/admin/cms')->middleware(['auth', 'dev'])->name('admin.cms.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\CmsController::class, 'index'])->name('index');
    Route::get('/{category}', [\App\Http\Controllers\Admin\CmsController::class, 'show'])->name('show');
    Route::post('/', [\App\Http\Controllers\Admin\CmsController::class, 'store'])->name('store');
    Route::put('/', [\App\Http\Controllers\Admin\CmsController::class, 'update'])->name('update');
    Route::put('/setting/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'updateSetting'])->name('update-setting');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'destroy'])->name('destroy');
    Route::post('/toggle/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'toggle'])->name('toggle');
    Route::post('/upload-image', [\App\Http\Controllers\Admin\CmsController::class, 'uploadImage'])->name('upload-image');
    Route::get('/api/settings/{category?}', [\App\Http\Controllers\Admin\CmsController::class, 'getSettings'])->name('api.settings');
});

require __DIR__ . '/auth.php';

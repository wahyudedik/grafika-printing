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

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::get('/', function () {
    $auctions = \App\Models\Auction::with(['user'])
        ->where('status', 'active')
        ->where('deadline', '>', now())
        ->orderBy('created_at', 'desc')
        ->limit(6)
        ->get();

    return view('welcome', compact('auctions'));
})->name('welcome');

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================

require __DIR__ . '/auth.php';

// ============================================================================
// ADMIN/DEV ROUTES (Super Admin)
// ============================================================================

Route::middleware(['auth', 'verified', 'dev'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [UserDashboardController::class, 'devDashboard'])->name('dashboard');

    // User Management
    Route::resource('users', UserController::class);

    // Vendor Management  
    Route::resource('vendors', VendorController::class);

    // Auction Management
    Route::prefix('auctions')->name('auctions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'statistics'])->name('statistics');
        Route::get('/{auction}', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'show'])->name('show');
        Route::get('/{auction}/edit', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'edit'])->name('edit');
        Route::put('/{auction}', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'update'])->name('update');
        Route::delete('/{auction}', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{auction}/approve', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'approve'])->name('approve');
        Route::post('/{auction}/reject', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'reject'])->name('reject');
        Route::post('/{auction}/close', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'close'])->name('close');
        Route::get('/{auction}/bids', [\App\Http\Controllers\Admin\AuctionManagementController::class, 'bids'])->name('bids');
    });

    // Payment Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PaymentManagementController::class, 'index'])->name('index');
        Route::get('/{payment}', [\App\Http\Controllers\Admin\PaymentManagementController::class, 'show'])->name('show');
        Route::post('/check-status/{payment}', [\App\Http\Controllers\Admin\PaymentManagementController::class, 'checkPaymentStatus'])->name('check-status');
        Route::post('/process-payment/{payment}', [\App\Http\Controllers\Admin\PaymentManagementController::class, 'processPaidPayment'])->name('process-payment');
        Route::post('/create-link/{auction}', [\App\Http\Controllers\Admin\PaymentManagementController::class, 'createNewPaymentLink'])->name('create-link');
        Route::post('/bulk-check', [\App\Http\Controllers\Admin\PaymentManagementController::class, 'bulkCheckStatus'])->name('bulk-check');
        Route::post('/resend-notification/{payment}', [\App\Http\Controllers\Admin\PaymentManagementController::class, 'resendNotification'])->name('resend-notification');
    });

    // Admin Fee Management
    Route::prefix('admin-fees')->name('admin-fees.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminFeeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AdminFeeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AdminFeeController::class, 'store'])->name('store');
        Route::get('/transactions', [\App\Http\Controllers\Admin\AdminFeeController::class, 'transactions'])->name('transactions');
        Route::get('/statistics', [\App\Http\Controllers\Admin\AdminFeeController::class, 'statistics'])->name('statistics');
        Route::post('/preview', [\App\Http\Controllers\Admin\AdminFeeController::class, 'getFeePreview'])->name('preview');
        Route::get('/preview', [\App\Http\Controllers\Admin\AdminFeeController::class, 'preview'])->name('preview');
        Route::get('/vendor-statistics', [\App\Http\Controllers\Admin\AdminFeeController::class, 'getVendorStatistics'])->name('vendor-statistics');
        Route::get('/{adminFee}', [\App\Http\Controllers\Admin\AdminFeeController::class, 'show'])->name('show');
        Route::get('/{adminFee}/edit', [\App\Http\Controllers\Admin\AdminFeeController::class, 'edit'])->name('edit');
        Route::put('/{adminFee}', [\App\Http\Controllers\Admin\AdminFeeController::class, 'update'])->name('update');
        Route::delete('/{adminFee}', [\App\Http\Controllers\Admin\AdminFeeController::class, 'destroy'])->name('destroy');
        Route::patch('/{adminFee}/toggle', [\App\Http\Controllers\Admin\AdminFeeController::class, 'toggleStatus'])->name('toggle');
    });

    // Withdrawal Management
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'statistics'])->name('statistics');
        Route::get('/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/approve', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'approve'])->name('approve');
        Route::post('/{withdrawal}/reject', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'reject'])->name('reject');
        Route::post('/{withdrawal}/complete', [\App\Http\Controllers\Admin\WithdrawalManagementController::class, 'complete'])->name('complete');
    });

    // Analytics & Monitoring
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/pulse', [\App\Http\Controllers\Admin\PulseController::class, 'index'])->name('pulse');
        Route::get('/pulse/statistics', [\App\Http\Controllers\Admin\PulseController::class, 'statistics'])->name('pulse.statistics');
        Route::get('/pulse/performance', [\App\Http\Controllers\Admin\PulseController::class, 'performance'])->name('pulse.performance');
        Route::get('/pulse/activity', [\App\Http\Controllers\Admin\PulseController::class, 'activity'])->name('pulse.activity');

        Route::get('/vendor-revenue', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'index'])->name('vendor-revenue');
        Route::get('/vendor-revenue/{vendor}', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'show'])->name('vendor-revenue.show');
        Route::get('/vendor-revenue/api/statistics', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'statistics'])->name('vendor-revenue.statistics');
        Route::get('/vendor-revenue/api/monthly-data', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'monthlyData'])->name('vendor-revenue.monthly-data');
        Route::get('/vendor-revenue/api/vendor/{vendor}', [\App\Http\Controllers\Admin\VendorRevenueController::class, 'vendorData'])->name('vendor-revenue.vendor-data');
    });

    // CMS Management
    Route::prefix('cms')->name('cms.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CmsController::class, 'index'])->name('index');
        Route::get('/{category}', [\App\Http\Controllers\Admin\CmsController::class, 'show'])->name('show');
        Route::get('/preview/landing', [\App\Http\Controllers\Admin\CmsController::class, 'preview'])->name('preview');
        Route::get('/statistics', function () {
            return view('admin.cms.statistics');
        })->name('statistics');
        Route::post('/', [\App\Http\Controllers\Admin\CmsController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'destroy'])->name('destroy');
        Route::post('/toggle/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'toggle'])->name('toggle');
        Route::post('/reset', [\App\Http\Controllers\Admin\CmsController::class, 'reset'])->name('reset');
        Route::post('/upload-image', [\App\Http\Controllers\Admin\CmsController::class, 'uploadImage'])->name('upload-image');
        Route::get('/export', [\App\Http\Controllers\Admin\CmsController::class, 'export'])->name('export');
        Route::post('/import', [\App\Http\Controllers\Admin\CmsController::class, 'import'])->name('import');
        Route::get('/api/settings/{category?}', [\App\Http\Controllers\Admin\CmsController::class, 'getSettings'])->name('api.settings');
        Route::put('/setting/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'updateSetting'])->name('update-setting');
    });
});

// ============================================================================
// VENDOR ROUTES
// ============================================================================

Route::middleware(['auth', 'verified', 'vendor'])->prefix('vendor')->name('vendor.')->group(function () {

    // Dashboard
    Route::get('/', [UserDashboardController::class, 'vendorDashboard'])->name('dashboard');

    // POS System
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/category/{slug}', [PosController::class, 'category'])->name('category');
        Route::get('/search', [PosController::class, 'search'])->name('search');
        Route::get('/cart', [PosController::class, 'cart'])->name('cart');
        Route::post('/add-to-cart', [PosController::class, 'addToCart'])->name('addToCart');
        Route::post('/update-cart', [PosController::class, 'updateCart'])->name('updateCart');
        Route::post('/remove-from-cart', [PosController::class, 'removeFromCart'])->name('removeFromCart');
        Route::post('/check-price', [PosController::class, 'checkPrice'])->name('checkPrice');
        Route::post('/remove-item/{index}', [PosController::class, 'removeItem'])->name('removeItem');
        Route::post('/clear-cart', [PosController::class, 'clearCart'])->name('clearCart');
        Route::post('/checkout', [CheckoutController::class, 'processCheckout'])->name('checkout');
        Route::get('/invoice/{transaksi}', [InvoiceController::class, 'show'])->name('invoice');
        Route::get('/invoice/{transaksi}/print', [InvoiceController::class, 'print'])->name('invoice.print');
    });

    // Product Management
    Route::resource('products', ProdukController::class);
    Route::resource('categories', KategoriProdukController::class);
    Route::resource('materials', BahanController::class);
    Route::resource('specifications', SpesifikasiController::class);
    Route::resource('tools', AlatController::class);

    // Customer Management
    Route::resource('customers', PelangganController::class);
    Route::resource('users', PenggunaController::class);

    // Transaction Management
    Route::resource('transactions', TransaksiController::class);
    Route::get('/transactions/{transaksi}/invoice', [InvoiceController::class, 'show'])->name('transactions.invoice');
    Route::get('/transactions/{transaksi}/print', [InvoiceController::class, 'print'])->name('transactions.print');

    // Auction System
    Route::prefix('auctions')->name('auctions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\vendor\AuctionBidController::class, 'index'])->name('index');
        Route::get('/my-bids', [\App\Http\Controllers\vendor\AuctionBidController::class, 'myBids'])->name('my-bids');
        Route::get('/{auction}', [\App\Http\Controllers\vendor\AuctionBidController::class, 'show'])->name('show');
        Route::get('/{auction}/bid', [\App\Http\Controllers\vendor\AuctionBidController::class, 'create'])->name('bid');
        Route::post('/{auction}/bid', [\App\Http\Controllers\vendor\AuctionBidController::class, 'store'])->name('store-bid');
        Route::get('/bids/{bid}/edit', [\App\Http\Controllers\vendor\AuctionBidController::class, 'edit'])->name('edit-bid');
        Route::put('/bids/{bid}', [\App\Http\Controllers\vendor\AuctionBidController::class, 'update'])->name('update-bid');
        Route::delete('/bids/{bid}', [\App\Http\Controllers\vendor\AuctionBidController::class, 'destroy'])->name('destroy-bid');
    });

    // Order Tracking & Shipping
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderTrackingController::class, 'vendorIndex'])->name('index');
        Route::put('/{transaksi}', [\App\Http\Controllers\OrderTrackingController::class, 'updateStatus'])->name('update');
        Route::post('/{auction}/shipping-invoice', [\App\Http\Controllers\ShippingInvoiceController::class, 'generateShippingInvoice'])->name('shipping-invoice');
        Route::put('/{auction}/shipping-status', [\App\Http\Controllers\ShippingInvoiceController::class, 'updateShippingStatus'])->name('shipping-status');
        Route::get('/{auction}/track', [\App\Http\Controllers\ShippingInvoiceController::class, 'trackShipment'])->name('track');
    });

    // Shipping Calculator
    Route::prefix('shipping')->name('shipping.')->group(function () {
        Route::get('/calculator', [\App\Http\Controllers\ShippingCalculatorController::class, 'index'])->name('calculator');
        Route::post('/calculate', [\App\Http\Controllers\ShippingCalculatorController::class, 'calculate'])->name('calculate');
        Route::get('/couriers', [\App\Http\Controllers\ShippingCalculatorController::class, 'getCouriers'])->name('couriers');
        Route::post('/service-types', [\App\Http\Controllers\ShippingCalculatorController::class, 'getServiceTypes'])->name('service-types');
        Route::post('/save/{transaksi}', [\App\Http\Controllers\ShippingCalculatorController::class, 'saveShipping'])->name('save');
    });

    // Wallet Management
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VendorWalletController::class, 'index'])->name('index');
        Route::get('/transactions', [\App\Http\Controllers\VendorWalletController::class, 'transactions'])->name('transactions');
        Route::get('/withdrawals', [\App\Http\Controllers\VendorWalletController::class, 'withdrawals'])->name('withdrawals');
        Route::get('/withdrawals/create', [\App\Http\Controllers\VendorWalletController::class, 'createWithdrawal'])->name('create-withdrawal');
        Route::post('/withdrawals', [\App\Http\Controllers\VendorWalletController::class, 'storeWithdrawal'])->name('store-withdrawal');
        Route::get('/withdrawals/{withdrawal}', [\App\Http\Controllers\VendorWalletController::class, 'showWithdrawal'])->name('show-withdrawal');
        Route::post('/withdrawals/{withdrawal}/cancel', [\App\Http\Controllers\VendorWalletController::class, 'cancelWithdrawal'])->name('cancel-withdrawal');
    });

    // Bank Account Management
    Route::resource('bank-accounts', \App\Http\Controllers\VendorBankAccountController::class);

    // Withdrawal Management
    Route::prefix('withdrawal')->name('withdrawal.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VendorWithdrawalController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\VendorWithdrawalController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\VendorWithdrawalController::class, 'store'])->name('store');
        Route::get('/history', [\App\Http\Controllers\VendorWithdrawalController::class, 'history'])->name('history');
        Route::get('/{withdrawal}', [\App\Http\Controllers\VendorWithdrawalController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/cancel', [\App\Http\Controllers\VendorWithdrawalController::class, 'cancel'])->name('cancel');
    });

    // Rating System
    Route::prefix('ratings')->name('ratings.')->group(function () {
        Route::get('/{auction}', [\App\Http\Controllers\VendorRatingController::class, 'create'])->name('create');
        Route::post('/{auction}', [\App\Http\Controllers\VendorRatingController::class, 'store'])->name('store');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/transactions', [LaporanController::class, 'transactions'])->name('transactions');
        Route::get('/products', [LaporanController::class, 'products'])->name('products');
        Route::get('/customers', [LaporanController::class, 'customers'])->name('customers');
        Route::get('/export', [LaporanController::class, 'export'])->name('export');
    });

    // Laporan Routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/penjualan-harian', [LaporanController::class, 'penjualanHarian'])->name('penjualan-harian');
        Route::get('/penjualan-bulanan', [LaporanController::class, 'penjualanBulanan'])->name('penjualan-bulanan');
        Route::get('/penjualan-tahunan', [LaporanController::class, 'penjualanTahunan'])->name('penjualan-tahunan');
        Route::get('/export-penjualan', [LaporanController::class, 'exportPenjualan'])->name('export-penjualan');
    });
});

// ============================================================================
// USER ROUTES
// ============================================================================

Route::middleware(['auth', 'verified', 'user'])->prefix('user')->name('user.')->group(function () {

    // Delivery Confirmation Routes
    Route::prefix('delivery-confirmation')->name('delivery-confirmation.')->group(function () {
        Route::get('/{auction}/create', [\App\Http\Controllers\DeliveryConfirmationController::class, 'create'])->name('create');
        Route::post('/{auction}', [\App\Http\Controllers\DeliveryConfirmationController::class, 'store'])->name('store');
        Route::get('/{confirmation}', [\App\Http\Controllers\DeliveryConfirmationController::class, 'show'])->name('show');
    });

    // Dashboard
    Route::get('/', [UserDashboardController::class, 'userDashboard'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Auction System
    Route::resource('auctions', \App\Http\Controllers\AuctionController::class);
    Route::post('/auctions/{auction}/payment', [\App\Http\Controllers\AuctionController::class, 'createPayment'])->name('auctions.payment');
    Route::post('/auctions/{auction}/close', [\App\Http\Controllers\AuctionController::class, 'close'])->name('auctions.close');
    Route::get('/auctions/my/auctions', [\App\Http\Controllers\AuctionController::class, 'myAuctions'])->name('auctions.my');

    // Order Tracking
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderTrackingController::class, 'index'])->name('index');
        Route::get('/{auction}', [\App\Http\Controllers\OrderTrackingController::class, 'show'])->name('show');
    });

    // Shipping Management
    Route::prefix('shipping')->name('shipping.')->group(function () {
        Route::post('/invoice/{transaksi}', [\App\Http\Controllers\ShippingInvoiceController::class, 'generateShippingInvoice'])->name('generate-invoice');
        Route::post('/payment/{transaksi}', [\App\Http\Controllers\ShippingInvoiceController::class, 'handleCODPayment'])->name('handle-payment');
        Route::post('/calculate', [\App\Http\Controllers\ShippingInvoiceController::class, 'calculateShippingCost'])->name('calculate-cost');
    });
});

// ============================================================================
// API ROUTES
// ============================================================================

Route::prefix('api')->name('api.')->group(function () {

    // Shipping API
    Route::post('/calculate-shipping', [\App\Http\Controllers\OrderTrackingController::class, 'calculateShipping'])->name('calculate-shipping');
    Route::post('/track-shipment', [\App\Http\Controllers\OrderTrackingController::class, 'trackShipment'])->name('track-shipment');
    Route::post('/shipping/calculate', [\App\Http\Controllers\ShippingCalculatorController::class, 'calculate'])->name('shipping.calculate');
    Route::get('/shipping/couriers', [\App\Http\Controllers\ShippingCalculatorController::class, 'getCouriers'])->name('shipping.couriers');
    Route::post('/shipping/service-types', [\App\Http\Controllers\ShippingCalculatorController::class, 'getServiceTypes'])->name('shipping.service-types');

    // Xendit Payment API
    Route::prefix('xendit')->name('xendit.')->group(function () {
        Route::get('/payment-methods', [\App\Http\Controllers\XenditPaymentController::class, 'getPaymentMethods'])->name('payment.methods');
        Route::get('/payments/{payment}', [\App\Http\Controllers\XenditPaymentController::class, 'show'])->name('payment.show');
        Route::get('/payments/{payment}/status', [\App\Http\Controllers\XenditPaymentController::class, 'getStatus'])->name('payment.status');
        Route::post('/payments/{payment}/expire', [\App\Http\Controllers\XenditPaymentController::class, 'expire'])->name('payment.expire');
        Route::post('/webhook', [\App\Http\Controllers\XenditWebhookController::class, 'handleWebhook'])->name('webhook');
    });

    // Vendor API
    Route::prefix('vendor')->middleware(['auth', 'vendor'])->name('vendor.')->group(function () {
        Route::get('/banks', [\App\Http\Controllers\VendorBankAccountController::class, 'getBanks'])->name('banks');
        Route::get('/ewallet-providers', [\App\Http\Controllers\VendorBankAccountController::class, 'getEwalletProviders'])->name('ewallet-providers');
        Route::get('/account-details', [\App\Http\Controllers\VendorBankAccountController::class, 'getAccountDetails'])->name('account-details');
        Route::post('/withdrawal/calculate-fee', [\App\Http\Controllers\VendorWithdrawalController::class, 'calculateFee'])->name('withdrawal.calculate-fee');
    });

    // Mobile API
    Route::prefix('mobile')->middleware(['auth'])->name('mobile.')->group(function () {
        Route::get('/user', [\App\Http\Controllers\Api\AuthController::class, 'user'])->name('user');
    });
});

// ============================================================================
// WEBHOOK ROUTES
// ============================================================================

Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/xendit', [\App\Http\Controllers\XenditWebhookController::class, 'handleWebhook'])->name('xendit');
});

// ============================================================================
// LEGACY ROUTES (Backward Compatibility)
// ============================================================================

// Keep old routes for backward compatibility
Route::get('/administrator', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified', 'dev']);

Route::get('/dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user && $user->usertype === 'vendor') {
        return redirect()->route('vendor.dashboard');
    } elseif ($user && $user->usertype === 'user') {
        return redirect()->route('user.dashboard');
    }
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified']);

// ============================================================================
// DEBUG ROUTES (Development Only)
// ============================================================================

if (app()->environment('local')) {
    Route::get('/debug/test', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        return response()->json([
            'user' => $user,
            'usertype' => $user?->usertype,
            'middleware_passed' => true,
            'route_accessible' => true
        ]);
    })->name('debug.test');
}

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
use App\Http\Controllers\Vendor\LinktreeController;
use App\Http\Controllers\LinktreePublicController;
use App\Http\Controllers\LinktreePaymentController;
use App\Http\Controllers\vendor\TemplateController;

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

// Public vendor profile route
Route::get('/vendor/{vendor}/profile', function (\App\Models\Vendor $vendor) {
    return view('vendor.public-profile', compact('vendor'));
})->name('vendor.public.profile');

// Public Linktree route (with rate limiting to prevent abuse)
Route::get('/l/{customUrl}', [LinktreePublicController::class, 'show'])->middleware('throttle:public-page')->name('linktree.public');
Route::get('/l/{customUrl}/click/{linkId}', [LinktreePublicController::class, 'trackClick'])->middleware('throttle:public-page')->name('linktree.click');
Route::post('/l/{customUrl}/pay/qris', [LinktreePaymentController::class, 'generateQris'])->middleware('throttle:public-page')->name('linktree.pay.qris');
Route::get('/l/{customUrl}/pay/status/{invoiceId}', [LinktreePaymentController::class, 'checkStatus'])->middleware('throttle:public-page')->name('linktree.pay.status');

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

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
        Route::get('/preview', [\App\Http\Controllers\Admin\AdminFeeController::class, 'preview'])->name('preview-page');
        Route::get('/vendor-statistics', [\App\Http\Controllers\Admin\AdminFeeController::class, 'getVendorStatistics'])->name('vendor-statistics');
        Route::get('/{adminFee}', [\App\Http\Controllers\Admin\AdminFeeController::class, 'show'])->name('show');
        Route::get('/{adminFee}/edit', [\App\Http\Controllers\Admin\AdminFeeController::class, 'edit'])->name('edit');
        Route::put('/{adminFee}', [\App\Http\Controllers\Admin\AdminFeeController::class, 'update'])->name('update');
        Route::delete('/{adminFee}', [\App\Http\Controllers\Admin\AdminFeeController::class, 'destroy'])->name('destroy');
        Route::patch('/{adminFee}/toggle', [\App\Http\Controllers\Admin\AdminFeeController::class, 'toggleStatus'])->name('toggle');
    });

    // Mediation Management
    Route::prefix('mediation')->name('mediation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MediationController::class, 'index'])->name('index');
        Route::get('/{mediationRequest}', [\App\Http\Controllers\Admin\MediationController::class, 'show'])->name('show');
        Route::post('/{mediationRequest}/start-review', [\App\Http\Controllers\Admin\MediationController::class, 'startReview'])->name('start-review');
        Route::post('/{mediationRequest}/resolve', [\App\Http\Controllers\Admin\MediationController::class, 'resolve'])->name('resolve');
        Route::post('/{mediationRequest}/close', [\App\Http\Controllers\Admin\MediationController::class, 'close'])->name('close');
        Route::get('/statistics', [\App\Http\Controllers\Admin\MediationController::class, 'statistics'])->name('statistics');
    });

    // Audit Logs Management
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('index');
        Route::get('/high-risk', [\App\Http\Controllers\Admin\AuditLogController::class, 'highRisk'])->name('high-risk');
        Route::get('/financial', [\App\Http\Controllers\Admin\AuditLogController::class, 'financial'])->name('financial');
        Route::get('/export', [\App\Http\Controllers\Admin\AuditLogController::class, 'export'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('show');
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

    // Wallet Management
    Route::prefix('wallets')->name('wallets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WalletManagementController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\WalletManagementController::class, 'statistics'])->name('statistics');
        Route::get('/{wallet}', [\App\Http\Controllers\Admin\WalletManagementController::class, 'show'])->name('show');
        Route::get('/{wallet}/transactions', [\App\Http\Controllers\Admin\WalletManagementController::class, 'transactions'])->name('transactions');
        Route::post('/{wallet}/freeze', [\App\Http\Controllers\Admin\WalletManagementController::class, 'freeze'])->name('freeze');
        Route::post('/{wallet}/unfreeze', [\App\Http\Controllers\Admin\WalletManagementController::class, 'unfreeze'])->name('unfreeze');
    });

    // Shipping Management
    Route::prefix('shipping')->name('shipping.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('index');
        Route::get('/invoices', [\App\Http\Controllers\Admin\ShippingController::class, 'invoices'])->name('invoices');
        Route::get('/export', [\App\Http\Controllers\Admin\ShippingController::class, 'export'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\Admin\ShippingController::class, 'show'])->name('show');
        Route::get('/{id}/track', [\App\Http\Controllers\Admin\ShippingController::class, 'track'])->name('track');
        Route::patch('/{id}/status', [\App\Http\Controllers\Admin\ShippingController::class, 'updateStatus'])->name('update-status');
    });

    // Delivery Management
    Route::prefix('delivery')->name('delivery.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DeliveryController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\Admin\DeliveryController::class, 'export'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\Admin\DeliveryController::class, 'show'])->name('show');
        Route::patch('/{id}/approve', [\App\Http\Controllers\Admin\DeliveryController::class, 'approve'])->name('approve');
        Route::patch('/{id}/reject', [\App\Http\Controllers\Admin\DeliveryController::class, 'reject'])->name('reject');
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

    // User Lelang Management
    Route::prefix('user-lelang')->name('user-lelang.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserLelangController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserLelangController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserLelangController::class, 'store'])->name('store');
        Route::get('/{profile}', [\App\Http\Controllers\Admin\UserLelangController::class, 'show'])->name('show');
        Route::get('/{profile}/edit', [\App\Http\Controllers\Admin\UserLelangController::class, 'edit'])->name('edit');
        Route::put('/{profile}', [\App\Http\Controllers\Admin\UserLelangController::class, 'update'])->name('update');
        Route::delete('/{profile}', [\App\Http\Controllers\Admin\UserLelangController::class, 'destroy'])->name('destroy');
        Route::post('/{profile}/verify', [\App\Http\Controllers\Admin\UserLelangController::class, 'verify'])->name('verify');
        Route::post('/{profile}/suspend', [\App\Http\Controllers\Admin\UserLelangController::class, 'suspend'])->name('suspend');
        Route::post('/{profile}/reactivate', [\App\Http\Controllers\Admin\UserLelangController::class, 'reactivate'])->name('reactivate');
    });

    // Service Configurations (Third-Party API Settings)
    Route::prefix('service-configs')->name('service-configs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ServiceConfigController::class, 'index'])->name('index');
        Route::get('/{service}', [\App\Http\Controllers\Admin\ServiceConfigController::class, 'show'])->name('show');
        Route::post('/', [\App\Http\Controllers\Admin\ServiceConfigController::class, 'store'])->name('store');
        Route::put('/{serviceConfig}', [\App\Http\Controllers\Admin\ServiceConfigController::class, 'update'])->name('update');
        Route::delete('/{serviceConfig}', [\App\Http\Controllers\Admin\ServiceConfigController::class, 'destroy'])->name('destroy');
        Route::post('/{serviceConfig}/toggle', [\App\Http\Controllers\Admin\ServiceConfigController::class, 'toggle'])->name('toggle');
        Route::post('/test-connection', [\App\Http\Controllers\Admin\ServiceConfigController::class, 'testConnection'])->name('test-connection');
        Route::get('/seed-defaults', [\App\Http\Controllers\Admin\ServiceConfigController::class, 'seedDefaults'])->name('seed-defaults');
        Route::get('/clear-cache', [\App\Http\Controllers\Admin\ServiceConfigController::class, 'clearCache'])->name('clear-cache');
    });
});

// ============================================================================
// VENDOR ROUTES
// ============================================================================

Route::middleware(['auth', 'verified', 'vendor', 'tenants'])->prefix('vendor')->name('vendor.')->group(function () {

    // Dashboard
    Route::get('/', [UserDashboardController::class, 'vendorDashboard'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

        // Payment System
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/{transaksi}/options', [\App\Http\Controllers\vendor\pos\PaymentController::class, 'showPaymentOptions'])->name('options');
            Route::get('/{transaksi}/cash', [\App\Http\Controllers\vendor\pos\PaymentController::class, 'showPaymentOptions'])->name('cash');
            Route::post('/{transaksi}/cash', [\App\Http\Controllers\vendor\pos\PaymentController::class, 'processCashPayment'])->name('cash.process');
            Route::get('/{transaksi}/online', [\App\Http\Controllers\vendor\pos\PaymentController::class, 'showPaymentOptions'])->name('online');
            Route::post('/{transaksi}/online', [\App\Http\Controllers\vendor\pos\PaymentController::class, 'processXenditPayment'])->name('online.process');
            Route::get('/{transaksi}/success', [\App\Http\Controllers\vendor\pos\PaymentController::class, 'paymentSuccess'])->name('success');
            Route::get('/{transaksi}/failure', [\App\Http\Controllers\vendor\pos\PaymentController::class, 'paymentFailure'])->name('failure');
        });

        // Thermal Printing
        Route::get('/{transaksi}/thermal', [\App\Http\Controllers\vendor\pos\ThermalPrintController::class, 'printDirect'])->name('thermal-print');
        Route::get('/{transaksi}/thermal-js', [\App\Http\Controllers\vendor\pos\ThermalPrintController::class, 'printViaJS'])->name('thermal-print-js');
        Route::get('/printer-settings', [\App\Http\Controllers\vendor\pos\ThermalPrintController::class, 'showSettings'])->name('printer.settings');
        Route::post('/printer-settings', [\App\Http\Controllers\vendor\pos\ThermalPrintController::class, 'saveSettings'])->name('printer.settings.save');
        Route::get('/printer-settings/json', [\App\Http\Controllers\vendor\pos\ThermalPrintController::class, 'getPrinterSettings'])->name('printer.settings.json');
    });

    // Product Management
    Route::post('products/bulk-update', [ProdukController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::resource('products', ProdukController::class);
    Route::resource('categories', KategoriProdukController::class);
    Route::post('materials/bulk-update', [BahanController::class, 'bulkUpdate'])->name('materials.bulk-update');
    Route::resource('materials', BahanController::class);
    Route::resource('specifications', SpesifikasiController::class);
    Route::post('tools/bulk-update', [AlatController::class, 'bulkUpdate'])->name('tools.bulk-update');
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

    // Laporan Routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/penjualan-harian', [LaporanController::class, 'penjualanHarian'])->name('penjualan-harian');
        Route::get('/penjualan-bulanan', [LaporanController::class, 'penjualanBulanan'])->name('penjualan-bulanan');
        Route::get('/penjualan-tahunan', [LaporanController::class, 'penjualanTahunan'])->name('penjualan-tahunan');
        Route::get('/export-penjualan', [LaporanController::class, 'exportPenjualan'])->name('export-penjualan');
    });

    // Linktree Management
    Route::prefix('linktree')->name('linktree.')->group(function () {
        Route::get('/', [LinktreeController::class, 'index'])->name('index');
        Route::get('/create', [LinktreeController::class, 'create'])->name('create');
        Route::post('/', [LinktreeController::class, 'store'])->name('store');
        Route::get('/{linktree}', [LinktreeController::class, 'show'])->name('show');
        Route::get('/{linktree}/analytics', [LinktreeController::class, 'analytics'])->name('analytics');
        Route::get('/{linktree}/edit', [LinktreeController::class, 'edit'])->name('edit');
        Route::put('/{linktree}', [LinktreeController::class, 'update'])->name('update');
        Route::delete('/{linktree}', [LinktreeController::class, 'destroy'])->name('destroy');
        Route::post('/{linktree}/toggle-active', [LinktreeController::class, 'toggleActive'])->name('toggle-active');

        // Link Management
        Route::post('/{linktree}/links', [LinktreeController::class, 'storeLink'])->name('links.store');
        Route::put('/{linktree}/links/{link}', [LinktreeController::class, 'updateLink'])->name('links.update');
        Route::delete('/{linktree}/links/{link}', [LinktreeController::class, 'destroyLink'])->name('links.destroy');
        Route::post('/{linktree}/links/reorder', [LinktreeController::class, 'reorderLinks'])->name('links.reorder');

        // Social Media Management
        Route::post('/{linktree}/socials', [LinktreeController::class, 'storeSocial'])->name('socials.store');
        Route::put('/{linktree}/socials/{social}', [LinktreeController::class, 'updateSocial'])->name('socials.update');
        Route::delete('/{linktree}/socials/{social}', [LinktreeController::class, 'destroySocial'])->name('socials.destroy');

        // Upload
        Route::post('/{linktree}/upload-avatar', [LinktreeController::class, 'uploadAvatar'])->name('upload-avatar');
        Route::post('/{linktree}/upload-banner', [LinktreeController::class, 'uploadBanner'])->name('upload-banner');
        Route::post('/{linktree}/upload-qris', [LinktreeController::class, 'uploadQris'])->name('upload-qris');

        // Bulk Link Management
        Route::get('/{linktree}/export-links', [LinktreeController::class, 'exportLinks'])->name('export-links');
        Route::get('/{linktree}/import-links', [LinktreeController::class, 'importLinksForm'])->name('import-links-form');
        Route::post('/{linktree}/import-links', [LinktreeController::class, 'importLinks'])->name('import-links');

        // Template Builder
        Route::get('/{linktree}/template', [TemplateController::class, 'index'])->name('template.index');
        Route::post('/{linktree}/template/preview', [TemplateController::class, 'preview'])->name('template.preview');
        Route::post('/{linktree}/template/apply', [TemplateController::class, 'apply'])->name('template.apply');
        Route::get('/{linktree}/template/{template}/colors', [TemplateController::class, 'getColors'])->name('template.colors');

        // A/B Testing
        Route::get('/{linktree}/ab-test', [\App\Http\Controllers\vendor\AbTestController::class, 'index'])->name('ab-test.index');
        Route::get('/{linktree}/ab-test/create', [\App\Http\Controllers\vendor\AbTestController::class, 'create'])->name('ab-test.create');
        Route::post('/{linktree}/ab-test', [\App\Http\Controllers\vendor\AbTestController::class, 'store'])->name('ab-test.store');
        Route::get('/{linktree}/ab-test/{abTest}', [\App\Http\Controllers\vendor\AbTestController::class, 'show'])->name('ab-test.show');
        Route::post('/{linktree}/ab-test/{abTest}/start', [\App\Http\Controllers\vendor\AbTestController::class, 'start'])->name('ab-test.start');
        Route::post('/{linktree}/ab-test/{abTest}/pause', [\App\Http\Controllers\vendor\AbTestController::class, 'pause'])->name('ab-test.pause');
        Route::post('/{linktree}/ab-test/{abTest}/stop', [\App\Http\Controllers\vendor\AbTestController::class, 'stop'])->name('ab-test.stop');
        Route::post('/{linktree}/ab-test/{abTest}/apply-winner', [\App\Http\Controllers\vendor\AbTestController::class, 'applyWinner'])->name('ab-test.apply-winner');
        Route::delete('/{linktree}/ab-test/{abTest}', [\App\Http\Controllers\vendor\AbTestController::class, 'destroy'])->name('ab-test.destroy');

        // Product Catalog Management
        Route::get('/{linktree}/products', [LinktreeController::class, 'manageProducts'])->name('products');
        Route::post('/{linktree}/products', [LinktreeController::class, 'addProduct'])->name('products.store');
        Route::put('/{linktree}/products/{product}', [LinktreeController::class, 'updateProduct'])->name('products.update');
        Route::post('/{linktree}/products/{product}/toggle', [LinktreeController::class, 'toggleProduct'])->name('products.toggle');
        Route::delete('/{linktree}/products/{product}', [LinktreeController::class, 'removeProduct'])->name('products.destroy');
        Route::post('/{linktree}/products/reorder', [LinktreeController::class, 'reorderProducts'])->name('products.reorder');
    });

    // Manual Transfer Orders
    Route::prefix('manual-transfers')->name('manual-transfers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\vendor\VendorManualTransferController::class, 'index'])->name('index');
        Route::get('/{order}', [\App\Http\Controllers\vendor\VendorManualTransferController::class, 'show'])->name('show');
        Route::post('/{order}/confirm', [\App\Http\Controllers\vendor\VendorManualTransferController::class, 'confirm'])->name('confirm');
        Route::post('/{order}/reject', [\App\Http\Controllers\vendor\VendorManualTransferController::class, 'reject'])->name('reject');
    });

    // Vendor Audit Logs
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VendorAuditLogController::class, 'index'])->name('index');
        Route::get('/financial', [\App\Http\Controllers\VendorAuditLogController::class, 'financial'])->name('financial');
        Route::get('/export', [\App\Http\Controllers\VendorAuditLogController::class, 'export'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\VendorAuditLogController::class, 'show'])->name('show');
    });
});

// ============================================================================
// USER ROUTES
// ============================================================================

Route::middleware(['auth', 'verified', 'user'])->prefix('user')->name('user.')->group(function () {

    // Delivery Confirmation Routes
    Route::prefix('delivery-confirmation')->name('delivery-confirmation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DeliveryConfirmationController::class, 'index'])->name('index');
        Route::get('/{auction}/create', [\App\Http\Controllers\DeliveryConfirmationController::class, 'create'])->name('create');
        Route::post('/{auction}', [\App\Http\Controllers\DeliveryConfirmationController::class, 'store'])->name('store');
        Route::get('/{confirmation}', [\App\Http\Controllers\DeliveryConfirmationController::class, 'show'])->name('show');
        Route::post('/{confirmation}/resolve-dispute', [\App\Http\Controllers\DeliveryConfirmationController::class, 'resolveDispute'])->name('resolve-dispute');
    });

    // Dashboard
    Route::get('/', [UserDashboardController::class, 'userDashboard'])->name('dashboard');

    // Lelang Dashboard (dedicated for lelang users)
    Route::get('/lelang-dashboard', [UserDashboardController::class, 'lelangDashboard'])->name('lelang-dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Auction System
    Route::resource('auctions', \App\Http\Controllers\AuctionController::class);
    Route::post('/auctions/{auction}/payment', [\App\Http\Controllers\AuctionController::class, 'createPayment'])->name('auctions.payment');
    Route::post('/auctions/{auction}/close', [\App\Http\Controllers\AuctionController::class, 'closeAuction'])->name('auctions.close');
    Route::get('/auctions/my/auctions', [\App\Http\Controllers\AuctionController::class, 'myAuctions'])->name('auctions.my');

    // Payment Confirmation System
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/{auction}/confirmation', [\App\Http\Controllers\PaymentConfirmationController::class, 'show'])->name('confirmation');
        Route::post('/{auction}/process', [\App\Http\Controllers\PaymentConfirmationController::class, 'process'])->name('process');
        Route::get('/{auction}/success', [\App\Http\Controllers\PaymentConfirmationController::class, 'success'])->name('success');
        Route::get('/{auction}/failure', [\App\Http\Controllers\PaymentConfirmationController::class, 'failure'])->name('failure');
    });

    // Order Tracking System
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderTrackingController::class, 'index'])->name('index');
        Route::get('/{orderTracking}', [\App\Http\Controllers\OrderTrackingController::class, 'show'])->name('show');
        Route::post('/{orderTracking}/mediation', [\App\Http\Controllers\OrderTrackingController::class, 'requestMediation'])->name('mediation');
        Route::post('/{orderTracking}/confirm-delivery', [\App\Http\Controllers\OrderTrackingController::class, 'confirmDelivery'])->name('confirm-delivery');
        Route::get('/{orderTracking}/status', [\App\Http\Controllers\OrderTrackingController::class, 'getTrackingStatus'])->name('status');
    });
});

// Shipping Management (authenticated)
Route::middleware(['auth', 'verified'])->prefix('shipping')->name('shipping.')->group(function () {
    Route::post('/invoice/{transaksi}', [\App\Http\Controllers\ShippingInvoiceController::class, 'generateShippingInvoice'])->name('generate-invoice');
    Route::post('/payment/{transaksi}', [\App\Http\Controllers\ShippingInvoiceController::class, 'handleCODPayment'])->name('handle-payment');
    Route::post('/calculate', [\App\Http\Controllers\ShippingInvoiceController::class, 'calculateShippingCost'])->name('calculate-cost');
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
// MANUAL TRANSFER ROUTES (Public - no auth required)
// ============================================================================

Route::post('/manual-transfer/order', [\App\Http\Controllers\ManualTransferController::class, 'placeOrder'])->middleware('throttle:manual-transfer')->name('manual-transfer.place');
Route::get('/manual-transfer/{orderNumber}/status', [\App\Http\Controllers\ManualTransferController::class, 'checkStatus'])->name('manual-transfer.status');
Route::post('/manual-transfer/{orderNumber}/upload-proof', [\App\Http\Controllers\ManualTransferController::class, 'uploadProof'])->middleware('throttle:manual-transfer')->name('manual-transfer.upload-proof');

// ============================================================================
// WEBHOOK ROUTES - Xendit webhook sudah ditangani di API routes (api/xendit/webhook)
// Route lama /webhooks/xendit di-deprecate karena tidak ter-cover CSRF exclusion
// ============================================================================

Route::get('/webhooks/xendit', function () {
    return response()->json(['message' => 'Please use POST /api/xendit/webhook instead'], 400);
});

// ============================================================================
// LEGACY ROUTES (Backward Compatibility)
// ============================================================================

// Keep old routes for backward compatibility
Route::get('/administrator', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified', 'dev']);

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user && $user->usertype === 'vendor') {
        // Check if user has vendor relationship
        if (!$user->vendorUser || $user->vendorUser->isEmpty()) {
            Auth::logout();
            return redirect('/login')->with('error', 'No vendor account associated with this user.');
        }
        return redirect()->route('vendor.dashboard');
    } elseif ($user && $user->usertype === 'user') {
        return redirect()->route('user.dashboard');
    } elseif ($user && $user->usertype === 'dev') {
        return redirect()->route('admin.dashboard');
    }
    return redirect('/login');
})->middleware(['auth', 'verified']);

// ============================================================================
// DEBUG ROUTES (Development Only)
// ============================================================================

if (app()->environment('local')) {
    Route::get('/debug/test', function () {
        $user = Auth::user();
        return response()->json([
            'user' => $user,
            'usertype' => $user?->usertype,
            'middleware_passed' => true,
            'route_accessible' => true
        ]);
    })->name('debug.test');
}

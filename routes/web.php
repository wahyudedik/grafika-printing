<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Vendor\AlatController;
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
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'dev'])->group(function () {
    Route::get('/administrator', [UserDashboardController::class, 'devDashboard'])
        ->name('dev.dashboard');

    // users routes resource
    Route::resource('/administrator/users', UserController::class);

    // vendors routes resource
    Route::resource('/administrator/vendors', VendorController::class);
});

Route::middleware(['auth', 'verified', 'vendor'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'vendorDashboard'])
        ->name('dashboard');

    // vendor users routes resource
    Route::resource('/dashboard/pengguna', PenggunaController::class);

    // Alat routes resource
    Route::resource('/dashboard/alat', AlatController::class);
    Route::put('/dashboard/alat/batch-update-status', [AlatController::class, 'batchUpdateStatus'])
        ->name('alat.batch-update-status');
    Route::delete('/dashboard/alat/batch-delete', [AlatController::class, 'batchDelete'])
        ->name('alat.batch-delete');

    // Bahan routes resource
    Route::resource('/dashboard/bahan', BahanController::class);
    Route::delete('/dashboard/bahan/batch-delete', [BahanController::class, 'batchDelete'])
        ->name('bahan.batch-delete');
    Route::put('/dashboard/bahan/batch-update-stock', [BahanController::class, 'batchUpdateStock'])
        ->name('bahan.batch-update-stock');
    Route::delete('/dashboard/bahan/wholesale-price/{id}', [BahanController::class, 'deleteWholesalePrice'])
        ->name('bahan.wholesale-price.delete');

    // Pelanggan routes resource
    Route::resource('/dashboard/pelanggan', PelangganController::class);
    Route::delete('pelanggan/batch-delete', [PelangganController::class, 'batchDelete'])
        ->name('pelanggan.batch-delete');
    Route::put('/dashboard/pelanggan/batch-update-status', [PelangganController::class, 'batchUpdateStatus'])
        ->name('pelanggan.batch-update-status');

    // Spesifikasi input type bahan produk routes resource
    Route::resource('/dashboard/spesifikasi', SpesifikasiController::class);
    Route::delete('/dashboard/spesifikasi/batch-delete', [SpesifikasiController::class, 'batchDelete'])
        ->name('spesifikasi.batch-delete');

    // Produk routes resource
    Route::resource('/dashboard/produk', ProdukController::class);
    Route::delete('/dashboard/produk/batch-delete', [ProdukController::class, 'batchDelete'])
        ->name('produk.batch-delete');
    Route::put('/dashboard/produk/batch-update', [ProdukController::class, 'batchUpdate'])
        ->name('produk.batch-update');

    // Kategori Produk routes resource
    Route::resource('/dashboard/kategori-produk', KategoriProdukController::class);
    Route::delete('/dashboard/kategori-produk/batch-delete', [KategoriProdukController::class, 'batchDelete'])
        ->name('kategori-produk.batch-delete');

    // Transaksi routes resource
    Route::resource('/dashboard/transaksi', TransaksiController::class);
    Route::delete('/dashboard/transaksi/batch-delete', [TransaksiController::class, 'batchDelete'])
        ->name('transaksi.batch-delete');
    Route::put('/dashboard/transaksi/batch-update', [TransaksiController::class, 'batchUpdate'])
        ->name('transaksi.batch-update');
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
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('tenant')->group(function () {
    // routes
});

require __DIR__ . '/auth.php';

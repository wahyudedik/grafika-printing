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
use App\Http\Controllers\vendor\PenggunaController;
use App\Http\Controllers\vendor\PelangganController;
use App\Http\Controllers\vendor\TransaksiController;
use App\Http\Controllers\vendor\SpesifikasiController;
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
    Route::put('/alat/batch-update-status', [AlatController::class, 'batchUpdateStatus'])
        ->name('alat.batch-update-status');
    Route::delete('/alat/batch-delete', [AlatController::class, 'batchDelete'])
        ->name('alat.batch-delete');

    // Bahan routes resource
    Route::resource('/dashboard/bahan', BahanController::class);
    Route::delete('bahan/batch-delete', [BahanController::class, 'batchDelete'])
        ->name('bahan.batch-delete');
    Route::put('bahan/batch-update-stock', [BahanController::class, 'batchUpdateStock'])
        ->name('bahan.batch-update-stock');
    Route::delete('bahan/wholesale-price/{id}', [BahanController::class, 'deleteWholesalePrice'])
        ->name('bahan.wholesale-price.delete');

    // Pelanggan routes resource
    Route::resource('/dashboard/pelanggan', PelangganController::class);
    Route::delete('pelanggan/batch-delete', [PelangganController::class, 'batchDelete'])
        ->name('pelanggan.batch-delete');
    Route::put('pelanggan/batch-update-status', [PelangganController::class, 'batchUpdateStatus'])
        ->name('pelanggan.batch-update-status');

    // Spesifikasi input type bahan produk routes resource
    Route::resource('/dashboard/spesifikasi', SpesifikasiController::class);
    Route::delete('spesifikasi/batch-delete', [SpesifikasiController::class, 'batchDelete'])
        ->name('spesifikasi.batch-delete');

    // Produk routes resource
    Route::resource('/dashboard/produk', ProdukController::class);
    Route::delete('produk/batch-delete', [ProdukController::class, 'batchDelete'])
        ->name('produk.batch-delete');
    Route::put('produk/batch-update', [ProdukController::class, 'batchUpdate'])
        ->name('produk.batch-update');

    // Kategori Produk routes resource
    Route::resource('/dashboard/kategori-produk', KategoriProdukController::class);
    Route::delete('kategori-produk/batch-delete', [KategoriProdukController::class, 'batchDelete'])
        ->name('kategori-produk.batch-delete');

    // Transaksi routes resource
    Route::resource('/dashboard/transaksi', TransaksiController::class);
    Route::delete('/dashboard/transaksi/batch-delete', [TransaksiController::class, 'batchDelete'])
        ->name('transaksi.batch-delete');
    Route::put('/transaksi/batch-update', [TransaksiController::class, 'batchUpdate'])
        ->name('transaksi.batch-update');
    Route::get('transaksi/{id}/invoice', [TransaksiController::class, 'generateInvoice'])
        ->name('transaksi.generateInvoice');

    // routes Laporan
    Route::get('/penjualan-harian', [LaporanController::class, 'penjualanHarian'])
        ->name('laporan.penjualan-harian');
    Route::get('/penjualan-bulanan', [LaporanController::class, 'penjualanBulanan'])
        ->name('laporan.penjualan-bulanan');
    Route::get('/penjualan-tahunan', [LaporanController::class, 'penjualanTahunan'])
        ->name('laporan.penjualan-tahunan');
    Route::get('/export/penjualan', [LaporanController::class, 'exportPenjualan'])
        ->name('laporan.export-penjualan');
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

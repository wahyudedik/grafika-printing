# Rencana Perbaikan & Pengembangan Projek Grafika-Printing

> Dibuat: 5 Agustus 2026
> Status: Draft - Menunggu Persetujuan

---

## Ringkasan Temuan

Setelah analisis mendalam terhadap seluruh kode sourse, routes, controllers, views, dan dokumentasi, berikut temuan-temuan yang perlu diperbaiki:

---

## 🔴 BUG CRITICAL (Harus Diperbaiki)

### 1. View `vendor/public-profile.blade.php` TIDAK ADA
- **Lokasi Masalah:** [`routes/web.php:43`](routes/web.php:43)
- **Deskripsi:** Route `Route::get('/vendor/{vendor}/profile', ...)` merender view `vendor.public-profile`, tapi file view tidak ada di `resources/views/vendor/public-profile/`
- **Dampak:** Error 500 jika user membuka halaman profil vendor publik
- **Fix:** Buat view `resources/views/vendor/public-profile.blade.php`

### 2. Route `resolve-dispute` TIDAK TERDAFTAR
- **Lokasi Masalah:** [`app/Http/Controllers/DeliveryConfirmationController.php:202`](app/Http/Controllers/DeliveryConfirmationController.php:202)
- **Deskripsi:** Method `resolveDispute()` ada di controller, tapi TIDAK ada route yang mendaftarkannya di `routes/web.php`. View [`resources/views/user/delivery-confirmation/show.blade.php:180`](resources/views/user/delivery-confirmation/show.blade.php:180) memanggil route `user.delivery-confirmation.resolve-dispute`
- **Dampak:** Error 404 saat user mencoba resolve dispute
- **Fix:** Tambah route di `routes/web.php` dalam user group

---

## 🟡 CLEAN CODE & BEST PRACTICES

### 3. Bahasa Tidak Konsisten di Alert Components
- **Lokasi Masalah:** [`resources/views/dev/components/alert.blade.php:133`](resources/views/dev/components/alert.blade.php:133)
- **Deskripsi:** Function `confirmDelete()` masih menggunakan Bahasa Inggris ("Are you sure?", "Yes, delete it!") sedangkan [`resources/views/components/alert.blade.php:166`](resources/views/components/alert.blade.php:166) sudah menggunakan Bahasa Indonesia
- **Fix:** Standarisasi ke Bahasa Indonesia

### 4. File Layout Usang (Dead Code)
- **Lokasi Masalah:**
  - [`resources/views/dev/layouts/app-old.blade.php`](resources/views/dev/layouts/app-old.blade.php)
  - [`resources/views/dev/layouts/app-improved.blade.php`](resources/views/dev/layouts/app-improved.blade.php)
- **Deskripsi:** Kedua file ini TIDAK digunakan oleh view manapun (tidak ada `@extends('dev.layouts.app-old')` atau `@extends('dev.layouts.app-improved')`)
- **Fix:** Hapus kedua file untuk menjaga kebersihan kode

### 5. `@method('POST')` Redundan
- **Lokasi Masalah:** [`resources/views/pos/printer-settings.blade.php:43`](resources/views/pos/printer-settings.blade.php:43)
- **Deskripsi:** POST adalah HTTP method default, `@method('POST')` tidak diperlukan
- **Fix:** Hapus `@method('POST')` dari form tersebut

---

## 🟢 IMPROVEMENT & UI/UX

### 6. Payment Views Menggunakan Layout Sederhana
- **Lokasi Masalah:**
  - [`resources/views/payments/success.blade.php`](resources/views/payments/success.blade.php)
  - [`resources/views/payments/failure.blade.php`](resources/views/payments/failure.blade.php)
  - [`resources/views/payments/confirmation.blade.php`](resources/views/payments/confirmation.blade.php)
  - [`resources/views/payments/xendit.blade.php`](resources/views/payments/xendit.blade.php)
- **Deskripsi:** Ke-4 view ini menggunakan `layouts.app` yang sangat sederhana tanpa navigasi yang memadai. User tidak bisa navigasi ke halaman lain setelah pembayaran
- **Fix:** Pertimbangkan untuk menambahkan navigasi minimal atau redirect otomatis ke dashboard

### 7. Alert Components Duplikat
- **Lokasi Masalah:**
  - [`resources/views/components/alert.blade.php`](resources/views/components/alert.blade.php) (198 baris)
  - [`resources/views/user/components/alert.blade.php`](resources/views/user/components/alert.blade.php) (198 baris)
  - [`resources/views/dev/components/alert.blade.php`](resources/views/dev/components/alert.blade.php) (165 baris)
- **Deskripsi:** Ketiga file ini hampir identik, hanya berbeda pada beberapa baris. Ini adalah code duplication
- **Fix:** Refactor menjadi satu component reusable dengan parameter

---

## 📋 TASK LIST

### Phase 1: Bug Fix (Ringan)
- [ ] Buat view `vendor/public-profile.blade.php`
- [ ] Tambah route `resolve-dispute` di `routes/web.php`
- [ ] Standarisasi bahasa di `dev/components/alert.blade.php`

### Phase 2: Clean Code (Ringan)
- [ ] Hapus `dev/layouts/app-old.blade.php`
- [ ] Hapus `dev/layouts/app-improved.blade.php`
- [ ] Hapus `@method('POST')` redundan di printer settings

### Phase 3: Improvement (Sedang)
- [ ] Perbaiki payment views navigation
- [ ] Review responsive design di semua views
- [ ] Verifikasi semua navigation links valid
- [ ] Verifikasi flow lelang lengkap
- [ ] Verifikasi flow POS
- [ ] Verifikasi flow linktree
- [ ] Verifikasi flow withdrawal

### Phase 4: Development (Besar)
- [ ] Refactor alert components menjadi reusable
- [ ] Tambah breadcrumbs navigation
- [ ] Tambah loading skeleton di dashboard
- [ ] Tambah test untuk flow yang belum tercover

---

## CATATAN PENTING

1. **PRODUCTION SAFE:** Semua perbaikan di Phase 1-3 TIDAK memerlukan perubahan database
2. **DATABASE:** Tidak ada migrasi baru yang diperlukan untuk perbaikan ini
3. **DEPLOYMENT:** Semua perubahan bisa langsung di-deploy ke production

---

## Flow Verification Checklist

### Flow Lelang
```
User Buat Lelang → Admin Approve → Vendor Bid → User Pilih Winner
→ User Bayar (Xendit) → Webhook Confirm → OrderTracking Dibuat
→ Vendor Proses → Vendor Ship → User Confirm Delivery
→ Escrow Released → Vendor Wallet Credited
```
Status: Perlu verifikasi end-to-end

### Flow POS
```
Vendor Browse Produk → Add to Cart → Checkout
→ Pilih Payment (Cash/Online) → Process Payment
→ Invoice Dibuat → Status Updated
```
Status: Perlu verifikasi end-to-end

### Flow Linktree
```
Vendor Atur Profil → Tambah Links → Pilih Template → Publish
→ User Buka /l/custom-url → Lihat Halaman → Klik Link / Bayar QRIS
→ Xendit Webhook → Transaksi Tercatat
```
Status: Perlu verifikasi end-to-end

### Flow Withdrawal
```
Vendor Request Withdrawal → Admin Review → Admin Approve/Reject
→ Jika Approve: Process Payment → Vendor Terima Dana
```
Status: Perlu verifikasi end-to-end

# Rencana Audit & Fix: Grafika-Printing
**Tanggal:** 5 Agustus 2026
**Status:** Draft - Menunggu Persetujuan

---

## Ringkasan Temuan Audit

Setelah melakukan pengecekan menyeluruh terhadap seluruh kode, routes, controllers, views, dan models, ditemukan beberapa bug dan area yang perlu diperbaiki. Berikut adalah rencana kerja yang diurutkan berdasarkan prioritas (ringan dulu, baru besar).

---

## Phase 1: Bug Fixes (KRITIS - Harus Diperbaiki)

### 1.1 Route Name Mismatch di Order Tracking Vendor
**File:** [`resources/views/vendor/order-tracking/index.blade.php`](resources/views/vendor/order-tracking/index.blade.php:86)
**Bug:** View menggunakan route name `vendor.order-tracking.update-status` tapi route yang terdaftar adalah `vendor.tracking.update`
**Impact:** Vendor TIDAK BISA mengupdate status order tracking - form submission akan gagal dengan error "Route not found"
**Fix:** Ganti `route('vendor.order-tracking.update-status', $tracking)` menjadi `route('vendor.tracking.update', $tracking)`

### 1.2 `$this->getStatusColor()` Salah di Tracking Show
**File:** [`resources/views/user/tracking/show.blade.php`](resources/views/user/tracking/show.blade.php:16)
**Bug:** Menggunakan `$this->getStatusColor()` yang tidak valid di Blade template. Fungsi `getStatusColor()` didefinisikan lokal di file yang sama (line 305) menggunakan `@php`
**Impact:** Error "Method does not exist" saat user melihat detail tracking
**Fix:** Ganti `$this->getStatusColor($auction->transaksi->tracking_status)` menjadi `getStatusColor($auction->transaksi->tracking_status)`

### 1.3 Konsistensi `getStatusColor()` di Tracking Views
**File:** [`resources/views/vendor/tracking/index.blade.php`](resources/views/vendor/tracking/index.blade.php:52)
**Issue:** Fungsi `getStatusColor()` didefinisikan ulang di setiap view file (duplikasi code)
**Impact:** Maintenance burden - jika ada perubahan warna status, harus diupdate di banyak tempat
**Recommendation:** Pertimbangkan untuk menggunakan accessor di model `OrderTracking` (`getStatusColorAttribute`) yang sudah ada di [`app/Models/OrderTracking.php`](app/Models/OrderTracking.php:81)

---

## Phase 2: Clean Code & Best Practice

### 2.1 Duplikasi Fungsi `getStatusColor()` di Views
**Files:**
- [`resources/views/vendor/tracking/index.blade.php`](resources/views/vendor/tracking/index.blade.php:376) - line 376
- [`resources/views/user/tracking/show.blade.php`](resources/views/user/tracking/show.blade.php:306) - line 306
- [`resources/views/user/tracking/index.blade.php`](resources/views/user/tracking/index.blade.php:115) - line 115

**Fix:** Gunakan accessor `->status_color` dari model `OrderTracking` atau buat Blade component/partials

### 2.2 Duplikasi Fungsi `getProgressPercentage()` di Views
**File:** [`resources/views/user/tracking/index.blade.php`](resources/views/user/tracking/index.blade.php:54)
**Issue:** Fungsi helper didefinisikan lokal di view
**Fix:** Pindahkan ke service, model accessor, atau Blade component

### 2.3 Cek Error Handling di Controllers
**Files to review:**
- [`app/Http/Controllers/XenditWebhookController.php`](app/Http/Controllers/XenditWebhookController.php) - Pastikan semua webhook event ditangani dengan benar
- [`app/Http/Controllers/DeliveryConfirmationController.php`](app/Http/Controllers/DeliveryConfirmationController.php) - Pastikan error handling untuk photo upload
- [`app/Http/Controllers/AuctionController.php`](app/Http/Controllers/AuctionController.php) - Pastikan error handling untuk payment flow

### 2.4 Cek Input Validation di Controllers
**Files to review:**
- [`app/Http/Controllers/vendor/pos/CheckoutController.php`](app/Http/Controllers/vendor/pos/CheckoutController.php) - Validasi cart items
- [`app/Http/Controllers/vendor/pos/PaymentController.php`](app/Http/Controllers/vendor/pos/PaymentController.php) - Validasi payment data
- [`app/Http/Controllers/vendor/LinktreeController.php`](app/Http/Controllers/vendor/LinktreeController.php) - Validasi custom URL

---

## Phase 3: Responsive Design & UI/UX

### 3.1 Cek Responsive Design di Halaman POS
**Files:**
- [`resources/views/pos/pos-home.blade.php`](resources/views/pos/pos-home.blade.php) - Product grid responsive
- [`resources/views/pos/cart.blade.php`](resources/views/pos/cart.blade.php) - Cart table responsive
- [`resources/views/pos/checkout.blade.php`](resources/views/pos/checkout.blade.php) - Checkout form responsive
- [`resources/views/pos/payment-options.blade.php`](resources/views/pos/payment-options.blade.php) - Payment options responsive

### 3.2 Cek Responsive Design di Halaman Vendor
**Files:**
- [`resources/views/vendor/order-tracking/index.blade.php`](resources/views/vendor/order-tracking/index.blade.php) - Table responsive
- [`resources/views/vendor/linktree/index.blade.php`](resources/views/vendor/linktree/index.blade.php) - Linktree list responsive
- [`resources/views/vendor/wallet/index.blade.php`](resources/views/vendor/wallet/index.blade.php) - Wallet dashboard responsive

### 3.3 Cek Responsive Design di Halaman User
**Files:**
- [`resources/views/user/auctions/index.blade.php`](resources/views/user/auctions/index.blade.php) - Auction list responsive
- [`resources/views/user/tracking/index.blade.php`](resources/views/user/tracking/index.blade.php) - Tracking cards responsive
- [`resources/views/user/delivery-confirmation/index.blade.php`](resources/views/user/delivery-confirmation/index.blade.php) - Delivery list responsive

### 3.4 Cek Responsive Design di Halaman Admin
**Files:**
- [`resources/views/dev/auctions/index.blade.php`](resources/views/dev/auctions/index.blade.php) - Auction management responsive
- [`resources/views/dev/wallets/index.blade.php`](resources/views/dev/wallets/index.blade.php) - Wallet management responsive

---

## Phase 4: Navigation & UX Improvements

### 4.1 Cek Konsistensi Navigation di Vendor Layout
**File:** [`resources/views/layouts/vendor.blade.php`](resources/views/layouts/vendor.blade.php)
**Check:**
- Semua nav items terhubung ke route yang benar
- Active state detection berfungsi
- Mobile menu berfungsi dengan baik

### 4.2 Cek Konsistensi Navigation di User Layout
**File:** [`resources/views/layouts/user.blade.php`](resources/views/layouts/user.blade.php)
**Check:**
- Semua nav items terhubung ke route yang benar
- Active state detection berfungsi
- Mobile menu berfungsi dengan baik

### 4.3 Cek Konsistensi Navigation di Admin Layout
**File:** [`resources/views/dev/layouts/app.blade.php`](resources/views/dev/layouts/app.blade.php)
**Check:**
- Semua nav items terhubung ke route yang benar
- Active state detection berfungsi
- Mobile menu berfungsi dengan baik

---

## Phase 5: Flow Fitur Check

### 5.1 Flow Auction
**Check:**
- User buat auction → Admin approve → Vendor bid → User pilih winner → User bayar (Xendit) → Webhook confirm → Order tracking dibuat
- Pastikan semua view dan controller terhubung dengan benar

### 5.2 Flow POS
**Check:**
- Vendor browse produk → Add to cart → Checkout → Pilih payment → Process → Invoice
- Pastikan cart session berfungsi
- Pastikan payment processing benar

### 5.3 Flow Linktree
**Check:**
- Vendor atur profil → Tambah links → Pilih template → Publish → User buka /l/custom-url
- Pastikan A/B testing berfungsi
- Pastikan QRIS payment berfungsi

---

## Catatan Penting

### Yang TIDAK BOLEH Dilakukan
- ❌ Update database schema yang sudah ada di production
- ❌ Menambah migration baru yang mengubah struktur tabel existing
- ❌ Menghapus kolom atau tabel yang sudah ada

### Yang BOLEH Dilakukan
- ✅ Fix bug di views (route name, function calls)
- ✅ Fix bug di controllers (error handling, validation)
- ✅ Update responsive design
- ✅ UI/UX improvements
- ✅ Clean code improvements
- ✅ Navigation fixes

---

## Urutan Pengerjaan

1. **Phase 1: Bug Fixes** - Fix 2 bug kritokus terlebih dahulu
2. **Phase 2: Clean Code** - Refactor duplikasi kode
3. **Phase 3: Responsive Design** - Cek dan perbaiki responsive di semua halaman
4. **Phase 4: Navigation** - Cek dan perbaiki navigation
5. **Phase 5: Flow Check** - Verifikasi semua flow fitur berfungsi

---

## Estimasi File yang Perlu Diubah

### Phase 1 (Bug Fixes):
1. `resources/views/vendor/order-tracking/index.blade.php` - 1 baris
2. `resources/views/user/tracking/show.blade.php` - 1 baris

### Phase 2 (Clean Code):
3. `resources/views/vendor/tracking/index.blade.php` - Refactor getStatusColor
4. `resources/views/user/tracking/index.blade.php` - Refactor getStatusColor
5. `resources/views/user/tracking/show.blade.php` - Refactor getStatusColor

### Phase 3-5 (Tergantung hasil pengecekan):
- Akan ditentukan setelah pengecekan lebih lanjut

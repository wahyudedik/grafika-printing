# AGENT.md - Panduan untuk AI Agent

> Dokumen ini memberikan konteks lengkap untuk AI agent yang bekerja di projek Grafika-Printing. Baca dokumen ini sebelum melakukan perubahan apapun.

---

## Overview Projek

**Grafika-Printing** adalah platform multi-tenant untuk bisnis percetakan Indonesia. Dibangun dengan **Laravel 13** (di-upgrade dari Laravel 11 pada Agustus 2026), menggunakan arsitektur shared-database multi-tenant.

- **Production URL:** https://grafika.noteds.com
- **Bahasa Kode:** PHP 8.2+, Blade Templates, JavaScript (minimal)
- **Database:** MySQL (database: `grafika_printing`)
- **Package Manager:** Composer (PHP), npm (JS)

---

## Struktur Direktori

```
grafika-printing/
├── app/
│   ├── Console/Commands/          # Artisan commands (~30+ commands)
│   ├── Facades/
│   │   └── Tenant.php             # Tenant facade
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/             # Controller admin (14 files)
│   │   │   ├── Api/               # API controllers
│   │   │   ├── Auth/              # Auth controllers (Breeze)
│   │   │   ├── vendor/            # Controller vendor (termasuk pos/, auction/)
│   │   │   └── *.php             # Controller root (Auction, Profile, dll)
│   │   ├── Middleware/            # 8 middleware files
│   │   └── Requests/             # Form requests
│   ├── Mail/                     # Email classes
│   ├── Models/
│   │   ├── User/UserTenantModel.php  # Base model untuk model global
│   │   ├── Vendor/
│   │   │   ├── TenantModel.php       # Base model untuk model tenant
│   │   │   └── *.php                # Model tenant (Produk, Transaksi, dll)
│   │   └── *.php                    # Model global (Auction, Vendor, dll)
│   ├── Notifications/            # Notification classes
│   ├── Providers/                # Service providers
│   ├── Services/                 # Business logic services (14 files)
│   ├── Traits/                   # HasUuid, HasVendorContext traits
│   └── View/Components/          # Blade components
├── config/
│   ├── multitenancy.php          # Spatie multitenancy config
│   ├── services.php              # External services config (Xendit, RajaOngkir)
│   └── *.php                    # Config lainnya
├── database/
│   ├── migrations/               # ~50+ migrations
│   │   ├── landlord/             # Landlord migrations
│   │   └── *.php                # Tenant migrations
│   └── seeders/                  # Database seeders
├── public/                       # Public assets
│   ├── produk_gambar/            # Product images
│   └── vendors_logo/             # Vendor logos
├── resources/
│   ├── css/                      # Custom CSS
│   ├── js/                       # Custom JavaScript
│   └── views/
│       ├── admin/                # Admin views
│       ├── dev/                  # Developer admin views
│       ├── vendor/               # Vendor views
│       ├── user/                 # User views
│       ├── pos/                  # POS views
│       ├── payments/             # Payment views
│       ├── laporan/              # Report views (termasuk pdf/)
│       ├── layouts/              # Layout templates
│       └── components/           # Shared Blade components
├── routes/
│   ├── web.php                   # Web routes (~621 baris)
│   ├── api.php                   # API routes
│   ├── auth.php                  # Auth routes (Breeze)
│   └── console.php               # Console routes
└── storage/                      # Laravel storage
```

---

## Arsitektur Multi-Tenant

Ini adalah bagian paling kritis dari projek. **Pahami ini sebelum mengubah apapun.**

### Cara Kerja

```
Request masuk
    → SetTenantContext middleware (lihat user->usertype)
        → vendor: Set tenant context via Tenant facade
        → user: Set user context
        → dev/admin: Global access (no tenant filter)
    → Controller method
        → Model query otomatis di-filter by vendor_id
```

### Base Models

1. **[`TenantModel`](app/Models/Vendor/TenantModel.php)** - Base untuk model tenant
   - Auto-fill `vendor_id` saat creating
   - Prevent `vendor_id` change saat saving
   - Auto-filter query by `vendor_id` via global scope

2. **[`UserTenantModel`](app/Models/User/UserTenantModel.php)** - Base untuk model global
   - Digunakan oleh Auction, OrderTracking, EscrowPayment, dll

### Jika Menambah Model Baru

**Untuk data per-vendor (tenant):**
```php
// Buat model extend TenantModel
namespace App\Models\Vendor;

class NamaModel extends TenantModel
{
    protected $fillable = ['field1', 'field2'];
    // vendor_id OTOMATIS diisi, jangan tambah ke fillable
}

// Migration harus include vendor_id
$table->foreignId('vendor_id')->constrained('vendors');
$table->index('vendor_id');
```

**Untuk data global:**
```php
// Buat model extend UserTenantModel
namespace App\Models;

class NamaModel extends UserTenantModel
{
    protected $fillable = ['user_id', 'vendor_id', 'field1'];
    // vendor_id sebagai field biasa, bukan auto-fill
}
```

---

## Middleware

| Middleware | File | Fungsi |
|-----------|------|--------|
| `dev` | [`DevMiddleware`](app/Http/Middleware/DevMiddleware.php) | Hanya allow `usertype=dev` |
| `vendor` | [`VendorMiddleware`](app/Http/Middleware/VendorMiddleware.php) | Allow `usertype=vendor`, cek vendor relationship |
| `user` | [`UserMiddleware`](app/Http/Middleware/UserMiddleware.php) | Allow `usertype=user` |
| `tenants` | [`SetTenantContext`](app/Http/Middleware/SetTenantContext.php) | Set tenant context berdasarkan user type |
| `security.headers` | [`SecurityHeaders`](app/Http/Middleware/SecurityHeaders.php) | X-Frame-Options, CSP, dll |
| `input.sanitize` | [`InputSanitizer`](app/Http/Middleware/InputSanitizer.php) | Sanitasi input XSS |

### Route Groups

```php
// Admin routes
Route::middleware(['auth', 'verified', 'dev'])->prefix('admin')->...

// Vendor routes (perlu tenant context)
Route::middleware(['auth', 'verified', 'vendor', 'tenants'])->prefix('vendor')->...

// User routes
Route::middleware(['auth', 'verified', 'user'])->prefix('user')->...
```

---

## Services (Business Logic)

| Service | File | Fungsi | Status |
|---------|------|--------|--------|
| `TenantManager` | [`TenantManager`](app/Services/TenantManager.php) | Kelola context tenant | ✅ Ada |
| `XenditService` | [`XenditService`](app/Services/XenditService.php) | Create payment link, webhook, QRIS | ✅ Ada |
| `XenditBalanceService` | [`XenditBalanceService`](app/Services/XenditBalanceService.php) | Cek saldo Xendit | ✅ Ada |
| `AdminFeeService` | [`AdminFeeService`](app/Services/AdminFeeService.php) | Kalkulasi admin fee | ✅ Ada |
| `RajaOngkirService` | [`RajaOngkirService`](app/Services/RajaOngkirService.php) | Hitung ongkir | ✅ Ada |
| `ShippingTrackingService` | [`ShippingTrackingService`](app/Services/ShippingTrackingService.php) | Track pengiriman | ✅ Ada |
| `OrderTrackingService` | [`OrderTrackingService`](app/Services/OrderTrackingService.php) | Order tracking, escrow | ✅ Ada |
| `AuctionToPosService` | [`AuctionToPosService`](app/Services/AuctionToPosService.php) | Auction ke POS | ✅ Ada |
| `SecurityService` | [`SecurityService`](app/Services/SecurityService.php) | Input validation | ✅ Ada |
| `EncryptionService` | [`EncryptionService`](app/Services/EncryptionService.php) | Enkripsi data sensitif | ✅ Ada |
| `AuditLogService` | [`AuditLogService`](app/Services/AuditLogService.php) | Audit logging | ✅ Ada |
| `AuthorizationService` | [`AuthorizationService`](app/Services/AuthorizationService.php) | Role & permission checking | ✅ Ada |
| `FileUploadService` | [`FileUploadService`](app/Services/FileUploadService.php) | File upload management | ✅ Ada |
| `ServiceConfigOverride` | [`ServiceConfigOverride`](app/Services/ServiceConfigOverride.php) | Service config override | ✅ Ada |

### API Versioning
- Semua API routes sudah di-version ke `/api/v1/`
- Backward compatibility: old paths redirect ke v1 (301/307)
- Rate limiting: 60 req/min untuk API, 5 req/min untuk auth
- Xendit webhook tetap di `/api/xendit/webhook` (no redirect)
- Configured di [`bootstrap/app.php`](bootstrap/app.php)

### Performance
- JS Lazy Loading: ApexCharts, Chart.js, SortableJS via dynamic import
- ActiveLinktree Caching: 1 jam TTL via `getActiveLinktreeCached()`
- Wallet query optimization: `withCount` + limited relationship

---

## Fitur Status

### ✅ Sudah Selesai

#### Xendit Payment Gateway
- [`XenditService`](app/Services/XenditService.php) sudah fully integrated
- Support: QRIS, VA, E-Wallet, Convenience Store
- Webhook handling sudah robust

#### API Versioning
- Semua API routes sudah di-version ke `/api/v1/`
- Backward compatibility: old paths redirect ke v1 (301/307)
- Rate limiting: 60 req/min API, 5 req/min auth
- Xendit webhook tetap di `/api/xendit/webhook`

#### Linktree Module
- Models: [`Linktree`](app/Models/Vendor/Linktree.php), [`LinktreeLink`](app/Models/Vendor/LinktreeLink.php), [`LinktreeSocial`](app/Models/Vendor/LinktreeSocial.php), [`LinktreeProduct`](app/Models/Vendor/LinktreeProduct.php) (extend TenantModel)
- Controllers: [`LinktreeController`](app/Http/Controllers/vendor/LinktreeController.php), [`LinktreePublicController`](app/Http/Controllers/LinktreePublicController.php)
- Views: `resources/views/vendor/linktree/`, `resources/views/linktree/public/`
- Routes: `/vendor/linktree/*` (vendor), `/l/{customUrl}` (public)
- Caching: `getActiveLinktreeCached()` — 1 jam TTL
- Migration: `2026_08_23_000001_add_vendor_id_to_linktree_products_table.php` — tambah `vendor_id` ke `linktree_products`

#### Template Builder
- Pilihan template: minimal, colorful, dark, professional, gradient, nature, neon, elegant
- Color picker, banner & avatar upload, button style configuration
- Live preview

#### Deployment Scripts
- [`deploy.sh`](deploy.sh) (first-time deployment)
- [`update.sh`](update.sh) (update deployment)
- Berdasarkan panduan di [`VPS_DEPLOYMENT_GUIDE.md`](VPS_DEPLOYMENT_GUIDE.md)

#### Production Seeders
- [`ProductionSeeder`](database/seeders/ProductionSeeder.php) — fresh install seeder
- [`ComprehensiveTestDataSeeder`](database/seeders/ComprehensiveTestDataSeeder.php) — testing data seeder
- [`PosCompleteSeeder`](database/seeders/PosCompleteSeeder.php) — Data lengkap POS (bahan, spesifikasi, produk, wholesale) — 10 kategori, 15 bahan, 10 spesifikasi, 6 alat, 10 produk, ~60 spesifikasi produk, 25 bahan-spesifikasi pivots, 25 estimasi produksi, 20 wholesale prices, 5 pelanggan, 1 printer setting

#### POS System
- **Controller:** [`PosController`](app/Http/Controllers/vendor/pos/PosController.php), [`CheckoutController`](app/Http/Controllers/vendor/pos/CheckoutController.php)
- **Views:** `resources/views/pos/` (cart, checkout, cash-payment, online-payment, payment-options, payment-success, payment-failure, print-invoice, thermal-print, printer-settings, pos-home)
- **Cart System:** Session-based cart dengan Alpine.js interactivity
- **Bahan/Finishing/Ukuran Calculation:** Harga dihitung berdasarkan kombinasi bahan, spesifikasi finishing, dan ukuran
- **Wholesale Pricing:** Tiered pricing berdasarkan quantity (20 tier rules per vendor)
- **Thermal Print Support:** Printer settings (nama, alamat, no. telepon), thermal print template 58mm/80mm
- **Multiple Payment:** Cash (tunai + kembalian otomatis) dan Online (Xendit QRIS, VA, E-Wallet)
- **HPP Calculation:** Kalkulasi Harga Pokok Penjualan berdasarkan komponen bahan dan estimasi produksi (menggunakan `ceil()` untuk pembulatan yang benar)
- **Pelanggan Management:** CRUD pelanggan, data kontak, riwayat transaksi
- **Optimized Checkout:** Triple price calculation sudah dieliminasi (dari 50+ queries ke ~10 queries)

#### User Lelang Management
- Model `LelangUserProfile` dengan scopes, status management, auto-assign
- `UserLelangController` untuk admin (CRUD + verify/suspend/reactivate)
- Dashboard khusus user lelang: `/user/lelang-dashboard`
- Views admin: [`resources/views/dev/user-lelang/`](resources/views/dev/user-lelang/) (index, show, create, edit)
- Routes: `/admin/user-lelang/*`

#### COD Ongkos Kirim
- Flow COD sudah lengkap dengan rincian: harga barang + ongkir
- UI enhancement: rincian breakdown harga di checkout

#### Admin Notification System
- Controller: [`AdminNotificationController`](app/Http/Controllers/AdminNotificationController.php) (index, markAllRead, markAsRead)
- View: [`resources/views/dev/notifications/index.blade.php`](resources/views/dev/notifications/index.blade.php) — extend admin layout
- Routes: `admin.notifications.index`, `admin.notifications.markAllRead`, `admin.notifications.markAsRead`
- Dropdown notification di admin layout sudah dynamic + mark-as-read + link "Lihat Semua"

#### Privacy — Audit Log Sanitization
- [`AuditLogService::sanitizeSensitiveData()`](app/Services/AuditLogService.php) mem-mask field sensitif sebelum logging
- Field yang di-mask: password, remember_token, api_key, api_secret, token, xendit_api_key, xendit_webhook_token, rajaongkir_api_key, app_key
- Diterapkan di: `logCreated`, `logUpdated`, `logDeleted`, `logFinancialTransaction`

#### Bug Fixes & Improvements (23 Agustus 2026)
- **`LinktreeProduct` → TenantModel**: Model diubah dari `extends Model` ke `extends TenantModel` + tambah `vendor()` relation
- **Migration**: `2026_08_23_000001_add_vendor_id_to_linktree_products_table.php`
- **`CheckoutController`**: Triple price calculation dieliminasi (50+ queries → ~10 queries)
- **`PosController`**: Duplicate category query dihapus
- **`SecurityService`**: `openssl_encrypt/decrypt` → `Crypt::encryptString()/decryptString()`
- **Transaction code**: `rand(1000,9999)` → sequence-based (`TRX-{Ymd}-{vendor_id}-{sequence}`)
- **`AuctionController::closeAuction()`**: Bid ownership validation ditambahkan
- **`CheckoutController`**: `payment_amount` required untuk cash payment
- **`PriceCalculationService`**: `(int)` → `ceil()` untuk float-to-int truncation fix
- **`LinktreeController::destroy()`**: Cascade delete untuk `linktreeProducts()` dan `abTests()`
- **`TransaksiController::update()`**: Kalkulasi ulang `hpp_total` dan `laba_total` setelah edit
- **Navigation**: User sidebar "Dasbor Lelang", vendor sidebar Linktree sub-menu, admin sidebar bahasa Indonesia
- **View fixes**: `style="display: none;"` → `x-show` + `x-cloak` (4 POS views), Bootstrap → Tailwind classes

#### Clean Code & Performance — Batch 2 & 3 (23 Agustus 2026)
- **DRY status config**: Array `$statusConfig` diekstrak dari duplikasi status colors/labels di `transaksi/index` dan `order-tracking/index` views
- **Linktree order price validation**: `LinktreePublicController` menambahkan validasi `unit_price` vs actual product price (toleransi 1 rupiah) untuk mencegah price manipulation
- **TransaksiController items validation**: Validasi items array saat update (is_array, non-empty, produk_id required, kuantitas min 1)
- **PosController N+1 fix**: Loop `SpesifikasiProduk::find()` + `Bahan::find()` diganti eager load batch `whereIn()` + `keyBy()`
- **TransaksiController eager loading**: HPP recalculation menggunakan `TransaksiItem::with('transaksiItemSpecifications')` eager load
- **Error response standardization**: `LinktreeController::updateOrderStatus()` dan `updatePaymentStatus()` diubah ke `FlashMessage::backSuccess()`
- **Focus ring consistency**: `focus:ring-blue-500` → `focus:ring-primary` di `transaksi/index` (4 tempat)

#### Batch 4 — Performance & UI Fixes (23 Agustus 2026)
- **Tailwind custom colors `danger`/`success`**: Ditambahkan ke `theme.extend.colors` di [`tailwind.config.js`](tailwind.config.js) — class seperti `bg-danger`, `text-success` sekarang di-generate oleh Tailwind
- **N+1 fix CheckoutController (3 locations)**: Batch load `Produk` dan `EstimasiProduk` sebelum loop di `processCheckout()`, `show()`, dan `calculateEstimatedCompletion()`
- **N+1 fix PosController::addToCart()**: Batch load `SpesifikasiProduk` dan `Bahan` sebelum loop validasi stok
- **Linktree public page — Alpine.js conversion**: QRIS loading/result/error sections dikonversi dari vanilla JS `display:none` ke Alpine.js `x-show` + `x-cloak` dengan `qrisState` state management
- **Admin views — Hardcoded URLs fix**: Hardcoded URL strings diganti ke `{{ url() }}` helper di `dev/wallets/index.blade.php` dan `dev/delivery/index.blade.php`
- **VendorControllerTest — Flaky test fix**: Tambah explicit `actingAs()` dan `vendorUser()->attach()` untuk test isolation

#### Batch 5 — Critical Bug Fixes (23 Agustus 2026)
- **POS Printer Settings:** Fix `resetDefaults()` crash — checkbox `id` attributes ditambahkan ke [`printer-settings.blade.php`](resources/views/pos/printer-settings.blade.php) agar `getElementById()` berfungsi (autoPrint, autoClose, autoCut)
- **Linktree Public:** Product modal dipindahkan ke dalam Alpine.js `x-data` scope di [`linktree/public.blade.php`](resources/views/linktree/public.blade.php) — sebelumnya di luar scope, Alpine.js tidak bisa mengontrol modal
- **POS Checkout:** Hapus Alpine.js v2 internal API (`__x`) di [`checkout.blade.php`](resources/views/pos/checkout.blade.php) — gunakan CustomEvent `close-modal` sebagai pengganti yang kompatibel dengan Alpine.js v3
- **VendorControllerTest:** Fix flaky test — eksplisit `'is_active' => true` di factory untuk menghindari global scope filter
- **VendorController::destroy():** Tambah logging dan better error handling di [`VendorController.php`](app/Http/Controllers/VendorController.php)

#### Test Coverage
- **546/546 passed (0 failed, 4 skipped), 1482 assertions** — coverage: Linktree CRUD, Vendor products, Transactions, Webhook auth, Multi-tenant isolation, POS, Wallet, Auction, Unit tests
- Coverage: Linktree CRUD, Vendor products, Transactions, Webhook auth, Multi-tenant isolation, POS, Wallet, Auction, Unit tests
- Termasuk `PosFlowTest` untuk integrasi POS end-to-end

---

## Flow Kritis

### Flow Auction dengan Xendit
```
User buat Auction → Admin Approve → Vendor Bid → User Pilih Winner
→ User Bayar (Xendit) → Webhook Confirm → OrderTracking Dibuat
→ Vendor Proses → Vendor Ship → User Confirm Delivery
→ Escrow Released → Vendor Wallet Credited
```

### Flow POS
```
Vendor Browse Produk → Add to Cart (session-based)
→ Bahan/Finishing/Ukuran Selection → Harga Dihitung
→ Wholesale Pricing (tiered) → Checkout
→ Pilih Payment:
  → Cash: Tunai + Kembalian Otomatis → Invoice
  → Online: Xendit (QRIS/VA/E-Wallet) → Webhook Confirm → Invoice
→ Invoice Dibuat (standar/thermal) → Status Updated
```

### Flow Linktree
```
Vendor Atur Profil → Tambah Links → Pilih Template → Publish
→ User Buka /l/custom-url → Lihat Halaman → Klik Link / Bayar QRIS
→ Xendit Webhook → Transaksi Tercatat
```

---

## Naming Convention

### File & Class
- **Model:** Singular PascalCase (e.g., `Produk`, `Transaksi`, `VendorWallet`, `Linktree`)
- **Controller:** Singular PascalCase + Controller suffix (e.g., `ProdukController`)
- **Service:** Singular PascalCase + Service suffix (e.g., `XenditService`, `RajaOngkirService`)
- **Migration:** Snake case dengan timestamp

### Route Names
- **Admin:** `admin.resource.action` (e.g., `admin.auctions.approve`)
- **Vendor:** `vendor.resource.action` (e.g., `vendor.pos.addToCart`, `vendor.linktree.update`)
- **User:** `user.resource.action` (e.g., `user.auctions.payment`)
- **Public:** `linktree.public` (e.g., `/l/{customUrl}`)

### Database Tables
- **Tenant tables:** Plural snake_case (e.g., `produks`, `transaksis`, `linktrees`)
- **Global tables:** Plural snake_case (e.g., `auctions`, `vendors`, `xendit_payments`)

---

## Konfigurasi Penting

### Services (config/services.php)
```php
'xendit' => [
    'api_key' => env('XENDIT_API_KEY'),
    'public_key' => env('XENDIT_PUBLIC_KEY'),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    'base_url' => env('XENDIT_BASE_URL'),
],
'rajaongkir' => [
    'api_key' => env('RAJAONGKIR_API_KEY'),
    'base_url' => env('RAJAONGKIR_BASE_URL'),
],
```

### CSRF Exclusion
```php
// bootstrap/app.php
->validateCsrfTokens(except: [
    'xendit/webhook',
])
```

---

## Tips Bekerja dengan Kode Ini

### 1. Selalu Periksa Tenant Context
Jika menambah query di controller vendor, pastikan model menggunakan `TenantModel` atau ada manual filter by `vendor_id`.

### 2. Jangan Ubah vendor_id
`TenantModel` mencegah perubahan `vendor_id`. Jangan pernah manually set `vendor_id` kecuali memang diperlukan.

### 3. Webhook Handling
- Xendit webhook: route `xendit/webhook` dan `webhooks/xendit`
- Webhook di-exclude dari auth dan CSRF

### 4. File Upload
- Produk gambar: `public/produk_gambar/`
- Vendor logo: `public/vendors_logo/`
- Linktree avatar: `public/linktree/avatars/`
- Linktree banner: `public/linktree/banners/`
- Gunakan `Storage::disk('public')->put()` atau `move()` untuk upload

### 5. Database
- Selalu cek migration sebelum membuat model baru
- Foreign key ke `vendors` table untuk tenant tables
- Tambah index pada `vendor_id` untuk performa

### 6. View Templates
- Layout admin: [`resources/views/dev/layouts/app.blade.php`](resources/views/dev/layouts/app.blade.php)
- Layout vendor: [`resources/views/layouts/vendor.blade.php`](resources/views/layouts/vendor.blade.php)
- Layout user: [`resources/views/layouts/user.blade.php`](resources/views/layouts/user.blade.php)
- Guest: [`resources/views/layouts/guest.blade.php`](resources/views/layouts/guest.blade.php)
- Linktree public: [`resources/views/linktree/public.blade.php`](resources/views/linktree/public.blade.php)

### 7. Frontend — Tailwind CSS
- **Tailwind CSS** digunakan untuk seluruh styling (migrasi dari Bootstrap Tabler — Agustus 2026)
- **Alpine.js** untuk interaktivitas client-side (menggantikan Bootstrap JS)
- **FontAwesome** untuk ikon — import via `@fortawesome/fontawesome-free` di `resources/css/app.css`
- **Vite** untuk build assets (`npm run dev` atau `npm run build`)
- **SweetAlert2** via npm (bukan CDN)
- **ApexCharts** via npm — global di `resources/js/app.js` (`window.ApexCharts`)
- **Chart.js** via npm — global di `resources/js/app.js` (`window.Chart`)
- **SortableJS** via npm — global di `resources/js/app.js` (`window.Sortable`)
- Linktree public page: gunakan Tailwind CSS standalone (tanpa vendor layout)
- **⚠️ Jangan gunakan CDN** untuk Tailwind, FontAwesome, atau JS libraries — semua sudah via npm/Vite build

### 7.1 Tailwind CSS Guidelines
- Gunakan **utility classes** Tailwind, hindari custom CSS kecuali sangat perlu
- Config: [`tailwind.config.js`](tailwind.config.js) — custom primary colors (blue palette), danger (red shades), success (green shades), Inter font, `@tailwindcss/forms` plugin
- **Custom colors tersedia**: `bg-danger`, `text-danger`, `bg-success`, `text-success` — digunakan di POS views untuk status styling
- **UI Components** (`resources/views/components/ui/`): gunakan `<x-ui.button>`, `<x-ui.card>`, `<x-ui.modal>`, `<x-ui.empty-state>`, `<x-ui.badge>`, `<x-ui.dropdown>`, dll
- **x-cloak**: Selalu gunakan `x-cloak` pada Alpine.js elements (bukan `style="display: none"`)
- **Alpine.js** untuk interaktivitas: `x-data`, `x-on:click`, `x-show`, `x-transition`, `x-model`
- **Responsive**: gunakan `sm:`, `md:`, `lg:`, `xl:` prefixes
- **Dark mode**: belum diaktifkan (opsional untuk enhancement mendatang)
- **Contoh pattern modal:**
  ```html
  <div x-data="{ open: false }">
      <button @click="open = true" class="btn-primary">Buka</button>
      <div x-show="open" x-transition class="modal-backdrop">
          <div class="modal-content" @click.outside="open = false">
              ...
          </div>
      </div>
  </div>
  ```
- **Contoh pattern dropdown:**
  ```html
  <div x-data="{ open: false }" class="relative">
      <button @click="open = !open">Menu</button>
      <div x-show="open" @click.outside="open = false" x-transition class="dropdown-menu">
          ...
      </div>
  </div>
  ```

### 8. Testing
- Test files di `tests/Feature/` dan `tests/Unit/`
- **546/546 passed (0 failed, 4 skipped), 1482 assertions** — coverage: Linktree, Vendor, Transactions, Webhook, Multi-tenant, POS, Wallet, Auction, Unit tests
- Jalankan: `php artisan test`
- Untuk test spesifik: `php artisan test --filter=NamaTest`
- Jalankan dengan coverage: `php artisan test --coverage`
- **Tip**: Untuk menghindari flaky tests, gunakan explicit `actingAs()` dan setup vendor relationship di setiap test method

### 9. API Versioning
- Semua API routes sudah di-version ke `/api/v1/`
- Old paths redirect ke v1 (301/307) — jangan buat route baru di old path
- Xendit webhook tetap di `/api/xendit/webhook` (no versioning)
- Rate limiting: 60 req/min API, 5 req/min auth — configured di `bootstrap/app.php`

### 10. JS Lazy Loading
- ApexCharts, Chart.js, SortableJS di-load via dynamic import (bukan static import)
- Pattern: `const ApexCharts = (await import('apexcharts')).default`
- Hanya gunakan di halaman yang membutuhkan chart/sortable
- Jangan tambahkan CDN script tag — semua sudah via npm/Vite build

### 11. DRY Status Config Pattern
- Untuk views yang menampilkan status dengan colors/labels (transaksi, order-tracking), gunakan array `$statusConfig` PHP di bagian atas file
- Pattern: `$statusConfig = ['status_name' => ['color' => 'tailwind-class', 'label' => 'Label']]`
- Hindari duplikasi array status di desktop table dan mobile cards — gunakan satu `$statusConfig` untuk keduanya
- Contoh: `resources/views/transaksi/index.blade.php`, `resources/views/user/order-tracking/index.blade.php`

### 12. Eager Loading di Loop
- Hindari query di dalam loop (N+1 problem). Gunakan batch fetch dengan `whereIn()` + `keyBy()`
- Contoh: `PosController::checkPrice()` — `SpesifikasiProduk::whereIn('id', $ids)->keyBy('id')` menggantikan loop `SpesifikasiProduk::find($id)`
- Untuk HPP recalculation: gunakan `TransaksiItem::with('transaksiItemSpecifications')` eager load

---

## File yang Sering Diubah

| Kategori | File |
|----------|------|
| Routes | `routes/web.php`, `routes/api.php` |
| Config | `config/services.php`, `config/multitenancy.php` |
| Tenant Base | `app/Models/Vendor/TenantModel.php` |
| Tenant Manager | `app/Services/TenantManager.php` |
| Middleware | `app/Http/Middleware/SetTenantContext.php` |
| POS Controller | `app/Http/Controllers/vendor/pos/PosController.php`, `app/Http/Controllers/vendor/pos/CheckoutController.php` |
| POS Views | `resources/views/pos/` (cart, checkout, cash-payment, online-payment, thermal-print, printer-settings) |
| Security | `app/Services/SecurityService.php`, `app/Services/PriceCalculationService.php` |
| Auction | `app/Http/Controllers/AuctionController.php` |
| Transaction | `app/Http/Controllers/vendor/TransaksiController.php` |
| Transaction Views | `resources/views/transaksi/index.blade.php`, `resources/views/user/order-tracking/index.blade.php` |
| Linktree | `app/Http/Controllers/vendor/LinktreeController.php`, `app/Http/Controllers/LinktreePublicController.php`, `app/Models/Vendor/LinktreeProduct.php` |
| Linktree Public | `resources/views/linktree/public.blade.php` |
| Tailwind Config | `tailwind.config.js` |
| UI Components | `resources/views/components/ui/` |
| Layouts | `resources/views/layouts/vendor.blade.php`, `resources/views/layouts/user.blade.php`, `resources/views/dev/layouts/app.blade.php` |
| Admin Views | `resources/views/dev/wallets/index.blade.php`, `resources/views/dev/delivery/index.blade.php` |
| Laravel Bootstrap | `bootstrap/app.php` |
| Env | `.env` (jangan commit!) |

---

## Debugging

### Common Issues

1. **"No vendor context" error**
   - Pastikan user memiliki vendor relationship di tabel `vendor_user`
   - Cek apakah `SetTenantContext` middleware aktif di route

2. **Query mengembalikan semua data (tidak filtered)**
   - Pastikan model extend `TenantModel`, bukan `Model`
   - Cek apakah global scope ada dihapus secara tidak sengaja

3. **Webhook tidak masuk**
   - Pastikan CSRF exception untuk webhook route
   - Cek dashboard payment gateway untuk webhook URL yang benar
   - Cek log: `storage/logs/laravel.log`

4. **Payment link gagal**
   - Cek API key di `.env` (`XENDIT_API_KEY`, `XENDIT_WEBHOOK_TOKEN`)
   - Cek apakah base URL benar (production vs development)
   - Cek log untuk error detail

5. **Migration error**
   - Cek apakah tabel dependencies sudah ada
   - Pastikan MySQL version mendukung (>= 5.7)

6. **Linktree 404**
   - Cek apakah `custom_url` exists di database
   - Cek apakah route `/l/{customUrl}` terdaftar
   - Cek apakah vendor linktree `is_active = true`

7. **FontAwesome icons tidak muncul**
   - Pastikan `@import '@fortawesome/fontawesome-free/css/all.min.css';` ada di `resources/css/app.css` (sebelum `@tailwind base`)
   - Jalankan `npm run build` ulang
   - Cek browser console untuk 404 pada fontawesome CSS

8. **ApexCharts/Chart.js/SortableJS tidak defined**
   - Pastikan package sudah terinstall: `npm ls apexcharts chart.js sortablejs`
   - Pastikan import ada di `resources/js/app.js` (global window objects)
   - Jalankan `npm run build` ulang
   - Jangan tambahkan CDN script tag — semua sudah via npm

9. **Notifikasi tidak muncul di dropdown**
  - Pastikan model user memiliki `Notifiable` trait (biasanya sudah ada di model User bawaan Laravel)
  - Cek apakah tabel `notifications` ada di database (`php artisan migrate:status`)
  - Cek route notification: `vendor.notifications.index` (vendor), `user.notifications.index` (user), `admin.notifications.index` (admin)
  - Jalankan `php artisan notifications:table` jika tabel belum ada, lalu `php artisan migrate`

10. **Blade directive @section vs @push**
   - Layout vendor, user, admin menggunakan `@stack('scripts')` → child views harus pakai `@push('scripts')`
   - Layout auth menggunakan `@yield('scripts')` → child views harus pakai `@section('scripts')`
   - Jika salah pakai, script tidak akan di-render

11. **POS — Field name mismatch**
   - Pastikan field name konsisten antara model, migration, dan view (e.g., `telepon` vs `no_telp`)
   - Cek `$fillable` di model `Pelanggan` dan `Transaksi` untuk memastikan semua field yang dibutuhkan ada

12. **POS — Variable name inconsistency di payment views**
   - Pastikan variabel `transaksiItem` (bukan `transaksiItems`) konsisten di semua POS payment views
   - Cek `cash-payment.blade.php`, `online-payment.blade.php`, dan `payment-success.blade.php`

13. **POS — Stock validation**
   - Pastikan validasi stok dilakukan di dua tempat: saat `addToCart` dan saat `checkout`
   - Stok bisa berubah antara saat cart diisi dan saat checkout diproses

14. **POS — Thermal print vendor name**
   - Gunakan `config('app.name')` untuk nama bisnis di thermal print, bukan hardcoded vendor name
   - Pastikan `printer_settings` table memiliki data yang benar

15. **POS — Division by zero**
    - Hati-hati dengan `harga_satuan` calculation — pastikan quantity tidak nol sebelum membagi
    - Gunakan protective check: `$quantity > 0 ? $total / $quantity : 0`

16. **LinktreeProduct — vendor_id tidak terisi**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): LinktreeProduct sudah extend TenantModel
    - Migration: `2026_08_23_000001_add_vendor_id_to_linktree_products_table.php`
    - Jika masih error, jalankan `php artisan migrate`

17. **Checkout lambat (50+ queries)**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): Triple price calculation dieliminasi di CheckoutController
    - Sekarang hanya ~10 queries per checkout

18. **Transaction code collision**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): Sequence-based: `TRX-{Ymd}-{vendor_id}-{sequence}`
    - Tidak lagi menggunakan `rand()` yang bisa collid

19. **Harga terpotong ke bawah (float-to-int)**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): `PriceCalculationService` menggunakan `ceil()` bukan `(int)`
    - Pastikan menggunakan `ceil()` untuk pembulatan harga

20. **Tailwind class `bg-danger`/`text-success` tidak ada styling**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): Custom colors `danger` (red shades) dan `success` (green shades) ditambahkan ke `tailwind.config.js`
    - Jalankan `npm run build` ulang setelah perubahan config

21. **N+1 queries di CheckoutController loop**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): Batch load `Produk` dan `EstimasiProduk` sebelum loop di `processCheckout()`, `show()`, dan `calculateEstimatedCompletion()`

22. **Hardcoded URLs di admin views**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): Diganti ke `{{ url() }}` helper di `dev/wallets/index.blade.php` dan `dev/delivery/index.blade.php`
    - Gunakan `{{ url() }}` atau `route()` helper, jangan hardcode URL

23. **Flaky tests (VendorControllerTest)**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): Tambah explicit `actingAs()` dan `vendorUser()->attach()` untuk test isolation
    - Tip: Selalu gunakan explicit auth setup di test method, jangan bergantung pada global state

24. **POS printer settings resetDefaults() crash**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): Checkbox `id` attributes ditambahkan ke `printer-settings.blade.php` (autoPrint, autoClose, autoCut)
    - Root cause: `getElementById()` gagal karena checkbox tidak punya `id` attribute

25. **Linktree product modal tidak berfungsi**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): Modal dipindahkan ke dalam `x-data="productModal()"` scope
    - Root cause: Modal berada di luar Alpine.js `x-data` scope, sehingga Alpine.js tidak bisa mengontrol visibility

26. **POS checkout — Alpine.js v2 API deprecated**
    - ✅ **SUDAH DIPERBAIKI** (23 Agustus 2026): Hapus `__x` internal API, gunakan CustomEvent `close-modal`
    - Root cause: `__x` adalah internal API Alpine.js v2 yang tidak tersedia di v3

### Log Location
```
storage/logs/laravel.log
```

### Quick Debug Commands
```bash
# Cek routes
php artisan route:list

# Cek config
php artisan config:show services

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check migration status
php artisan migrate:status
```

---

## Environment Variables Penting

| Variable | Required | Deskripsi |
|----------|----------|-----------|
| `APP_KEY` | Ya | Laravel encryption key |
| `DB_*` | Ya | Database connection |
| `XENDIT_API_KEY` | Ya | Xendit API key |
| `XENDIT_PUBLIC_KEY` | Ya | Xendit public key |
| `XENDIT_WEBHOOK_TOKEN` | Ya | Token validasi webhook |
| `RAJAONGKIR_API_KEY` | Ya | RajaOngkir API key |
| `APP_URL` | Ya | Base URL aplikasi |
| `FORCE_HTTPS` | Disarankan | Paksa HTTPS |

> **⚠️ Security:** Jangan pernah commit `.env` ke version control. Gunakan `.env.example` sebagai template (tanpa secrets) dan `.env.production.example` untuk production deployment (menggunakan Redis untuk session/queue/cache).

---

## Workflow untuk Menambah Fitur Baru

### 1. Model & Migration
```bash
php artisan make:model Vendor/NamaModel -m
```
- Edit migration: tambah `vendor_id` foreign key + index
- Edit model: extend `TenantModel`, isi `$fillable`

### 2. Controller
```bash
php artisan make:controller vendor/NamaModelController --resource
```
- Inject tenant context jika diperlukan
- Gunakan `Tenant::getVendorId()` untuk get current vendor

### 3. Route
Tambah di `routes/web.php` dalam vendor group:
```php
Route::resource('resources', NamaModelController::class);
```

### 4. Views
- Buat di `resources/views/vendor/nama-model/`
- Extend layout vendor
- Gunakan **Tailwind CSS** utility classes (bukan Bootstrap classes)
- Gunakan **Alpine.js** untuk interaktivitas (bukan Bootstrap JS)
- Gunakan UI components: `<x-ui.button>`, `<x-ui.card>`, `<x-ui.modal>`, `<x-ui.input>`, `<x-ui.alert>`, dll
- **Contoh view baru:**
  ```blade
  @extends('layouts.vendor')
  @section('content')
  <div class="space-y-6">
      <h1 class="text-2xl font-bold text-gray-900">Judul Halaman</h1>
      <x-ui.card>
          <div class="p-6">
              <x-ui.input label="Nama" name="nama" />
              <x-ui.button type="submit">Simpan</x-ui.button>
          </div>
      </x-ui.card>
  </div>
  @endsection
  ```

### 5. Service (jika ada business logic kompleks)
```bash
php artisan make:service NamaService
```
- Inject di controller via constructor

### 6. Testing
```bash
php artisan make:test NamaModelTest
```
- Test CRUD operations
- Test tenant isolation
- Test authorization

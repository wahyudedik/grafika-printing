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
│   ├── Services/                 # Business logic services (12 files)
│   ├── Traits/                   # HasUuid trait
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
│   ├── web.php                   # Web routes (~487 baris)
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

---

## Fitur Status

### ✅ Sudah Selesai

#### Xendit Payment Gateway
- [`XenditService`](app/Services/XenditService.php) sudah fully integrated
- Support: QRIS, VA, E-Wallet, Convenience Store
- Webhook handling sudah robust

#### Linktree Module
- Models: [`Linktree`](app/Models/Vendor/Linktree.php), [`LinktreeLink`](app/Models/Vendor/LinktreeLink.php), [`LinktreeSocial`](app/Models/Vendor/LinktreeSocial.php), [`LinktreeProduct`](app/Models/Vendor/LinktreeProduct.php)
- Controllers: [`LinktreeController`](app/Http/Controllers/vendor/LinktreeController.php), [`LinktreePublicController`](app/Http/Controllers/LinktreePublicController.php)
- Views: `resources/views/vendor/linktree/`, `resources/views/linktree/public/`
- Routes: `/vendor/linktree/*` (vendor), `/l/{customUrl}` (public)

#### Template Builder
- Pilihan template: minimal, colorful, dark, professional, gradient, nature, neon, elegant
- Color picker, banner & avatar upload, button style configuration
- Live preview

#### Deployment Scripts
- [`deploy.sh`](deploy.sh) (first-time deployment)
- [`update.sh`](update.sh) (update deployment)
- Berdasarkan panduan di [`VPS_DEPLOYMENT_GUIDE.md`](VPS_DEPLOYMENT_GUIDE.md)

### ⚠️ Perlu Enhancement

#### User Lelang Management
- Client ingin role "User Lelang" yang bisa dikelola admin
- Tambah field `is_lelang_user` ke `users` table
- Buat `UserLelangController` untuk admin
- Dashboard khusus user lelang
- Routes: `/admin/user-lelang/*`

#### COD Ongkos Kirim
- Flow COD belum lengkap
- Perlu pisahkan rincian: harga barang + ongkir COD

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
Vendor Browse Produk → Add to Cart → Checkout
→ Pilih Payment (Cash/Online) → Process Payment
→ Invoice Dibuat → Status Updated
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
- Linktree public: `resources/views/linktree/public/show.blade.php` (belum ada)

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
- Config: [`tailwind.config.js`](tailwind.config.js) — custom primary colors, Inter font, `@tailwindcss/forms` plugin
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
- Test files di `tests/Feature/`
- Jalankan: `php artisan test`
- Untuk test spesifik: `php artisan test --filter=NamaTest`

---

## File yang Sering Diubah

| Kategori | File |
|----------|------|
| Routes | `routes/web.php`, `routes/api.php` |
| Config | `config/services.php`, `config/multitenancy.php` |
| Tenant Base | `app/Models/Vendor/TenantModel.php` |
| Tenant Manager | `app/Services/TenantManager.php` |
| Middleware | `app/Http/Middleware/SetTenantContext.php` |
| Tailwind Config | `tailwind.config.js` |
| UI Components | `resources/views/components/ui/` |
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

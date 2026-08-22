# ROADMAP.md - Pengembangan Grafika-Printing

## Status Proyek Saat Ini

**Fase:** Phase 7 - Production Hardening Complete
**Laravel Version:** 13.24.0 (di-upgrade dari 11.41.3 pada Agustus 2026)
**Last Updated:** 22 Agustus 2026 (Phase 7 Complete)

### Tech Stack
| Layer | Teknologi | Versi |
|-------|-----------|-------|
| Backend | Laravel | 13.24.0 |
| Language | PHP | 8.2+ |
| Database | MySQL | 5.7+ |
| Frontend CSS | **Tailwind CSS** | 3.1.0+ |
| Frontend JS | **Alpine.js** | 3.4.2 |
| Icons | FontAwesome | 6.4.0 |
| Build Tool | Vite | 6.0.11 |
| Payment | Xendit | Full integration |
| Shipping | RajaOngkir | API integration |

> **Catatan:** Migrasi **Bootstrap Tabler → Tailwind CSS** sudah selesai (Agustus 2026). Seluruh ~150+ views dan 6 layout files sudah dikonversi ke Tailwind CSS + Alpine.js.

Platform sudah memiliki fitur POS, Auction, Wallet, Linktree, dan integrasi **Xendit full** (payment gateway). Brief client meminta Xendit sebagai payment gateway untuk semua pembayaran (lelang + linktree), tambahan fitur Linktree, dan deployment scripts.

---

## Prioritas Berdasarkan Brief Client

```
🔴 KRITIS (Harus selesai)
    ├── User Lelang Management
    └── COD Ongkir Flow Enhancement

🟡 PENTING (Fitur utama brief)
    ├── Linktree Module (CRUD, halaman publik, custom URL) ✅
    ├── Template Builder ✅
    └── Xendit Integration untuk Linktree QRIS ✅

🟢 NORMAL (Supporting)
    ├── Testing & Bug Fixing
    ├── deploy.sh & update.sh ✅
    └── Code cleanup

🟦 SUDAH SELESAI (Update 4 Agustus 2026)
    ├── Bug Fix Thermal Printer ✅
    ├── Admin Service Configs CRUD ✅
    ├── Manual Transfer Payment ✅
    ├── Deployment Scripts (deploy.sh, update.sh) ✅
    ├── 20+ Missing Views (withdrawal, wallet, order tracking, mediation, shipping) ✅
    ├── Layout Bug Fix (layouts.app, vendor.layouts.app) ✅
    ├── Navigation Bug Fix (admin & user) ✅
    ├── Mediation Admin Views ✅
    └── Linktree Product Catalog ✅

🟩 SUDAH SELESAI (Update 6 Agustus 2026 — Code Quality & CDN Cleanup)
    ├── FontAwesome import fix (app.css) ✅
    ├── CDN Tailwind removal (5 views → Vite build) ✅
    ├── CDN libraries removal (ApexCharts, Chart.js, SortableJS → npm) ✅
    ├── .env.example security cleanup ✅
    ├── .env.production.example creation ✅
    ├── Copyright year update (2025 → 2026) ✅
    └── Error pages review (self-contained, no changes needed) ✅
```

> **Catatan Penting:** Client meminta **Xendit sebagai payment gateway FULL**. Tidak perlu Midtrans. `XenditService` sudah fully integrated dan mendukung QRIS, VA, E-Wallet. Phase 1 diubah menjadi verifikasi & enhancement Xendit yang sudah ada.

---

## Phase 1: Xendit Payment Gateway — ✅ SELESAI (diverifikasi)

> **Prioritas:** ✅ SUDAH ADA → ✅ DIVERIFIKASI
> **Status:** Fully verified & operational

### Tujuan
Memastikan [`XenditService`](app/Services/XenditService.php) yang sudah ada cover seluruh kebutuhan pembayaran untuk lelang dan linktree. **Tidak perlu membuat MidtransService baru.**

### 1.1 Verifikasi XenditService — ✅
- [x] QRIS tersedia sebagai metode pembayaran di Xendit dashboard
- [x] `createPaymentLink()` support QRIS
- [x] `createXenPayment()` support QRIS direct payment
- [x] Webhook callback sudah benar untuk semua metode pembayaran

### 1.2 Verifikasi Flow Pembayaran Lelang — ✅
- [x] Test flow: User pilih winner → Buat payment link → Bayar → Webhook confirm
- [x] [`OrderTrackingService`](app/Services/OrderTrackingService.php) ter-trigger dengan benar
- [x] [`EscrowPayment`](app/Models/EscrowPayment.php) terbuat saat payment success
- [x] Wallet vendor ter-kredit setelah delivery confirmed

### 1.3 Verifikasi Flow POS Payment — ✅
- [x] Test flow: Checkout → Pilih online payment → Xendit → Webhook confirm
- [x] Invoice terbuat dengan benar
- [x] Status transaksi terupdate

### 1.4 Environment Variables — ✅
- [x] `XENDIT_API_KEY` terkonfigurasi di `.env`
- [x] `XENDIT_WEBHOOK_TOKEN` terkonfigurasi
- [x] `XENDIT_BASE_URL` benar (production vs development)

---

## Phase 2: Linktree Module — ✅ SELESAI

> **Prioritas:** 🟡 PENTING → ✅ DISELESAIKAN
> **Estimasi:** ~35% dari total effort
> **Status:** Fully implemented

### 2.1 Database — ✅
- [x] Buat migration `create_linktrees_table.php`
- [x] Buat migration `create_linktree_links_table.php`
- [x] Buat migration `create_linktree_socials_table.php`
- [x] Buat migration `create_linktree_payments_table.php`
- [x] Buat Model `Linktree` (extend TenantModel)
- [x] Buat Model `LinktreeLink` (extend TenantModel)
- [x] Buat Model `LinktreeSocial` (extend TenantModel)
- [x] Buat Model `LinktreePayment` (extend UserTenantModel)

### 2.2 Backend - CRUD Links — ✅
- [x] Buat `app/Http/Controllers/vendor/LinktreeController.php`
  - `index()` - Dashboard linktree vendor
  - `edit()` - Edit profil & pengaturan
  - `update(Request $request)` - Save pengaturan
  - `links()` - List links
  - `storeLink(Request $request)` - Tambah link
  - `updateLink(Request $request, LinktreeLink $link)` - Update link
  - `destroyLink(LinktreeLink $link)` - Hapus link
  - `reorderLinks(Request $request)` - Drag & drop reorder
  - `toggleLink(LinktreeLink $link)` - Active/inactive
- [x] Buat `app/Http/Controllers/LinktreePublicController.php`
  - `show(string $customUrl)` - Render halaman publik
  - `handlePayment(Request $request)` - Proses pembayaran via QRIS

### 2.3 Backend - Template Builder — ✅
- [x] Buat `app/Http/Controllers/vendor/TemplateController.php`
  - `index()` - List template tersedia
  - `preview(Request $request)` - Preview template
  - `apply(Request $request)` - Apply template ke linktree

### 2.4 Backend - Linktree Payment (Xendit) — ✅
- [x] Integrasikan `XenditService` untuk QRIS payment di linktree
- [x] Buat QR code generation
- [x] Webhook handler untuk payment confirmation
- [x] Pencatatan transaksi

### 2.5 Routes — ✅
- [x] Vendor routes:
  ```php
  Route::prefix('linktree')->name('linktree.')->group(function () {
      Route::get('/', [LinktreeController::class, 'index'])->name('index');
      Route::put('/', [LinktreeController::class, 'update'])->name('update');
      Route::get('/links', [LinktreeController::class, 'links'])->name('links');
      Route::post('/links', [LinktreeController::class, 'storeLink'])->name('store-link');
      Route::put('/links/{link}', [LinktreeController::class, 'updateLink'])->name('update-link');
      Route::delete('/links/{link}', [LinktreeController::class, 'destroyLink'])->name('destroy-link');
      Route::post('/links/reorder', [LinktreeController::class, 'reorderLinks'])->name('reorder-links');
      Route::patch('/links/{link}/toggle', [LinktreeController::class, 'toggleLink'])->name('toggle-link');
      Route::get('/templates', [TemplateController::class, 'index'])->name('templates');
      Route::post('/templates/preview', [TemplateController::class, 'preview'])->name('template-preview');
      Route::post('/templates/apply', [TemplateController::class, 'apply'])->name('template-apply');
  });
  ```
- [x] Public route:
  ```php
  Route::get('/l/{customUrl}', [LinktreePublicController::class, 'show'])->name('linktree.public');
  ```

### 2.6 Views — ✅
- [x] Vendor views:
  - `resources/views/vendor/linktree/index.blade.php` - Dashboard
  - `resources/views/vendor/linktree/edit.blade.php` - Edit profil & settings
  - `resources/views/vendor/linktree/links.blade.php` - Manage links
  - `resources/views/vendor/linktree/template.blade.php` - Template builder
  - `resources/views/vendor/linktree/preview.blade.php` - Preview
- [x] Public views:
  - `resources/views/linktree/public.blade.php` - Halaman publik
  - `resources/views/vendor/linktree/products.blade.php` - Product catalog

### 2.7 Frontend — ✅
- [x] Alpine.js untuk drag & drop reorder links
- [x] Color picker untuk theme customization
- [x] Live preview desktop & mobile
- [x] Image upload (avatar, banner)
- [x] QR code display untuk pembayaran

---

## Phase 3: User Lelang Enhancement — ✅ SELESAI

> **Prioritas:** 🔴 KRITIS → ✅ DISELESAIKAN (7 Agustus 2026)
> **Estimasi:** ~15% dari total effort
> **Status:** Fully implemented

### 3.1 User Lelang Role — ✅
- [x] Model `LelangUserProfile` dengan scopes, status management, auto-assign
- [x] `UserLelangController` untuk admin (CRUD + verify/suspend/reactivate)
- [x] Auto-assign `LelangUserProfile` saat user pertama kali buat auction (`AuctionController::store`)
- [x] Filter admin: usertype + lelang profile status di `UserController::index`
- [x] Views admin: `resources/views/dev/user-lelang/` (index, show, create, edit)

### 3.2 Dashboard User Lelang — ✅
- [x] Dedicated dashboard: `resources/views/user/lelang-dashboard.blade.php`
- [x] Route: `/user/lelang-dashboard` → `UserDashboardController::lelangDashboard()`
- [x] Stats: total lelang, lelang selesai, pesanan aktif, total pengeluaran, win rate
- [x] Recent auctions dengan status badges
- [x] Quick actions grid
- [x] Link dari `user/dashboard.blade.php` → "Dashboard Lelang" button

### 3.3 Admin Management — ✅
- [x] Routes: `/admin/user-lelang/*` (full CRUD)
- [x] Views: `resources/views/dev/user-lelang/` (index, show, create, edit)
- [x] Filter di halaman user management (`dev/users/index.blade.php`)
- [x] Actions: verify, suspend, reactivate

### 3.4 Routes — ✅
```php
Route::prefix('user-lelang')->name('user-lelang.')->group(function () {
    Route::get('/', [UserLelangController::class, 'index'])->name('index');
    Route::get('/create', [UserLelangController::class, 'create'])->name('create');
    Route::post('/', [UserLelangController::class, 'store'])->name('store');
    Route::get('/{profile}', [UserLelangController::class, 'show'])->name('show');
    Route::get('/{profile}/edit', [UserLelangController::class, 'edit'])->name('edit');
    Route::put('/{profile}', [UserLelangController::class, 'update'])->name('update');
    Route::delete('/{profile}', [UserLelangController::class, 'destroy'])->name('destroy');
    Route::patch('/{profile}/verify', [UserLelangController::class, 'verify'])->name('verify');
    Route::patch('/{profile}/suspend', [UserLelangController::class, 'suspend'])->name('suspend');
    Route::patch('/{profile}/reactivate', [UserLelangController::class, 'reactivate'])->name('reactivate');
});
```

---

## Phase 4: COD Ongkir Enhancement — ✅ SELESAI

> **Prioritas:** 🔴 KRITIS → ✅ DISELESAIKAN (7 Agustus 2026)
> **Estimasi:** ~10% dari total effort
> **Status:** UI Enhancement Complete (Backend sudah ada)

### Yang Sudah Ada (Backend)
- [x] Field `is_cod`, `ongkir`, `subtotal`, `shipping_cost` di `transaksis` table
- [x] `handleCODPayment()` di `ShippingInvoiceController`
- [x] Invoice templates sudah tampilkan subtotal + ongkir breakdown
- [x] `Transaksi` model sudah support COD fields

### Yang Diperbarui (UI Enhancement)
- [x] `user/tracking/show.blade.php`: Breakdown detail (subtotal barang, ongkir dengan COD badge, kurir info, total payment section)
- [x] `user/tracking/index.blade.php`: COD badge pada tampilan ongkir
- [x] Info message untuk COD: "Pembayaran dilakukan saat barang diterima"

---

## Phase 5: Comprehensive Enhancement — ✅ SELESAI (11 Agustus 2026)

> **Status:** ✅ Fully Implemented (TAHAP 1A-3B)
> **Last Updated:** 11 Agustus 2026

### 5.1 Integrasi Authorization & Vendor Context ✅
- [x] **TAHAP 1A**: HasVendorContext trait → 8 vendor controllers
- [x] **TAHAP 1B**: AuthorizationService → 4 user/admin controllers

### 5.2 Request Validation & Flash Messages ✅
- [x] **TAHAP 1C**: Form Request classes → 8 controllers
- [x] **TAHAP 1D**: FlashMessage standardization → 23 controllers, 70+ instances

### 5.3 Rate Limiting & API Responses ✅
- [x] **TAHAP 1E**: Rate limiting (bootstrap/app.php)
- [x] **TAHAP 2A**: ApiResponse → AuthController, XenditPaymentController

### 5.4 Audit Log & Controller Refactoring ✅
- [x] **TAHAP 2B**: AuditLogService enhancement → 5 controllers
- [x] **TAHAP 2C**: 4 Action classes, 3 controllers refactored

### 5.5 Performance & UI ✅
- [x] **TAHAP 2D**: Responsive mobile fixes → 6 views
- [x] **TAHAP 2E**: N+1 query optimization → 3 controllers

### 5.6 Testing & Deployment ✅
- [x] **TAHAP 3A**: FlashMessageTest (15 tests) + ApiResponseTest (16 tests) = 31 tests, 98 assertions
- [x] **TAHAP 3B**: Deployment script sync (4 bug fixes + multi-tenant + Node.js 20.x)
- [x] **TAHAP 3C**: Documentation update (FEATURES.md, ROADMAP.md) — ✅ 19 Agustus 2026
- [ ] **TAHAP 3D**: Vite build validation
- [ ] **TAHAP 3E**: Final integration test

### 5.7 Bug Fix & Layout Enhancement (Batch 3) ✅ — 20 Agustus 2026
- [x] **BUG FIX**: Footer link dead link `href="#"` → `{{ route('welcome') }}` di 3 layouts (admin, vendor, user)
- [x] **BUG FIX**: Admin layout missing `@yield('breadcrumbs')` section
- [x] **BUG FIX**: Admin footer language inconsistency "All rights reserved." → "Hak cipta dilindungi."
- [x] **AUDIT**: Eager loading verification — semua 14 controller utama sudah benar
- [x] **AUDIT**: .env.example & .env.production — sudah lengkap
- [x] **AUDIT**: Responsive mobile — 3 layouts + key views sudah responsive
- [ ] **FUTURE**: Vendor views (order-tracking, tracking, wallet, withdrawal, audit-logs) perlu mobile card layouts

---

## Phase 6: Testing & Bug Fixing — ✅ SELESAI (134 tests, 244 assertions)

> **Prioritas:** 🟢 NORMAL → ✅ DISELESAIKAN
> **Status:** 134 tests, 244 assertions — coverage: Linktree, Vendor, Transactions, Webhook, Multi-tenant, POS, Wallet, Auction

### 6.1 Unit Tests — ✅
- [x] FlashMessageTest (15 tests, 27 assertions)
- [x] ApiResponseTest (16 tests, 71 assertions)
- [x] ProfileTest — User profile management

### 6.2 Feature Tests — ✅
- [x] LinktreeControllerTest — Linktree CRUD
- [x] LinktreeFlowTest — End-to-end linktree flow
- [x] VendorProductTest — Product CRUD
- [x] VendorTransactionTest — Transaction flow
- [x] AuctionFlowTest — Auction lifecycle
- [x] PosFlowTest — Point of Sale flow
- [x] WalletWithdrawalTest — Wallet & withdrawal

### 6.3 Integration Tests — ✅
- [x] WebhookSignatureTest — Xendit webhook verification
- [x] MultiTenantIsolationTest — Tenant data isolation
- [x] AuthenticationTest — Login, register, password

### 6.4 Bug Fixing — ✅
- [x] Fix N+1 query notification dropdown (3 layouts)
- [x] Fix N+1 query notification index views (3 views) — `unreadNotifications->count()` → `()->count()`
- [x] Fix status_color Tabler→Tailwind (2 models + 4 views)
- [x] Fix user/dashboard.blade.php status_color rendering bug
- [x] Fix FlashMessage pattern inconsistency (3 controllers)
- [x] Fix unused import UserNotificationController
- [x] Fix query-in-blade: `\App\Models\Vendor::all()` di admin-fees/transactions → pindah ke controller
- [x] Fix query-in-blade: `\App\Models\User` di pulse/activity → pindah ke controller
- [x] Fix missing eager loading: SpesifikasiController::show() → tambah `with('spesifikasiProduk')`
- [x] Fix query-in-blade: wholesalePrices query di bahan/show → gunakan eager-loaded collection
- [x] Fix duplicate Xendit webhook route
- [x] Fix `Linktree::booted()` missing `parent::booted()`
- [x] Fix `LinktreeController::authorizeLinktree()` undefined method
- [x] Fix Base Controller missing `AuthorizesRequests` trait
- [x] Fix missing `harga_jual` column (new migration)
- [x] Fix `Pelanggan` model missing `Notifiable` trait
- [x] Fix N+1 queries di `AuctionController`, `DeliveryConfirmationController`
- [x] Fix `WalletManagementController` performance (withCount + limited relationship)
- [x] Responsive design — sudah OK (semua view utama sudah punya desktop table + mobile card)

---

## Phase 7: Production Hardening (Agustus 2026) — ✅ SELESAI

> **Status:** ✅ Fully Implemented
> **Last Updated:** 22 Agustus 2026

### 7.1 Bug Fixes — ✅
- [x] Fix duplicate Xendit webhook route
- [x] Fix production bugs (Linktree, Controller, Migration)
- [x] Fix `Linktree::booted()` missing `parent::booted()`
- [x] Fix `LinktreeController::authorizeLinktree()` undefined method
- [x] Fix Base Controller missing `AuthorizesRequests` trait
- [x] Fix missing `harga_jual` column (new migration)
- [x] Fix `Pelanggan` model missing `Notifiable` trait
- [x] Fix N+1 queries di `AuctionController`, `DeliveryConfirmationController`
- [x] Fix `WalletManagementController` performance (withCount + limited relationship)

### 7.1.1 Bug Fixes — POS (22 Agustus 2026) — ✅
- [x] Fix field name `telepon` → `no_telp` di POS online payment view
- [x] Fix `transaksiItems` → `transaksiItem` di POS payment views (variable name inconsistency)
- [x] Fix stock validation sebelum `addToCart` dan `checkout`
- [x] Fix division by zero protection di `harga_satuan` calculation
- [x] Fix hardcoded vendor name di thermal print → gunakan `config('app.name')`
- [x] Fix pagination inconsistency di vendor views

### 7.2 API Versioning — ✅
- [x] All API routes versioned ke `/api/v1/`
- [x] Backward compatibility: old paths redirect ke v1 (301/307)
- [x] Rate limiting: 60 req/min untuk API, 5 req/min untuk auth
- [x] Xendit webhook tetap di `/api/xendit/webhook` (no redirect)

### 7.3 Performance Optimizations — ✅
- [x] JS lazy loading: ApexCharts, Chart.js, SortableJS loaded on-demand
- [x] ActiveLinktree caching: 1 hour TTL via `getActiveLinktreeCached()`
- [x] Wallet query optimization: withCount + limited relationship

### 7.4 Production Seeders — ✅
- [x] `ProductionSeeder.php` untuk fresh install
- [x] `ComprehensiveTestDataSeeder.php` untuk testing
- [x] `PosCompleteSeeder.php` untuk data lengkap POS — 10 kategori, 15 bahan, 10 spesifikasi, 6 alat, 10 produk, ~60 spesifikasi produk, 25 bahan-spesifikasi pivots, 25 estimasi produksi, 20 wholesale prices, 5 pelanggan, 1 printer setting (data realistis bisnis percetakan Indonesia)

### 7.5 Test Coverage Expansion — ✅
- [x] 134+ tests, 244+ assertions
- [x] Coverage: Linktree, Vendor, Transactions, Webhook, Multi-tenant, POS (termasuk `PosFlowTest`), Wallet, Auction, **Linktree Order Flow** (termasuk `LinktreeOrderTest`)

### 7.7 N+1 Query Fixes — ✅
- [x] Eager loading di `AuctionController` (user, bids.vendor)
- [x] Eager loading di `DeliveryConfirmationController` (relasi delivery)
- [x] `WalletManagementController` performance (withCount + limited relationship)

### 7.6 UI Standardization — ✅
- [x] Emoji → FontAwesome icons (~30 emoji di user views)
- [x] Pagination consistency: vendor views menggunakan `components.pagination`
- [x] Config improvements: `RAJAONGKIR_DELIVERY_API_KEY` di `config/services.php`
- [x] Cleanup: redundant CSS spacing di `tailwind.config.js`

---

## Linktree Order Flow (22 Agustus 2026) — ✅ SELESAI

> **Status:** ✅ Fully Implemented
> **Last Updated:** 22 Agustus 2026

### Fitur
- [x] Fix LinktreeProduct extends TenantModel
- [x] Tambah accessors spesifikasi di LinktreeProduct (`spesifikasi_summary`, `full_specs`, `bahans_list`, `kategori_name`)
- [x] Buat LinktreeOrder model + migration (`linktree_orders` table dengan UUID, selected_specs JSON, status enums)
- [x] Update LinktreePublicController (product detail, store order, order success)
- [x] Update LinktreeController vendor (orders management: list, detail, update status, update payment)
- [x] Update public page (product modal, specs display, order form)
- [x] Buat order-success page (WhatsApp button)
- [x] Buat vendor order management views (orders list, order detail)
- [x] Seeder untuk linktree products (`LinktreeProductSeeder`)
- [x] Test coverage (`LinktreeOrderTest` — 20+ tests)

### Routes
```php
// Public
Route::get('/l/{customUrl}/product/{linktreeProduct}', ...);   // Product detail API
Route::post('/l/{customUrl}/order/{linktreeProduct}', ...);    // Store order
Route::get('/l/{customUrl}/order/{uuid}/success', ...);        // Order success page
Route::get('/l/{customUrl}', ...);                             // Public page

// Vendor
Route::get('/orders', ...);                    // Orders list
Route::get('/orders/{uuid}', ...);             // Order detail
Route::put('/orders/{uuid}/status', ...);      // Update order status
Route::put('/orders/{uuid}/payment', ...);     // Update payment status
```

### Planned Improvements
- [ ] Xendit auto-payment integration (saat env sudah setup)
- [ ] Stock validation saat order
- [ ] Email notification ke vendor saat ada order baru
- [ ] Order history untuk customer
- [ ] Rating/review setelah order selesai

---

## Phase 7.8: POS Improvements (Planned)

> **Prioritas:** 🟡 PENTING (mendatang)
> **Status:** Planned

### 7.8.1 PriceCalculationService Extraction
- [ ] Ekstrak logic kalkulasi harga dari PosController ke `PriceCalculationService`
- [ ] Deduplicate logic perhitungan bahan/finishing/ukuran
- [ ] Sentralisasi wholesale pricing logic
- [ ] Memudahkan testing dan maintenance

### 7.8.2 Stock Minimum Alerts
- [ ] Notifikasi ketika stok bahan di bawah minimum threshold
- [ ] Dashboard widget untuk stock alerts
- [ ] Email/notification ke vendor

### 7.8.3 Void/Cancel Transaction
- [ ] Fitur void transaksi POS (belum selesai)
- [ ] Fitur cancel transaksi dengan alasan
- [ ] Reversal stok otomatis saat void/cancel
- [ ] Audit log untuk void/cancel actions

### 7.8.4 Discount/Coupon System
- [ ] Diskon per item atau per transaksi
- [ ] Coupon code system
- [ ] Diskon percentage atau fixed amount
- [ ] Batasan penggunaan (single-use, multi-use, expiry)

### 7.8.5 Profit Tracking
- [ ] Tambah kolom `hpp_total` di `transaksi_items`
- [ ] Hitung profit per transaksi: `harga_jual - hpp_total`
- [ ] Dashboard profit analytics
- [ ] Laporan profit harian/bulanan

### 7.8.6 Thermal Print Template Merge
- [ ] Customizable thermal print template
- [ ] Template menyatu dengan printer settings
- [ ] Preview sebelum print
- [ ] Support berbagai ukuran kertas (58mm, 80mm)

---

## Phase 8: Mobile API (Planned)

> **Prioritas:** 🟡 PENTING (mendatang)
> **Status:** Planned

### 8.1 Mobile API Endpoints
- [ ] Mobile API endpoints untuk auctions, products, orders
- [ ] API authentication (Sanctum token-based)
- [ ] API response standardization

### 8.2 API Documentation
- [ ] Swagger/OpenAPI documentation
- [ ] API versioning documentation

### 8.3 Push Notification
- [ ] Push notification support (FCM/APNs)
- [ ] Real-time order status updates

---

## Phase 9: Database Optimization (Planned)

> **Prioritas:** 🟢 NORMAL (mendatang)
> **Status:** Planned

### 9.1 Bank Account Normalization
- [ ] Bank account data split dari `vendors` table ke tabel terpisah
- [ ] Support multiple bank accounts per vendor

### 9.2 Migration Consolidation
- [ ] Database migration consolidation
- [ ] Review dan cleanup unused migrations

### 9.3 Query Optimization
- [ ] Query optimization audit
- [ ] Index optimization untuk hot queries
- [ ] Database connection pooling evaluation

---

## Dependency & Integration Map

```mermaid
graph TB
    subgraph Phase 1 - Xendit Verification
        X1[XenditService Audit] --> X2[QRIS Verification]
        X2 --> X3[Auction Flow Test]
        X3 --> X4[POS Flow Test]
    end
    
    subgraph Phase 2 - Linktree
        L1[Models] --> L2[Migration]
        L2 --> L3[Controller]
        L3 --> L4[Routes]
        L4 --> L5[Views]
        L5 --> L6[Template Builder]
        L6 --> L7[Xendit QRIS Integration]
    end
    
    subgraph Phase 3 - User Lelang
        U1[Role Enhancement] --> U2[Controller]
        U2 --> U3[Dashboard]
        U3 --> U4[Admin Management]
    end
    
    subgraph Phase 4 - COD
        C1[Flow Enhancement] --> C2[Invoice Update]
        C2 --> C3[Rekonsiliasi]
    end
    
    subgraph Phase 5 - Deploy
        D1[deploy.sh] --> D2[update.sh]
    end
    
    X4 --> L7
    X4 --> U1
```

---

## Tech Debt yang Perlu Diatasi

1. ~~**Xendit Full Coverage**~~ ✅ **SUDAH VERIFIKASI** — XenditService sudah fully integrated. QRIS, VA, E-Wallet berfungsi. Webhook handling robust.

2. ~~**Credentials di .env.example**~~ ✅ **SUDAH DIPERBAIKI** — Semua hardcoded API keys, passwords, dan APP_KEY dihapus dari `.env.example`. `.env.production.example` dibuat dengan placeholder.

3. ~~**No deploy/update scripts**~~ ✅ **SUDAH ADA** — `deploy.sh` dan `update.sh` sudah dibuat.

4. ~~**Test coverage minim**~~ ✅ **SELESAI** — 134 tests, 244 assertions. Coverage: Linktree, Vendor, Transactions, Webhook, Multi-tenant isolation, POS, Wallet, Auction.

5. ~~**N+1 Query di notification dropdown**~~ ✅ **SUDAH DIPERBAIKI** — 3 layout (vendor, user, admin) sebelumnya memanggil `unreadNotifications->count()` 3 kali per page load.

6. ~~**Tabler CSS classes di model attributes**~~ ✅ **SUDAH DIPERBAIKI** — `LelangUserProfile` dan `XenditPayment` sudah return Tailwind-compatible values.

7. ~~**FlashMessage pattern inconsistency**~~ ✅ **SUDAH DIPERBAIKI** — 3 notification controller sudah menggunakan `FlashMessage::backSuccess()`.

5. **Mixed language kode** - Campuran Bahasa Indonesia dan Inggris, perlu standardisasi

6. ~~**CDN dependencies**~~ ✅ **SUDAH DIPERBAIKI** — FontAwesome, ApexCharts, Chart.js, SortableJS semua sudah via npm/Vite build. 5 view yang masih pakai CDN Tailway sudah dimigrasi.

7. ~~**FontAwesome icon tidak load**~~ ✅ **SUDAH DIPERBAIKI** — Import FontAwesome ditambahkan ke `resources/css/app.css` (sebelumnya hanya di `welcome.css`).

8. ~~**Native confirm() dialogs**~~ ✅ **SUDAH DIPERBAIKI** — 22 native `confirm()` dikonversi ke SweetAlert2 (`confirmDelete()`/`confirmAction()`)

9. ~~**Empty state inconsistency**~~ ✅ **SUDAH DIPERBAIKI** — 6+ views dikonversi ke `<x.ui.empty-state>` component

10. ~~**Missing breadcrumbs**~~ ✅ **SUDAH DIPERBAIKI** — 32 halaman vendor ditambah breadcrumbs via `<x-ui.breadcrumb>`

---

## Completed: Migrasi ke Tailwind CSS (Agustus 2026) ✅

Migrasi **FULL** dari Bootstrap Tabler ke **Tailwind CSS** telah selesai. Ini adalah perubahan frontend terbesar dalam sejarah projek.

### Yang Sudah Dilakukan
- **Layouts** (6 file): vendor, user, dev/admin, app, auth, vendor/layouts/app — semua di-redesign dengan Tailwind CSS
- **Views** (~150+ file): Semua panel (Admin, Vendor, User, POS, Payments) sudah dikonversi dari Tabler ke Tailwind utility classes
- **Design System**: 12 UI components (`components/ui/`) dengan Tailwind CSS + Alpine.js
- **JavaScript**: Bootstrap JS → Alpine.js (modals, dropdowns, alerts, toasts, collapsibles)
- **Build**: Vite production build berhasil (CSS 95 kB gzip 15 kB, JS 155 kB gzip 49 kB)
- **Cleanup**: Hapus `@tabler/core` dari npm, hapus semua CDN references, hapus dead CSS shims
- **Dev Header**: Konversi ke pure Tailwind (sebelumnya Bootstrap-style)

## Code Quality Improvements (Agustus 2026) ✅

### Yang Sudah Dilakukan
- **Error Pages** (403, 404, 500): Self-contained CSS, tidak bergantung pada CDN Tailwind
- **x-cloak**: Menggantikan `style="display: none"` di 20+ files Alpine.js → mencegah FOUC
- **Auth Inline Styles**: Dipindahkan dari inline `<style>` ke `resources/css/app.css`
- **Welcome Page CSS**: ~1000 baris inline CSS dipindahkan ke `resources/css/welcome.css` (external, compiled via Vite)
- **Empty State Component**: `<x.ui.empty-state>` reusable — menggantikan 7+ tempat raw HTML empty state
- **UI Components Terdaftar**: 13 components di `components/ui/` (tambah `empty-state`, `confirmation-dialog`, `form-group`, `stat-card`)
- **FontAwesome Import Fix**: Ditambahkan ke `resources/css/app.css` — sebelumnya 300+ ikon gagal load di semua panel
- **CDN Cleanup**: 5 view migrasi dari CDN Tailwind ke Vite build, 7 view bersih dari CDN ApexCharts/Chart.js/SortableJS
- **npm Packages**: ApexCharts, Chart.js, SortableJS ditambahkan ke `package.json` dan diimport sebagai global di `app.js`
- **Security**: `.env.example` dibersihkan dari hardcoded keys, `.env.production.example` dibuat
- **Comprehensive Audit II** (7 Agustus 2026):
  - Konversi 11 native `confirm()` → SweetAlert2 (`confirmDelete()`/`confirmAction()`) di 6 file
  - Auth views Tailwind migration: 6 file auth views dikonversi dari legacy CSS ke Tailwind
  - Extract `resources/js/auth.js`: `togglePassword()` dan `initPasswordStrength()` — hapus 3x code duplication
  - Konsistensi empty state: 8 file dikonversi ke `<x.ui.empty-state>` component
  - Flash messages konsisten: Manual flash blocks diganti `<x.ui.alert>` di service-configs
  - Hapus duplicate `confirmDelete()` inline scripts (sudah global via `components/alert.blade.php`)
  - Gap Analysis dikoreksi: Linktree Module & Template Builder = ✅ Sudah ada
  - Form validation: OrderTrackingController `status` ditambahkan `in:` constraint (10 valid statuses)
  - Form validation: AuctionBidController `bid_amount` dikoreksi dari `min:0` ke `min:1`
  - Bug fix ShippingController: Kolom `status`/`resi`/`cost` dikoreksi ke `shipping_status`/`waybill_number`/`shipping_cost` (sesuai migration schema)

- **Comprehensive Audit III** (7 Agustus 2026 — Full Enhancement):
  - **RINGAN:**
    - Konversi 11 native `confirm()` → SweetAlert2 di 6 file (vendor views)
    - Fix `bulkCheckStatus` placeholder → implemented with Xendit API check
    - PenggunaController CRUD: create, store, edit, update, destroy (5 methods)
    - Konsistensi confirm dialog: `confirmDelete()`, `confirmAction()`, `confirmFormSubmit()`
    - Standardisasi empty state: 6 views → `<x.ui.empty-state>` component
  - **MEDIUM:**
    - Breadcrumbs: 32 halaman vendor ditambah `<x.ui.breadcrumb>`
    - Adopt `<x-ui.button>`: 120+ buttons dikonversi di 32 halaman vendor
  - **BESAR:**
    - User Lelang: Auto-assign profile, filter admin, dedicated dashboard
    - COD Ongkir: Breakdown detail di tracking views
  - **Documentation:**
    - FEATURES.md: Comprehensive audit section added
    - ROADMAP.md: Updated to reflect all completed tasks

### Masih Perlu Dikerjakan (Deferred)
- ~~Adopt `<x.ui.button>` component di views yang masih pakai raw HTML buttons~~ ✅ DONE (120+ buttons dikonversi)
- ~~Tambah breadcrumbs pada halaman vendor/admin~~ ✅ DONE (32 halaman vendor ditambah breadcrumbs)
- ~~Fix responsive mobile pada views tertentu~~ ✅ DONE (6 views: transaksi, produk, pelanggan, pengguna, order-tracking, spesifikasi)
- Standardisasi card styling across views
- Dark mode activation: Aktifkan `darkMode: 'class'` di `tailwind.config.js` jika diperlukan
- ~~**Test coverage**~~ ✅ DONE (134 tests, 244 assertions). Coverage: Linktree, Vendor, Transactions, Webhook, Multi-tenant, POS, Wallet, Auction.
- ~~**Request Validation Classes**~~ ✅ DONE (8 controllers menggunakan Form Request)
- ~~**API Response Standardization**~~ ✅ DONE (AuthController + XenditPaymentController)
- **N+1 Query Optimization** ✅ DONE (3 controllers). Perlu review controller lainnya.
- **Deployment Script Sync** ✅ DONE (4 bug fixes + multi-tenant commands)

### Teknologi Frontend Saat Ini
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| **Tailwind CSS** | 3.1.0+ | Seluruh styling (utility-first) |
| **Alpine.js** | 3.4.2 | Client-side interactivity |
| FontAwesome | 6.4.0 | Ikon |
| Vite | 6.0.11 | Build assets |
| SweetAlert2 | 11.17.2 | Dialog & notifikasi (npm) |
| ApexCharts | - | Grafik dashboard |
| Chart.js | - | Grafik tambahan |

### Tailwind CSS Configuration
- **Config:** [`tailwind.config.js`](tailwind.config.js)
- **Custom primary colors** (blue palette)
- **Custom font:** Inter, Figtree
- **Plugins:** `@tailwindcss/forms`

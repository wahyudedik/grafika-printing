# Rencana Perbaikan & Enhancement Grafika-Printing
> Tanggal: 6 Agustus 2026 | Status: Draft untuk Review

---

## Ringkasan Temuan

Dari analisis menyeluruh terhadap seluruh kode sumber, ditemukan **3 masalah kritis**, **5 masalah sedang**, dan **4 masalah ringan** yang perlu diperbaiki. Semua perbaikan ini **tidak memerlukan perubahan database** sehingga aman untuk production.

---

## 🔴 MASALAH KRITIS

### 1. FontAwesome Tidak Load di Seluruh Panel Aplikasi

**File:** [`resources/css/app.css`](resources/css/app.css:1)

**Masalah:** FontAwesome di-install via npm (`@fortawesome/fontawesome-free: ^6.4.0` di [`package.json`](package.json:23)) dan di-import di [`welcome.css`](resources/css/welcome.css:1) untuk landing page, TAPI **tidak di-import di [`app.css`](resources/css/app.css:1)**. Semua layout files ([`layouts/vendor`](resources/views/layouts/vendor.blade.php:10), [`layouts/user`](resources/views/layouts/user.blade.php:1), [`dev/layouts/app`](resources/views/dev/layouts/app.blade.php:10), [`layouts/app`](resources/views/layouts/app.blade.php:10), [`layouts/auth`](resources/views/layouts/auth.blade.php:11)) memuat `app.css` via Vite.

**Dampak:** **300+ icon FontAwesome** di seluruh view panel (vendor, user, admin) **tidak tampil**. Ini termasuk icon navigasi, tombol aksi, status indicator, dan elemen UI lainnya.

**Perbaikan:** Tambahkan `@import '@fortawesome/fontawesome-free/css/all.min.css';` di bagian atas [`resources/css/app.css`](resources/css/app.css:1) sebelum `@tailwind base;`

**Estimasi Dampak:** ~300+ view terdampak

---

### 2. CDN Tailwind CSS di 5 View (Bukan Vite Build)

**Views terdampak:**
- [`manual-transfer/status.blade.php`](resources/views/manual-transfer/status.blade.php:9) - Baris 9
- [`vendor/public-profile.blade.php`](resources/views/vendor/public-profile.blade.php:16) - Baris 16
- [`xendit/example/success.blade.php`](resources/views/xendit/example/success.blade.php:9) - Baris 9
- [`xendit/example/payment.blade.php`](resources/views/xendit/example/payment.blade.php:9) - Baris 9
- [`xendit/example/failure.blade.php`](resources/views/xendit/example/failure.blade.php:9) - Baris 9

**Masalah:** Menggunakan `<script src="https://cdn.tailwindcss.com"></script>` instead of Vite build. Ini:
- Tidak konsisten dengan design system yang ada
- Tidak menggunakan custom Tailwind config dari [`tailwind.config.js`](tailwind.config.js:1)
- Bergantung pada CDN eksternal (risiko downtime)
- Filesize lebih besar (CDN Tailwind ~300KB vs Vite build ~minimal)
- Tidak menggunakan custom CSS variables dan component classes dari `app.css`

**Perbaikan:** Migrasi setiap view ke menggunakan layout yang sesuai atau minimal menggunakan `@vite(['resources/css/app.css', 'resources/js/app.js'])` dengan Tailwind inline.

---

### 3. CDN FontAwesome di Xendit Example Views

**Views terdampak:**
- [`xendit/example/success.blade.php`](resources/views/xendit/example/success.blade.php:10) - Baris 10
- [`xendit/example/payment.blade.php`](resources/views/xendit/example/payment.blade.php:10) - Baris 10
- [`xendit/example/failure.blade.php`](resources/views/xendit/example/failure.blade.php:10) - Baris 10

**Masalah:** Menggunakan `<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">` (version 6.0.0) padahal project sudah install `@fortawesome/fontawesome-free: ^6.4.0` via npm.

**Perbaikan:** Setelah Fix #1 selesai (FontAwesome di-import ke app.css), migrasi view ini ke layout yang menggunakan Vite build.

---

## 🟡 MASALAH SEDANG

### 4. CDN Libraries yang Seharusnya npm Package

**File terdampak:**
- [`laporan/harian.blade.php`](resources/views/laporan/harian.blade.php:132) - ApexCharts CDN
- [`laporan/bulanan.blade.php`](resources/views/laporan/bulanan.blade.php:154) - ApexCharts CDN
- [`laporan/tahunan.blade.php`](resources/views/laporan/tahunan.blade.php:132) - ApexCharts CDN
- [`dashboard.blade.php`](resources/views/dashboard.blade.php:332) - ApexCharts CDN
- [`dev/dashboard.blade.php`](resources/views/dev/dashboard.blade.php:226) - Chart.js CDN
- [`admin/cms/statistics.blade.php`](resources/views/admin/cms/statistics.blade.php:115) - Chart.js CDN
- [`vendor/linktree/products.blade.php`](resources/views/vendor/linktree/products.blade.php:199) - SortableJS CDN

**Masalah:** Menggunakan CDN untuk library yang sudah tersedia di npm. Ini menciptakan dependency eksternal yang tidak perlu.

**Perbaikan:**
- Install `apexcharts` dan `sortablejs` via npm
- Import di [`resources/js/app.js`](resources/js/app.js:1)
- Buat global reference agar view bisa mengaksesnya
- Hapus CDN script tags dari views

---

### 5. .env.example Berisi Hardcoded API Keys

**File:** [`.env.example`](.env.example:3)

**Masalah:**
- Line 3: `APP_KEY=base64:+u/24/zzl+Y0OWxNq8NWQAlXbp7RBAUqiQ9aN9Qt25M=` (APP_KEY hardcoded!)
- Line 29: `DB_PASSWORD=Wahyu123456789@` (password database hardcoded!)
- Line 55-56: `MAIL_USERNAME` dan `MAIL_PASSWORD` hardcoded
- Line 69-71: `XENDIT_API_KEY`, `XENDIT_PUBLIC_KEY`, `XENDIT_WEBHOOK_TOKEN` hardcoded
- Line 81-86: `RAJAONGKIR_API_KEY` dan delivery key hardcoded

**Risiko:** API keys dan credentials bocor ke public repository.

**Perbaikan:** Ganti semua values sensitif dengan placeholder kosong atau deskriptif.

---

### 6. .env.example Tidak Sinkron dengan .env

**Perbedaan:**
| Setting | .env | .env.example |
|---------|------|--------------|
| `SESSION_DRIVER` | `redis` | `database` |
| `QUEUE_CONNECTION` | `redis` | `database` |
| `CACHE_STORE` | `redis` | `database` |
| `SANCTUM_STATEFUL_DOMAINS` | `http://grafika-printing.test` | `https://grafika.noteds.com` |

**Perbaikan:** Sinkronkan .env.example dengan .env, atau buat dua versi (development dan production).

---

### 7. Copyright Year "2025" di Welcome Page

**File:** [`resources/views/welcome.blade.php`](resources/views/welcome.blade.php:475)

**Masalah:** Default value di `CmsSetting::get()` masih "2025" padahal sekarang tahun 2026. Jika CMS setting belum di-set, copyright year akan salah.

**Perbaikan:** Update default value dari `©2025` ke `©2026`.

---

### 8. Error Pages Menggunakan Inline CSS

**Files:** [`errors/403.blade.php`](resources/views/errors/403.blade.php:1), [`errors/404.blade.php`](resources/views/errors/404.blade.php:1), [`errors/500.blade.php`](resources/views/errors/500.blade.php:1)

**Masalah:** Error pages menggunakan inline CSS dan Google Fonts CDN tanpa menggunakan Vite build. Ini konsisten untuk error pages (karena bisa ditampilkan sebelum app load), tapi bisa dioptimasi.

**Perbaikan:** Pertahankan approach standalone untuk error pages (karena error page harus works tanpa app stack), tapi pastikan styling konsisten dengan design system.

---

## 🟢 MASALAH RINGAN

### 9. Tidak Ada File .env.production

**Masalah:** Belum ada template .env.production yang memisahkan config production dari development.

**Perbaikan:** Buat `.env.production.example` dengan config production (APP_DEBUG=false, production API keys placeholder, etc.)

---

### 10. AGENTS.md vs AGENT.md

**Masalah:** Ada dua file: `AGENT.md` (515 baris, dokumen lengkap) dan `AGENTS.md` (mungkin versi lama). Perlu diverifikasi apakah masih relevan.

**Perbaikan:** Cek dan sinkronkan/update kedua file.

---

### 11. Vendor Layout Menggunakan SVG Inline Icons

**File:** [`resources/views/layouts/vendor.blade.php`](resources/views/layouts/vendor.blade.php:27-50)

**Observasi:** Sidebar vendor menggunakan SVG inline icons (bukan FontAwesome). Ini sebenarnya bagus untuk performance, tapi menciptakan dual icon system. View content di dalam layout menggunakan FontAwesome.

**Saran:** Pertahankan SVG untuk sidebar (better performance), FontAwesome untuk content icons.

---

### 12. ApexCharts dan Chart.js Kedua-duanya Digunakan

**Observasi:** Beberapa view menggunakan ApexCharts (laporan, dashboard vendor) dan beberapa menggunakan Chart.js (dev dashboard, admin CMS). Membuat dependency lebih kompleks.

**Saran:** Pertahankan saat ini, tapi pertimbangkan standarisasi di masa depan.

---

## Rencana Implementasi (Urutan Eksekusi)

### Phase 1: Quick Wins (Fix Kritis)
1. Tambahkan import FontAwesome ke [`app.css`](resources/css/app.css:1)
2. Migrasi 5 view dari CDN Tailwind ke Vite build
3. Update copyright year di [`welcome.blade.php`](resources/views/welcome.blade.php:475)

### Phase 2: Security & Config
4. Bersihkan [.env.example](.env.example:1) dari hardcoded values
5. Sinkronkan [.env.example](.env.example:1) config dengan [.env](.env:1)
6. Buat [.env.production.example](.env.production.example:1)

### Phase 3: CDN Migration
7. Install apexcharts dan sortablejs via npm
8. Import libraries di [`app.js`](resources/js/app.js:1)
9. Migrasi CDN script tags di views

### Phase 4: Documentation
10. Update [FEATURES.md](FEATURES.md:1) dengan temuan dan perbaikan
11. Update [ROADMAP.md](ROADMAP.md:1) dengan progress
12. Update/sinkronkan [AGENT.md](AGENT.md:1)

### Phase 5: Optimization (Optional)
13. Audit responsive mobile views
14. Review UI/UX consistency
15. Standardize chart library usage

---

## Catatan Penting

- **Tidak ada perubahan database** - Semua fix aman untuk production
- **Tidak ada perubahan routing** - Hanya asset loading dan view structure
- **Backward compatible** - Perubahan tidak mempengaruhi fitur existing
- **Deploy只需要 `npm run build`** - Setelah perubahan CSS/JS

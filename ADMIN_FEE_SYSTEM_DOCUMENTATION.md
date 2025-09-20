# Sistem Biaya Admin - Dokumentasi Lengkap

## Overview
Sistem biaya admin yang scalable dan dapat dikonfigurasi dari dashboard superadmin/dev. Fitur ini memungkinkan admin untuk mengatur biaya admin aplikasi yang akan ditambahkan ke setiap lelang.

## Fitur Utama

### 1. Pengaturan Biaya Admin (Admin Dashboard)
- **Lokasi**: `/admin/admin-fees`
- **Akses**: Hanya untuk user dengan role `dev`
- **Fitur**:
  - CRUD pengaturan biaya admin
  - Preview perhitungan biaya
  - Statistik dan laporan transaksi
  - Manajemen status aktif/nonaktif

### 2. Jenis Biaya Admin
- **Biaya Tetap (Fixed)**: Biaya dalam Rupiah (contoh: Rp 5.000)
- **Biaya Persentase (Percentage)**: Biaya dalam persentase (contoh: 10%)
- **Biaya Payment Gateway**: Biaya untuk payment gateway (Xendit)

### 3. Kategori Biaya
- **auction**: Biaya untuk lelang
- **payment**: Biaya untuk pembayaran
- **transaction**: Biaya untuk transaksi

## Flow Sistem

### 1. Pembuatan Lelang
```
User membuat lelang → Sistem menghitung biaya admin → Lelang disimpan dengan biaya admin
```

### 2. Vendor Menawar
```
Vendor menawar → Sistem menampilkan total biaya (bid + biaya admin) → User melihat total pembayaran
```

### 3. Pembayaran
```
User memilih vendor → Sistem membuat payment link dengan total biaya → User membayar via Xendit
```

## Struktur Database

### Tabel: admin_fee_settings
```sql
- id (bigint, primary key)
- name (varchar) - Nama pengaturan
- description (text) - Deskripsi pengaturan
- type (enum: 'fixed', 'percentage') - Tipe biaya
- value (decimal) - Nilai biaya
- minimum_amount (decimal) - Jumlah minimum
- maximum_amount (decimal) - Jumlah maksimum
- category (varchar) - Kategori biaya
- is_active (boolean) - Status aktif
- effective_from (datetime) - Berlaku dari
- effective_until (datetime) - Berlaku sampai
- conditions (json) - Kondisi tambahan
- created_by (bigint) - Dibuat oleh
- updated_by (bigint) - Diperbarui oleh
- created_at (datetime)
- updated_at (datetime)
```

### Tabel: admin_fee_transactions
```sql
- id (bigint, primary key)
- auction_id (bigint) - ID lelang
- vendor_id (bigint) - ID vendor
- user_id (bigint) - ID user
- auction_amount (decimal) - Jumlah lelang
- admin_fee_amount (decimal) - Biaya admin
- payment_gateway_fee (decimal) - Biaya payment gateway
- total_amount (decimal) - Total pembayaran
- vendor_receives (decimal) - Yang diterima vendor
- admin_receives (decimal) - Yang diterima admin
- status (enum: 'pending', 'paid', 'failed', 'refunded')
- fee_breakdown (json) - Rincian biaya
- created_at (datetime)
- updated_at (datetime)
```

### Tabel: auctions (Field Tambahan)
```sql
- admin_fee_amount (decimal) - Biaya admin
- payment_gateway_fee (decimal) - Biaya payment gateway
- total_amount_with_fees (decimal) - Total dengan biaya
- vendor_receives (decimal) - Yang diterima vendor
- admin_receives (decimal) - Yang diterima admin
- fee_breakdown (json) - Rincian biaya
- fees_calculated (boolean) - Status perhitungan
```

## API Endpoints

### Admin Fee Management
```
GET    /admin/admin-fees              - Daftar pengaturan
GET    /admin/admin-fees/create       - Form tambah pengaturan
POST   /admin/admin-fees              - Simpan pengaturan
GET    /admin/admin-fees/{id}         - Detail pengaturan
GET    /admin/admin-fees/{id}/edit    - Form edit pengaturan
PUT    /admin/admin-fees/{id}         - Update pengaturan
DELETE /admin/admin-fees/{id}          - Hapus pengaturan
PATCH  /admin/admin-fees/{id}/toggle  - Toggle status
GET    /admin/admin-fees/transactions - Daftar transaksi
GET    /admin/admin-fees/statistics   - Statistik
GET    /admin/admin-fees/preview      - Preview biaya
POST   /admin/admin-fees/preview      - Hitung preview
```

## Contoh Penggunaan

### 1. Membuat Pengaturan Biaya Admin
```php
// Via Seeder (sudah ada)
php artisan db:seed --class=AdminFeeSettingSeeder

// Via Controller
$setting = AdminFeeSetting::create([
    'name' => 'Biaya Admin 10%',
    'description' => 'Biaya admin aplikasi 10%',
    'type' => 'percentage',
    'value' => 10.00,
    'minimum_amount' => 10000,
    'maximum_amount' => 10000000,
    'category' => 'auction',
    'is_active' => true,
    'created_by' => auth()->id()
]);
```

### 2. Menghitung Biaya Admin
```php
use App\Services\AdminFeeService;

$adminFeeService = app(AdminFeeService::class);
$fees = $adminFeeService->calculateTotalFees(50000, 'bank_transfer');

// Hasil:
// [
//     'auction_amount' => 50000,
//     'admin_fee' => 5000,        // 10% dari 50000
//     'payment_gateway_fee' => 750, // 1.5% dari 50000
//     'total_fees' => 5750,
//     'total_amount' => 55750,
//     'vendor_receives' => 50000,
//     'admin_receives' => 5750
// ]
```

### 3. Preview Biaya Admin
```php
// Via API
POST /admin/admin-fees/preview
{
    "amount": 50000,
    "payment_method": "bank_transfer"
}

// Response
{
    "auction_amount": 50000,
    "admin_fee": 5000,
    "payment_gateway_fee": 750,
    "total_fees": 5750,
    "total_amount": 55750,
    "vendor_receives": 50000,
    "admin_receives": 5750,
    "fee_percentage": 10.0,
    "breakdown": {
        "admin_fees": [...],
        "payment_gateway": {...}
    }
}
```

## Konfigurasi Default

### Biaya Admin Aplikasi
- **10% untuk lelang normal** (Rp 10.000 - Rp 10.000.000)
- **5% untuk lelang besar** (di atas Rp 1.000.000)
- **Rp 5.000 tetap** (opsional, nonaktif)

### Biaya Payment Gateway (Xendit)
- **Bank Transfer**: 1.5%
- **Credit Card**: 2.9%
- **E-Wallet**: 2.0%
- **Retail Outlet**: 1.0%

## Contoh Skenario

### Skenario 1: Lelang Rp 50.000
```
Budget Lelang: Rp 50.000
Biaya Admin (10%): Rp 5.000
Biaya Payment Gateway (1.5%): Rp 750
Total Pembayaran: Rp 55.750
Vendor Menerima: Rp 50.000
Admin Menerima: Rp 5.750
```

### Skenario 2: Lelang Rp 1.500.000
```
Budget Lelang: Rp 1.500.000
Biaya Admin (5%): Rp 75.000
Biaya Payment Gateway (1.5%): Rp 22.500
Total Pembayaran: Rp 1.597.500
Vendor Menerima: Rp 1.500.000
Admin Menerima: Rp 97.500
```

## Monitoring dan Laporan

### 1. Dashboard Statistik
- Total transaksi biaya admin
- Total pendapatan admin
- Rata-rata persentase biaya
- Grafik tren pendapatan

### 2. Laporan Transaksi
- Daftar semua transaksi biaya admin
- Filter berdasarkan tanggal, status, vendor
- Export data transaksi

### 3. Preview Biaya
- Kalkulator biaya admin real-time
- Preview untuk berbagai skenario
- Breakdown rincian biaya

## Keamanan

### 1. Akses Kontrol
- Hanya user dengan role `dev` yang dapat mengakses
- Middleware `DevMiddleware` untuk proteksi

### 2. Validasi Data
- Validasi input pengaturan biaya
- Validasi perhitungan biaya
- Sanitasi data user

### 3. Audit Trail
- Log semua perubahan pengaturan
- Tracking user yang membuat perubahan
- Timestamp untuk semua operasi

## Troubleshooting

### 1. Biaya Admin Tidak Dihitung
- Periksa status pengaturan (is_active = true)
- Periksa tanggal efektif (effective_from/until)
- Periksa range minimum/maksimum

### 2. Preview Tidak Muncul
- Periksa JavaScript console untuk error
- Periksa route admin-fees.preview
- Periksa CSRF token

### 3. Statistik Tidak Akurat
- Periksa filter tanggal
- Periksa status transaksi
- Periksa relasi database

## Maintenance

### 1. Backup Data
```bash
# Backup tabel admin_fee_settings
mysqldump -u username -p database_name admin_fee_settings > admin_fee_settings_backup.sql

# Backup tabel admin_fee_transactions
mysqldump -u username -p database_name admin_fee_transactions > admin_fee_transactions_backup.sql
```

### 2. Update Pengaturan
```php
// Update biaya admin
$setting = AdminFeeSetting::find(1);
$setting->update(['value' => 15.00]); // Ubah dari 10% ke 15%

// Nonaktifkan pengaturan lama
$oldSetting = AdminFeeSetting::find(1);
$oldSetting->update(['is_active' => false]);
```

### 3. Monitoring Performance
```php
// Cek performa perhitungan biaya
$start = microtime(true);
$fees = $adminFeeService->calculateTotalFees(50000, 'bank_transfer');
$end = microtime(true);
$executionTime = ($end - $start) * 1000; // dalam milidetik
```

## Kesimpulan

Sistem biaya admin ini memberikan fleksibilitas penuh untuk mengatur biaya aplikasi sesuai dengan kebutuhan bisnis. Dengan interface yang user-friendly dan API yang robust, admin dapat dengan mudah mengelola pengaturan biaya dan memantau pendapatan dari sistem lelang.

Fitur ini terintegrasi penuh dengan sistem lelang yang ada dan tidak mengganggu flow bisnis yang sudah berjalan. Semua perhitungan biaya dilakukan secara otomatis dan transparan kepada semua pihak yang terlibat.

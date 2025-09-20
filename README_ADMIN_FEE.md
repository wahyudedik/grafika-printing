# Fitur Biaya Admin - README

## 🚀 Fitur Baru: Sistem Biaya Admin yang Scalable

Sistem biaya admin yang dapat dikonfigurasi dari dashboard superadmin/dev untuk mengatur biaya aplikasi yang ditambahkan ke setiap lelang.

## ✨ Fitur Utama

### Dashboard Admin
- **Pengaturan Biaya Admin**: Kelola biaya tetap atau persentase
- **Preview Biaya**: Kalkulator biaya real-time
- **Statistik & Laporan**: Monitor pendapatan dan transaksi
- **Manajemen Status**: Aktif/nonaktif pengaturan biaya

### Jenis Biaya
- **Biaya Tetap**: Rp 5.000, Rp 10.000, dll
- **Biaya Persentase**: 5%, 10%, 15%, dll
- **Biaya Payment Gateway**: Xendit (1.5% - 2.9%)

### Kategori Biaya
- **auction**: Biaya untuk lelang
- **payment**: Biaya untuk pembayaran
- **transaction**: Biaya untuk transaksi

## 🎯 Flow Sistem

### 1. User Membuat Lelang
```
Budget: Rp 50.000
↓
Sistem menghitung biaya admin (10% = Rp 5.000)
↓
Total yang harus dibayar: Rp 55.000
```

### 2. Vendor Menawar
```
Vendor menawar: Rp 60.000
↓
Sistem menampilkan: Rp 60.000 + biaya admin
↓
User melihat total pembayaran: Rp 66.000
```

### 3. Pembayaran
```
User memilih vendor
↓
Sistem membuat payment link Xendit
↓
User membayar total biaya via Xendit
```

## 🛠️ Instalasi & Setup

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Jalankan Seeder
```bash
php artisan db:seed --class=AdminFeeSettingSeeder
```

### 3. Clear Cache
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

## 📊 Contoh Penggunaan

### Skenario 1: Lelang Kecil
```
Budget: Rp 50.000
Biaya Admin (10%): Rp 5.000
Biaya Payment Gateway (1.5%): Rp 750
Total Pembayaran: Rp 55.750
Vendor Menerima: Rp 50.000
Admin Menerima: Rp 5.750
```

### Skenario 2: Lelang Besar
```
Budget: Rp 1.500.000
Biaya Admin (5%): Rp 75.000
Biaya Payment Gateway (1.5%): Rp 22.500
Total Pembayaran: Rp 1.597.500
Vendor Menerima: Rp 1.500.000
Admin Menerima: Rp 97.500
```

## 🔧 Konfigurasi Default

### Biaya Admin Aplikasi
- **10% untuk lelang normal** (Rp 10.000 - Rp 10.000.000)
- **5% untuk lelang besar** (di atas Rp 1.000.000)
- **Rp 5.000 tetap** (opsional, nonaktif)

### Biaya Payment Gateway
- **Bank Transfer**: 1.5%
- **Credit Card**: 2.9%
- **E-Wallet**: 2.0%
- **Retail Outlet**: 1.0%

## 📱 Interface Admin

### Menu Baru di Dashboard Dev
- **Biaya Admin**: Kelola pengaturan biaya
- **Preview Biaya**: Kalkulator biaya
- **Transaksi**: Daftar transaksi biaya
- **Statistik**: Laporan pendapatan

### Fitur CRUD
- ✅ **Create**: Tambah pengaturan biaya baru
- ✅ **Read**: Lihat daftar pengaturan
- ✅ **Update**: Edit pengaturan yang ada
- ✅ **Delete**: Hapus pengaturan
- ✅ **Toggle**: Aktif/nonaktif pengaturan

## 🔍 Monitoring

### Dashboard Statistik
- Total transaksi biaya admin
- Total pendapatan admin
- Rata-rata persentase biaya
- Grafik tren pendapatan

### Laporan Transaksi
- Daftar semua transaksi biaya admin
- Filter berdasarkan tanggal, status, vendor
- Export data transaksi

## 🚨 Troubleshooting

### Biaya Admin Tidak Dihitung
1. Periksa status pengaturan (is_active = true)
2. Periksa tanggal efektif (effective_from/until)
3. Periksa range minimum/maksimum

### Preview Tidak Muncul
1. Periksa JavaScript console untuk error
2. Periksa route admin-fees.preview
3. Periksa CSRF token

### Statistik Tidak Akurat
1. Periksa filter tanggal
2. Periksa status transaksi
3. Periksa relasi database

## 📚 Dokumentasi Lengkap

Lihat file `ADMIN_FEE_SYSTEM_DOCUMENTATION.md` untuk dokumentasi teknis lengkap.

## 🎉 Status Implementasi

- ✅ **Database**: Migration dan seeder selesai
- ✅ **Model**: AdminFeeSetting dan AdminFeeService selesai
- ✅ **Controller**: AdminFeeController selesai
- ✅ **Views**: Semua view admin selesai
- ✅ **Routes**: Semua route admin selesai
- ✅ **Integration**: Terintegrasi dengan sistem lelang
- ✅ **Documentation**: Dokumentasi lengkap selesai

## 🔄 Next Steps

1. **Testing**: Test semua fitur biaya admin
2. **Monitoring**: Monitor performa sistem
3. **Optimization**: Optimasi query database jika diperlukan
4. **Backup**: Setup backup data transaksi

---

**Fitur biaya admin telah siap digunakan! 🎉**

Sistem ini memberikan fleksibilitas penuh untuk mengatur biaya aplikasi sesuai dengan kebutuhan bisnis, dengan interface yang user-friendly dan integrasi yang seamless dengan sistem lelang yang ada.

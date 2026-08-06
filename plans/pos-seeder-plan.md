# Rencana: POS Seeder - Lengkapi Data Test

## Overview
Membuat seeder lengkap untuk fitur POS (Point of Sale) yang mencakup produk, bahan, alat, spesifikasi, pelanggan, dan data transaksi.

## Model yang Perlu Di-seed

### 1. KategoriProduk (Product Categories)
**Table**: `kategori_produks` | **Tenant**: Ya (vendor_id)
- Kategori produk percetakan Indonesia
- Contoh: Kartu Nama, Undangan, Banner, Stiker, Dokumen, etc.

### 2. Spesifikasi (Specifications)
**Table**: `spesifikasis` | **Tenant**: Ya (vendor_id)
- Spesifikasi yang bisa dipilih untuk produk
- Tipe: number, select, text
- Contoh: Ukuran, Jenis Kertas, Finishing, Jumlah Halaman

### 3. Bahan (Materials)
**Table**: `bahans` | **Tenant**: Ya (vendor_id)
- Bahan baku percetakan dengan stok dan HPP
- Contoh: Kertas HVS, Kertas Art Paper, Tinta, Laminasi, etc.

### 4. Alat (Equipment/Tools)
**Table**: `alats` | **Tenant**: Ya (vendor_id)
- Peralatan cetak dengan kapasitas dan status
- Contoh: Mesin Cetak Digital, Mesin Potong, Mesin Laminasi, etc.

### 5. Produk (Products)
**Table**: `produks` | **Tenant**: Ya (vendor_id)
- Produk percetakan yang dijual via POS
- Link ke KategoriProduk

### 6. SpesifikasiProduk (Product Specifications)
**Table**: `spesifikasi_produks` | **Tenant**: Ya (vendor_id)
- Pivot antara Produk dan Spesifikasi
- Pilihan values, wajib/tidak

### 7. BahanSpesifikasiProduk (Pivot)
**Table**: `bahan_spesifikasi_produk`
- Pivot antara SpesifikasiProduk dan Bahan
- Menentukan bahan apa yang tersedia untuk spesifikasi produk

### 8. EstimasiProduk (Production Estimates)
**Table**: `estimasi_produks` | **Tenant**: Ya (vendor_id)
- Estimasi waktu produksi per produk per alat
- Waktu persiapan dan waktu produksi per unit

### 9. Pelanggan (Customers)
**Table**: `pelanggans` | **Tenant**: Ya (vendor_id)
- Data pelanggan POS
- Kode, nama, alamat, telepon, email

### 10. WholesalePrice (Harga Grosir)
**Table**: `harga_grosir` | **Tenant**: Ya (vendor_id)
- Harga grosir untuk bahan berdasarkan quantity
- Min/max quantity dan harga

## Data Seed

### KategoriProduk (6 kategori)
1. Kartu Nama & Undangan
2. Banner, Spanduk & X-Banner
3. Stiker & Label
4. Dokumen & Brosur
5. Packaging & Kemasan
6. Merchandise & Souvenir

### Spesifikasi (8 spesifikasi)
1. Ukuran (select: A4, A3, A5, F4, Custom)
2. Jenis Kertas (select: HVS 80gsm, Art Paper 150gsm, Art Carton 260gsm, etc.)
3. Finishing (select: Laminasi Doff, Laminasi Glossy, Potong, Folding, etc.)
4. Jumlah Halaman (number)
5. Warna Cetak (select: Hitam Putih, Full Color, Spot Color)
6. Jumlah Item (number)
7. Orientasi (select: Portrait, Landscape)
8. Keterangan Tambahan (text)

### Bahan (12 bahan)
1. Kertas HVS A4 80gsm (Rp 85.000/rim)
2. Kertas HVS F4 70gsm (Rp 75.000/rim)
3. Kertas Art Paper 150gsm (Rp 120.000/lembar)
4. Kertas Art Carton 260gsm (Rp 180.000/lembar)
5. Kertas Stiker Vinyl (Rp 95.000/meter)
6. Kertas Buffalo 250gsm (Rp 65.000/lembar)
7. Tinta Black (Rp 85.000/botol)
8. Tinta Color CMYK (Rp 320.000/set)
9. Laminasi Doff Film (Rp 45.000/meter)
10. Laminasi Glossy Film (Rp 45.000/meter)
11. Lem Panas (Rp 25.000/batang)
12. Tali Kur (Rp 15.000/roll)

### Alat (6 alat)
1. Mesin Cetak Digital Canon IR (aktif)
2. Mesin Potong Cutting Plotter (aktif)
3. Mesin Laminasi (maintenance)
4. Mesin Cetak Offset (aktif)
5. Mesin Potong Manual (aktif)
6. Komputer Desain (aktif)

### Produk (10 produk)
1. Kartu Nama Standar (kategori: Kartu Nama & Undangan)
2. Kartu Nama Premium (kategori: Kartu Nama & Undangan)
3. Undangan Pernikahan (kategori: Kartu Nama & Undangan)
4. Banner Indoor (kategori: Banner & Spanduk)
5. Banner Outdoor (kategori: Banner & Spanduk)
6. Stiker Vinyl (kategori: Stiker & Label)
7. Brosur A4 (kategori: Dokumen & Brosur)
8. Nota/Invoice (kategori: Dokumen & Brosur)
9. Box Kemasan (kategori: Packaging & Kemasan)
10. Tumbler Custom (kategori: Merchandise & Souvenir)

### Pelanggan (8 pelanggan)
Data pelanggan sample dengan berbagai tipe

### WholesalePrice (6 level harga grosir)
Harga grosir untuk beberapa bahan

## File yang Dibuat/Diupdate

1. **Baru**: `database/seeders/PosSeeder.php` - Seeder utama untuk POS data
2. **Update**: `database/seeders/DatabaseSeeder.php` - Tambah step POS seeder

## Urutan Execution

```
1. SimpleTestSeeder → Users & Vendor
2. CmsSettingsSeeder → CMS Settings
3. AdminFeeSettingsSeeder → Admin Fees
4. VendorWalletSeeder → Wallets
5. LinktreeSeeder → Linktree
6. LelangUserProfileSeeder → Lelang Profiles
7. PosSeeder → POS Data (BARU)
```

## Flow Testing

Setelah seeding, vendor bisa:
1. Login → Lihat dashboard dengan data
2. Buka POS → Lihat daftar produk
3. Tambah ke cart → Pilih spesifikasi
4. Checkout → Proses pembayaran
5. Cetak invoice → Thermal print
6. Lihat laporan penjualan

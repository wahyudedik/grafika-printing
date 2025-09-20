# Dummy Data Seeder Documentation

## 🎯 **Overview**
Seeder data dummy yang lengkap untuk semua fitur sistem Grafika Printing. Data ini akan membantu client memahami cara penggunaan sistem dengan data contoh yang realistis.

## 📊 **Data yang Dibuat**

### **1. DummyDataSeeder**
- ✅ **Users**: 15+ users (dev, vendor, regular users)
- ✅ **Vendors**: 5 vendors dengan data lengkap
- ✅ **Admin Fee Settings**: 3 pengaturan biaya admin
- ✅ **Vendor Wallets**: Wallet untuk setiap vendor

### **2. PosDataSeeder**
- ✅ **Categories**: 5 kategori produk per vendor
- ✅ **Materials**: 8 bahan baku per vendor
- ✅ **Tools**: 5 alat per vendor
- ✅ **Products**: 5 produk per vendor
- ✅ **Specifications**: 5 spesifikasi per vendor
- ✅ **Wholesale Prices**: Harga grosir untuk setiap bahan
- ✅ **Production Estimates**: Estimasi produksi

### **3. AuctionDataSeeder**
- ✅ **Auctions**: 2-4 lelang per user
- ✅ **Auction Bids**: 2-5 bid per lelang
- ✅ **Xendit Payments**: Payment record untuk lelang yang dibayar
- ✅ **Admin Fee Integration**: Perhitungan biaya admin otomatis

### **4. TransactionDataSeeder**
- ✅ **Customers**: 5 pelanggan per vendor
- ✅ **POS Transactions**: 10-20 transaksi per vendor
- ✅ **Transaction Items**: 1-3 item per transaksi
- ✅ **Transaction Specifications**: Spesifikasi untuk setiap item

### **5. WithdrawalDataSeeder**
- ✅ **Wallet Transactions**: 20-50 transaksi per vendor
- ✅ **Withdrawals**: 3-8 penarikan per vendor
- ✅ **Transaction History**: Riwayat transaksi lengkap

### **6. DeliveryConfirmationSeeder**
- ✅ **Delivery Confirmations**: 70% lelang yang dibayar
- ✅ **Vendor Payments**: Pembayaran otomatis ke vendor
- ✅ **Ratings & Feedback**: Rating dan feedback user
- ✅ **Dispute Cases**: Beberapa kasus dispute

### **7. AdminFeePaymentSeeder**
- ✅ **Admin Fee Transactions**: Transaksi biaya admin
- ✅ **Xendit Payments**: Payment gateway records
- ✅ **Payment Status**: Berbagai status pembayaran

## 🚀 **Cara Menjalankan Seeder**

### **1. Jalankan Semua Seeder**
```bash
php artisan db:seed
```

### **2. Jalankan Seeder Spesifik**
```bash
# Hanya data dasar
php artisan db:seed --class=DummyDataSeeder

# Hanya data POS
php artisan db:seed --class=PosDataSeeder

# Hanya data lelang
php artisan db:seed --class=AuctionDataSeeder

# Hanya data transaksi
php artisan db:seed --class=TransactionDataSeeder

# Hanya data withdrawal
php artisan db:seed --class=WithdrawalDataSeeder

# Hanya data delivery confirmation
php artisan db:seed --class=DeliveryConfirmationSeeder

# Hanya data admin fee
php artisan db:seed --class=AdminFeePaymentSeeder
```

### **3. Reset Database dan Jalankan Seeder**
```bash
php artisan migrate:fresh --seed
```

## 📋 **Data yang Dibuat**

### **Users (15+ users)**
- **Dev User**: admin@grafika.com (password: password)
- **Vendor Users**: 5 vendor dengan email dan password
- **Regular Users**: 8 user biasa

### **Vendors (5 vendors)**
- Ahmad Print Shop
- Budi Digital Printing
- Citra Offset Printing
- Dedi Screen Printing
- Eka Large Format

### **Auctions (20+ lelang)**
- Brosur promosi perusahaan
- Banner outdoor event
- Kartu nama perusahaan
- Kaos event komunitas
- Buku panduan perusahaan
- Sticker promosi
- Poster event
- Undangan pernikahan

### **POS Data (per vendor)**
- **Categories**: 5 kategori
- **Materials**: 8 bahan baku
- **Tools**: 5 alat
- **Products**: 5 produk
- **Specifications**: 5 spesifikasi
- **Customers**: 5 pelanggan
- **Transactions**: 10-20 transaksi

### **Wallet & Withdrawal**
- **Wallet Transactions**: 20-50 per vendor
- **Withdrawals**: 3-8 per vendor
- **Various Status**: pending, approved, completed, failed

### **Delivery Confirmations**
- **Confirmed Deliveries**: 80% dari lelang yang dibayar
- **Disputed Cases**: 15% dari lelang yang dibayar
- **Resolved Cases**: 5% dari lelang yang dibayar
- **Ratings**: 1-5 bintang
- **Feedback**: Komentar user

## 🎮 **Fitur yang Bisa Dicoba**

### **1. Login sebagai Admin**
```
Email: admin@grafika.com
Password: password
```
- Lihat dashboard admin
- Kelola biaya admin
- Monitor semua transaksi
- Resolve dispute

### **2. Login sebagai Vendor**
```
Email: ahmad@printshop.com
Password: password
```
- Lihat dashboard vendor
- Kelola produk dan bahan
- Lihat transaksi POS
- Kelola withdrawal

### **3. Login sebagai User**
```
Email: john@example.com
Password: password
```
- Buat lelang baru
- Lihat lelang aktif
- Konfirmasi barang
- Rating vendor

## 📊 **Statistik Data**

### **Setelah Seeder Berjalan:**
- **Users**: 15+ users
- **Vendors**: 5 vendors
- **Auctions**: 20+ lelang
- **Bids**: 50+ bid
- **POS Transactions**: 50+ transaksi
- **Withdrawals**: 20+ penarikan
- **Delivery Confirmations**: 15+ konfirmasi
- **Admin Fee Transactions**: 20+ transaksi
- **Xendit Payments**: 20+ payment

## 🔧 **Konfigurasi Data**

### **Admin Fee Settings**
- **10% untuk lelang normal** (Rp 10.000 - Rp 1.000.000)
- **5% untuk lelang besar** (Rp 1.000.000+)
- **Rp 5.000 tetap** (nonaktif)

### **Payment Methods**
- Bank Transfer (BCA, BNI, BRI, MANDIRI, BSI, PERMATA)
- E-Wallet (OVO, DANA, LINKAJA, SHOPEEPAY)
- Retail Outlet (ALFAMART, INDOMARET)

### **Transaction Status**
- **Pending**: 20%
- **Paid**: 70%
- **Failed**: 15%
- **Refunded**: 5%

### **Delivery Status**
- **Delivered**: 80%
- **Disputed**: 15%
- **Resolved**: 5%

## 🎯 **Keuntungan Data Dummy**

### **1. Untuk Client**
- ✅ Bisa langsung lihat fitur tanpa input data
- ✅ Memahami flow sistem dengan data realistis
- ✅ Bisa test semua fitur dengan data yang ada
- ✅ Tidak perlu input data manual

### **2. Untuk Development**
- ✅ Data untuk testing
- ✅ Data untuk demo
- ✅ Data untuk dokumentasi
- ✅ Data untuk training

### **3. Untuk User Experience**
- ✅ Dashboard tidak kosong
- ✅ Bisa langsung explore fitur
- ✅ Memahami cara penggunaan
- ✅ Data yang realistis

## 🚨 **Catatan Penting**

### **1. Data Dummy**
- Data ini hanya untuk demo dan testing
- Bukan data real production
- Bisa dihapus kapan saja
- Tidak mempengaruhi sistem production

### **2. Login Credentials**
- Semua user menggunakan password: `password`
- Email sudah terverifikasi
- User sudah aktif

### **3. Data Relationships**
- Semua data saling berhubungan
- Flow sistem sudah lengkap
- Data konsisten antar tabel

## 🎉 **Kesimpulan**

Seeder ini memberikan data dummy yang lengkap untuk semua fitur sistem Grafika Printing. Client bisa langsung:

1. **Login** dengan berbagai role
2. **Explore** semua fitur
3. **Test** flow sistem
4. **Understand** cara penggunaan
5. **Demo** kepada stakeholder

**Data dummy ini membuat sistem terlihat profesional dan siap digunakan!** 🎉

# 📊 Seeder Documentation

## 🎯 Overview
Seeder ini dibuat untuk mengisi database dengan data dummy yang realistis untuk testing dan development.

## 📁 Available Seeders

### 1. **UserSeeder**
- **File**: `database/seeders/UserSeeder.php`
- **Purpose**: Membuat 20 user tambahan
- **Data**: User dengan email `user1@example.com` sampai `user20@example.com`
- **Password**: `password` (untuk semua user)

### 2. **VendorSeeder**
- **File**: `database/seeders/VendorSeeder.php`
- **Purpose**: Membuat 10 vendor tambahan
- **Data**: Vendor dengan nama dan detail yang realistis
- **Features**: Setiap vendor memiliki user account yang terhubung

### 3. **AuctionSeeder**
- **File**: `database/seeders/AuctionSeeder.php`
- **Purpose**: Membuat 100 pengajuan lelang
- **Data**: Lelang dengan berbagai status dan detail yang realistis

### 4. **FullSeeder**
- **File**: `database/seeders/FullSeeder.php`
- **Purpose**: Menjalankan semua seeder sekaligus
- **Features**: Membersihkan data lama sebelum seeding

## 🚀 How to Run

### Run Individual Seeder
```bash
# Run specific seeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=VendorSeeder
php artisan db:seed --class=AuctionSeeder
```

### Run All Seeders
```bash
# Run all seeders
php artisan db:seed

# Or run full seeder
php artisan db:seed --class=FullSeeder
```

### Fresh Database with Seeding
```bash
# Fresh migration with seeding
php artisan migrate:fresh --seed
```

## 📊 Data Generated

### Users (23 total)
- 3 default users (dev, vendor, user)
- 20 additional users from seeder

### Vendors (11 total)
- 1 default vendor (Grafika Printing)
- 10 additional vendors from seeder

### Auctions (100+ total)
- **Active**: ~78 auctions
- **Waiting Payment**: ~10 auctions
- **Paid**: ~9 auctions
- **Closed**: ~8 auctions

### Bids (200+ total)
- 1-5 bids per active auction
- Random bid amounts (lower than budget)
- Various bid statuses

## 🎨 Data Variety

### Auction Categories
- Banner & Spanduk
- Stiker & Label
- Kartu Nama
- Brosur & Flyer
- Poster & Banner
- Undangan
- Buku & Katalog
- Kemasan & Packaging
- Bendera & Umbul-umbul
- Baju & Merchandise
- Banner Digital
- Stiker Kaca
- Kartu Undangan
- Brosur Promosi
- Poster Event

### Sample Titles
- "Cetak Banner Promosi Event"
- "Brosur Produk Baru"
- "Kartu Nama Perusahaan"
- "Stiker Label Produk"
- "Poster Acara Musik"
- "Bendera Event"
- "Kemasan Produk Makanan"
- "Undangan Pernikahan"
- "Buku Katalog Produk"
- "Banner Digital LED"

### Budget Range
- **Minimum**: Rp 50,000
- **Maximum**: Rp 5,000,000
- **Average**: ~Rp 2,500,000

### Quantity Range
- **Minimum**: 1 pcs
- **Maximum**: 1,000 pcs
- **Average**: ~500 pcs

## 🔧 Customization

### Modify Auction Count
Edit `AuctionSeeder.php` line 85:
```php
for ($i = 1; $i <= 100; $i++) { // Change 100 to desired number
```

### Modify User Count
Edit `UserSeeder.php` line 15:
```php
for ($i = 1; $i <= 20; $i++) { // Change 20 to desired number
```

### Modify Vendor Count
Edit `VendorSeeder.php` - add more vendors to the `$vendors` array

## 📈 Statistics After Seeding

```
Total Users: 33
Total Vendors: 11
Total Auctions: 105
Total Bids: 231
```

## 🎯 Use Cases

1. **Development Testing**: Test fitur dengan data yang realistis
2. **Performance Testing**: Test aplikasi dengan data dalam jumlah besar
3. **Demo Purposes**: Demo aplikasi dengan data yang menarik
4. **UI/UX Testing**: Test interface dengan berbagai status lelang

## ⚠️ Important Notes

1. **Data Reset**: Seeder akan menghapus data lama (kecuali user default)
2. **Password**: Semua user dummy menggunakan password `password`
3. **Email**: Email dummy menggunakan domain `@example.com`
4. **Status Distribution**: Status lelang didistribusikan secara random
5. **Bid Generation**: Bid hanya dibuat untuk lelang dengan status `active`

## 🔄 Reset Data

Untuk menghapus semua data dummy:
```bash
php artisan tinker
```
```php
\App\Models\AuctionBid::truncate();
\App\Models\Auction::truncate();
\App\Models\Vendor::where('id', '>', 1)->delete();
\App\Models\User::where('id', '>', 3)->delete();
```

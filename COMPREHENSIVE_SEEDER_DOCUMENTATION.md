# Comprehensive Dummy Data Seeder - Dokumentasi Lengkap

## Overview
Seeder komprehensif yang menghasilkan data dummy yang realistis untuk semua fitur sistem Grafika Printing. Seeder ini dirancang untuk testing dan development yang lebih efektif.

## Fitur yang Dicover

### 1. **User Management**
- ✅ **Dev User**: Admin dengan akses penuh
- ✅ **Vendor Users**: 5 vendor dengan data lengkap
- ✅ **Regular Users**: 7 user biasa untuk testing lelang

### 2. **Vendor Management**
- ✅ **Vendor Data**: Informasi lengkap vendor
- ✅ **Auto Withdrawal**: Konfigurasi penarikan otomatis
- ✅ **Bank Accounts**: Data rekening bank dan e-wallet

### 3. **Auction System**
- ✅ **Auctions**: 50 lelang dengan berbagai status
- ✅ **Auction Bids**: Penawaran dari vendor dengan status berbeda
- ✅ **Winner Selection**: Pemenang lelang yang realistis

### 4. **Payment System**
- ✅ **Xendit Payments**: Data pembayaran dengan berbagai status
- ✅ **Admin Fee Transactions**: Transaksi biaya admin
- ✅ **Payment Methods**: Berbagai metode pembayaran

### 5. **Transaction Management**
- ✅ **POS Transactions**: Transaksi di sistem POS vendor
- ✅ **Wallet Transactions**: Transaksi wallet vendor
- ✅ **Withdrawals**: Data penarikan dengan berbagai status

### 6. **Delivery & Rating System**
- ✅ **Delivery Confirmations**: Konfirmasi pengiriman
- ✅ **Vendor Ratings**: Rating dan review vendor
- ✅ **Shipping Invoices**: Invoice pengiriman

### 7. **Admin Fee System**
- ✅ **Admin Fee Settings**: Pengaturan biaya admin
- ✅ **Fee Calculations**: Perhitungan biaya yang realistis
- ✅ **Fee Transactions**: Transaksi biaya admin

## Data yang Dihasilkan

### **Users (12 total)**
- 1 Dev user (admin@grafika.com)
- 5 Vendor users
- 7 Regular users

### **Vendors (5 total)**
- Ahmad Print Shop
- Budi Digital Printing
- Citra Offset Printing
- Diana Print Solutions
- Eko Fast Print

### **Auctions (50 total)**
- Berbagai kategori: Banner, Sticker, Brochure, dll.
- Status: pending, active, closed, waiting_payment, paid, completed, rejected
- Budget: Rp 50.000 - Rp 5.000.000
- Deadline: 1-30 hari ke depan

### **Auction Bids (1-5 per auction)**
- Penawaran 70-100% dari budget
- Status: pending, accepted, rejected
- Notes dan spesifikasi lengkap

### **Transactions (POS)**
- Transaksi yang terintegrasi dengan lelang
- Status: in_progress, completed
- Progress percentage: 20-100%

### **Xendit Payments**
- Status: pending, paid, failed, expired
- Payment methods: BCA, BNI, BRI, OVO, DANA, dll.
- Tracking dan webhook data

### **Wallet Transactions (10-30 per vendor)**
- Credit: Pendapatan dari lelang
- Debit: Penarikan dan biaya
- Categories: auction_win, withdrawal, refund, bonus

### **Withdrawals (2-8 per vendor)**
- Status: pending, approved, processing, completed, rejected
- Methods: bank_transfer, ewallet
- Amount: Rp 500.000 - Rp 5.000.000

### **Delivery Confirmations**
- Status: shipped, delivered, confirmed, disputed
- Rating: 3-5 bintang
- Feedback dan dispute resolution

### **Vendor Ratings**
- Rating: 3-5 bintang
- Review yang realistis
- Verified ratings

### **Shipping Invoices**
- Courier: JNE, J&T, Pos Indonesia
- Service: REG, EXPRESS, OKE
- Tracking numbers
- Shipping costs: Rp 15.000 - Rp 50.000

### **Admin Fee Transactions**
- Fee calculations yang akurat
- Breakdown biaya admin
- Payment gateway fees

## Cara Menjalankan

### 1. **Seeder Standar**
```bash
php artisan db:seed --class=ComprehensiveDummyDataSeeder
```

### 2. **Reset Database + Seed**
```bash
php artisan migrate:fresh --seed
```

### 3. **Command Khusus**
```bash
# Seed dengan reset database
php artisan seed:comprehensive --fresh

# Seed tanpa reset
php artisan seed:comprehensive
```

### 4. **Database Seeder**
```bash
php artisan db:seed
```

## Struktur Data

### **Realistic Relationships**
- ✅ Auctions → Bids → Winners
- ✅ Payments → Transactions → Wallet
- ✅ Deliveries → Confirmations → Ratings
- ✅ Admin Fees → Calculations → Transactions

### **Status Progression**
- ✅ Auction: pending → active → closed → waiting_payment → paid → completed
- ✅ Bid: pending → accepted/rejected
- ✅ Payment: pending → paid/failed/expired
- ✅ Withdrawal: pending → approved → processing → completed
- ✅ Delivery: shipped → delivered → confirmed

### **Financial Data**
- ✅ Admin fees: 10% (normal), 5% (large)
- ✅ Payment gateway: 1.5% (bank transfer)
- ✅ Withdrawal fees: 1% (bank), 2% (ewallet)
- ✅ Shipping costs: Rp 15.000 - Rp 50.000

## Testing Scenarios

### **1. Admin Dashboard**
- ✅ View all auctions with different statuses
- ✅ Moderate pending auctions
- ✅ Manage payment issues
- ✅ Monitor vendor revenue

### **2. Vendor Dashboard**
- ✅ View available auctions
- ✅ Place bids on auctions
- ✅ Manage wallet and withdrawals
- ✅ Track order progress

### **3. User Dashboard**
- ✅ Create auction requests
- ✅ View auction status
- ✅ Make payments
- ✅ Confirm deliveries

### **4. Payment Flow**
- ✅ Create payment links
- ✅ Process payments
- ✅ Handle webhooks
- ✅ Update auction status

### **5. Delivery System**
- ✅ Generate shipping invoices
- ✅ Track shipments
- ✅ Confirm deliveries
- ✅ Rate vendors

## Data Quality

### **Realistic Values**
- ✅ Budgets: Rp 50.000 - Rp 5.000.000
- ✅ Quantities: 100 - 5.000 pieces
- ✅ Deadlines: 1-30 days
- ✅ Ratings: 3-5 stars
- ✅ Fees: Realistic percentages

### **Proper Relationships**
- ✅ Foreign keys properly set
- ✅ Timestamps in logical order
- ✅ Status transitions make sense
- ✅ Financial calculations accurate

### **Testing Coverage**
- ✅ All user types covered
- ✅ All auction statuses represented
- ✅ All payment statuses included
- ✅ All delivery statuses covered

## Maintenance

### **Adding New Data**
1. Edit `ComprehensiveDummyDataSeeder.php`
2. Add new data creation methods
3. Update summary display
4. Test with fresh database

### **Modifying Existing Data**
1. Update data generation logic
2. Adjust relationships
3. Update status progressions
4. Test with fresh database

### **Performance**
- ✅ Batch operations for large datasets
- ✅ Transaction wrapping for data integrity
- ✅ Proper error handling
- ✅ Progress indicators

## Troubleshooting

### **Common Issues**
1. **Foreign Key Errors**: Check model relationships
2. **Duplicate Data**: Use `firstOrCreate` instead of `create`
3. **Memory Issues**: Process data in batches
4. **Status Errors**: Ensure logical status progression

### **Debug Mode**
```bash
# Run with verbose output
php artisan seed:comprehensive --fresh -v

# Check specific model counts
php artisan tinker
>>> App\Models\Auction::count()
>>> App\Models\AuctionBid::count()
```

## Conclusion

Seeder komprehensif ini memberikan data dummy yang realistis dan lengkap untuk testing semua fitur sistem Grafika Printing. Dengan data yang berkualitas tinggi, development dan testing menjadi lebih efektif dan menyeluruh.

**Key Benefits:**
- ✅ **Complete Coverage**: Semua fitur dicover
- ✅ **Realistic Data**: Data yang masuk akal
- ✅ **Proper Relationships**: Relasi yang benar
- ✅ **Easy Testing**: Mudah untuk testing
- ✅ **Maintainable**: Mudah di-maintain

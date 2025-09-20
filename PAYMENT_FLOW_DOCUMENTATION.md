# Payment Flow Documentation - Sistem Lelang Grafika Printing

## 🎯 **Flow Sistem yang Benar**

### **1. User Membuat Lelang**
```
User → Buat lelang → Set budget → Publish
```
- ✅ User set budget (contoh: Rp 50.000)
- ✅ Admin fee otomatis dihitung (contoh: 10% = Rp 5.000)
- ✅ Payment gateway fee (contoh: 1.5% = Rp 750)
- ✅ Total yang harus dibayar: Rp 55.750

### **2. Vendor Menawar**
```
Vendor → Lihat lelang → Submit bid → User pilih vendor
```
- ✅ Vendor submit bid (contoh: Rp 45.000)
- ✅ User lihat total: Rp 45.000 + Rp 4.500 (admin fee) + Rp 675 (gateway) = Rp 50.175
- ✅ User pilih vendor terbaik

### **3. User Bayar Lelang**
```
User → Bayar via Xendit → Status "Settled" → Uang masuk ke Admin
```
- ✅ User bayar total: Rp 50.175
- ✅ Uang masuk ke akun admin Xendit
- ✅ Status auction: `paid`
- ❌ Vendor belum dapat bayar (menunggu konfirmasi)

### **4. Vendor Mulai Cetak**
```
Vendor → Terima order → Mulai cetak → Status: "processing"
```
- ✅ Vendor bisa lihat order di dashboard
- ✅ Vendor mulai proses cetak
- ❌ Vendor belum dapat bayar (menunggu konfirmasi)

### **5. Vendor Kirim Barang**
```
Vendor → Kirim barang → User terima → User bayar ongkir CASH
```
- ✅ Vendor kirim barang
- ✅ User bayar ongkir CASH saat terima barang
- ✅ Ongkir tidak masuk sistem (bayar langsung ke kurir)
- ✅ Contoh: Ongkir Rp 15.000 (bayar CASH ke kurir)

### **6. User Konfirmasi Barang**
```
User → Terima barang → Konfirmasi via form → Vendor dapat bayar
```
- ✅ User konfirmasi barang diterima
- ✅ Rating dan feedback untuk vendor
- ✅ Upload foto sebagai bukti
- ✅ Vendor otomatis dapat bayar: Rp 45.000 (minus admin fee)

### **7. Jika Ada Masalah**
```
User → Laporkan masalah → Dispute system → Admin resolve
```
- ✅ User bisa laporkan masalah
- ✅ Admin bisa resolve dengan pilihan:
  - Refund penuh
  - Refund sebagian
  - Rework (vendor perbaiki)

## 💰 **Breakdown Pembayaran**

### **Saat User Bayar:**
```
Lelang:           Rp 45.000
Admin Fee (10%):  Rp  4.500
Gateway Fee:      Rp    675
─────────────────────────
Total Bayar:      Rp 50.175
```

### **Saat Vendor Dapat Bayar:**
```
Lelang:           Rp 45.000
Admin Fee:        Rp  4.500 (dipotong)
─────────────────────────
Vendor Dapat:     Rp 40.500
```

### **Ongkir (CASH):**
```
User Bayar:       Rp 15.000 (CASH ke kurir)
Tidak masuk sistem
```

## 🔄 **Status Flow**

### **Auction Status:**
1. `active` → Lelang aktif, vendor bisa bid
2. `closed` → Lelang ditutup, ada pemenang
3. `paid` → User sudah bayar, vendor mulai cetak
4. `completed` → Barang dikonfirmasi, vendor dapat bayar

### **Delivery Status:**
1. `pending` → Menunggu konfirmasi user
2. `delivered` → User konfirmasi barang OK
3. `confirmed` → Vendor dapat bayar
4. `disputed` → Ada masalah, perlu resolusi
5. `resolved` → Masalah sudah diselesaikan

## 🎮 **Fitur yang Sudah Dibuat**

### **✅ Delivery Confirmation System**
- Form konfirmasi barang dengan rating
- Upload foto sebagai bukti
- Feedback untuk vendor
- Dispute system

### **✅ Automatic Payment**
- Vendor otomatis dapat bayar setelah konfirmasi
- Admin fee otomatis dipotong
- Log transaksi lengkap

### **✅ Dispute Resolution**
- Admin bisa resolve dispute
- Pilihan: refund, partial refund, rework
- Tracking resolusi

### **✅ Rating System**
- User bisa rating vendor 1-5 bintang
- Feedback untuk vendor
- Reputasi vendor

## 🚨 **Masalah yang Diselesaikan**

### **❌ Masalah Sebelumnya:**
- Vendor tidak tahu kapan bisa mulai kerja
- Tidak ada konfirmasi barang sampai
- Tidak ada sistem dispute
- Tidak ada rating system

### **✅ Solusi Sekarang:**
- Vendor bisa mulai kerja setelah user bayar
- User konfirmasi barang via form
- Sistem dispute untuk masalah
- Rating system untuk kualitas

## 📊 **Database Schema**

### **Tabel: delivery_confirmations**
```sql
- auction_id (bigint) - ID lelang
- user_id (bigint) - ID user
- vendor_id (bigint) - ID vendor
- delivery_status (enum) - Status pengiriman
- user_rating (integer) - Rating 1-5 bintang
- user_feedback (text) - Feedback user
- photos (json) - Array URL foto
- dispute_reason (text) - Alasan dispute
```

### **Tabel: auctions (updated)**
```sql
- admin_fee_amount (decimal) - Biaya admin
- payment_gateway_fee (decimal) - Biaya gateway
- total_amount_with_fees (decimal) - Total dengan biaya
- vendor_receives (decimal) - Yang diterima vendor
- admin_receives (decimal) - Yang diterima admin
```

## 🔧 **API Endpoints**

### **User Endpoints**
```
GET    /user/delivery-confirmation/{auction}/create
POST   /user/delivery-confirmation/{auction}
GET    /user/delivery-confirmation/{confirmation}
```

### **Vendor Endpoints**
```
GET    /vendor/delivery-confirmation/{confirmation}
POST   /vendor/delivery-confirmation/{confirmation}/confirm
```

### **Admin Endpoints**
```
GET    /admin/delivery-confirmation
POST   /admin/delivery-confirmation/{confirmation}/resolve
```

## 🎯 **Keuntungan Sistem**

### **🟢 Untuk User:**
- Transparent payment flow
- Bisa konfirmasi barang sampai
- Rating vendor untuk kualitas
- Dispute system untuk masalah

### **🟢 Untuk Vendor:**
- Tahu kapan bisa mulai kerja
- Dapat bayar setelah konfirmasi
- Rating system untuk reputasi
- Dispute resolution yang fair

### **🟢 Untuk Admin:**
- Control penuh atas payment flow
- Admin fee terjamin
- Quality control via rating
- Dispute resolution system

## 🚀 **Next Steps**

### **Fitur Tambahan:**
1. **Notifikasi Email/SMS** → Notify semua pihak
2. **Tracking Pengiriman** → Real-time tracking
3. **Chat System** → Komunikasi user-vendor
4. **Auto-reminder** → Reminder konfirmasi
5. **Mobile App** → Mobile experience

### **Integrasi:**
1. **Shipping API** → Integrasi kurir
2. **Payment Gateway** → Refund otomatis
3. **Notification Service** → Email/SMS/Push
4. **Analytics** → Business intelligence

## 🎉 **Kesimpulan**

Sistem ini menyelesaikan semua masalah:
- ✅ **Kapan vendor bisa mulai kerja?** → Setelah user bayar lelang
- ✅ **Kapan vendor dapat bayar?** → Setelah user konfirmasi barang
- ✅ **Bagaimana konfirmasi barang?** → Via form dengan rating dan foto
- ✅ **Bagaimana jika ada masalah?** → Dispute system dengan resolusi
- ✅ **Siapa yang bayar ongkir?** → User bayar CASH ke kurir

**Sistem ini fair untuk semua pihak dan memastikan kualitas layanan!** 🎉

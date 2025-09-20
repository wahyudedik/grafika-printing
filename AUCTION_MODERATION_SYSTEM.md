# 🛡️ Sistem Moderasi Lelang - Dokumentasi Lengkap

## Overview
Sistem moderasi lelang yang memungkinkan superadmin untuk mengontrol konten lelang sebelum dipublikasikan. Fitur ini mencegah konten yang tidak pantas seperti gambar porno, spam, atau konten yang melanggar kebijakan.

## 🎯 **Fitur Utama**

### 1. **Moderasi Konten**
- ✅ **Approve Lelang**: Setujui lelang yang sesuai dengan kebijakan
- ✅ **Reject Lelang**: Tolak lelang dengan alasan yang jelas
- ✅ **Filter Status**: Lihat lelang berdasarkan status (pending, active, rejected)
- ✅ **Notifikasi User**: User mendapat notifikasi saat lelang di-approve/reject

### 2. **Dashboard Moderasi**
- ✅ **Pending Auctions**: Lelang yang menunggu persetujuan
- ✅ **Quick Actions**: Tombol approve/reject langsung dari daftar
- ✅ **Status Indicators**: Badge warna untuk status lelang
- ✅ **Rejection Reasons**: Alasan penolakan yang jelas

### 3. **Sistem Notifikasi**
- ✅ **Email Notification**: Notifikasi via email ke user
- ✅ **Database Notification**: Notifikasi tersimpan di database
- ✅ **Approval Notification**: Notifikasi saat lelang disetujui
- ✅ **Rejection Notification**: Notifikasi saat lelang ditolak dengan alasan

## 🔧 **Cara Menggunakan**

### **1. Akses Dashboard Moderasi**
```
URL: /admin/auctions
Login sebagai: admin@grafika.com
Password: password
```

### **2. Filter Lelang**
- **Semua**: Lihat semua lelang
- **Pending**: Lelang yang menunggu persetujuan
- **Aktif**: Lelang yang sudah disetujui
- **Ditolak**: Lelang yang ditolak

### **3. Moderasi Lelang**

#### **Approve Lelang**
1. Klik tombol "Setujui" pada lelang pending
2. Konfirmasi persetujuan
3. Lelang otomatis menjadi aktif
4. User mendapat notifikasi approval

#### **Reject Lelang**
1. Klik tombol "Tolak" pada lelang pending
2. Masukkan alasan penolakan (wajib)
3. Konfirmasi penolakan
4. Lelang status menjadi "rejected"
5. User mendapat notifikasi dengan alasan

## 📊 **Database Structure**

### **Tabel: auctions (Field Tambahan)**
```sql
- rejection_reason (text) - Alasan penolakan
- rejected_by (bigint) - ID admin yang menolak
- rejected_at (datetime) - Waktu penolakan
- approved_by (bigint) - ID admin yang menyetujui
- approved_at (datetime) - Waktu persetujuan
```

### **Tabel: notifications**
```sql
- user_id (bigint) - ID user yang mendapat notifikasi
- type (varchar) - 'auction_approved' atau 'auction_rejected'
- data (json) - Data notifikasi (auction_id, reason, dll)
- read_at (datetime) - Waktu dibaca
```

## 🚀 **API Endpoints**

### **Admin Auction Management**
```
GET    /admin/auctions              - Daftar lelang dengan filter
GET    /admin/auctions/{id}         - Detail lelang
POST   /admin/auctions/{id}/approve - Setujui lelang
POST   /admin/auctions/{id}/reject  - Tolak lelang
GET    /admin/auctions/statistics   - Statistik lelang
```

## 📧 **Sistem Notifikasi**

### **1. Auction Approved Notification**
```php
// Email Content
Subject: Lelang Anda Disetujui - {auction_title}
Body: 
- Selamat! Lelang Anda telah disetujui
- Detail lelang (judul, budget, deadline)
- Link ke lelang
```

### **2. Auction Rejected Notification**
```php
// Email Content
Subject: Lelang Anda Ditolak - {auction_title}
Body:
- Lelang Anda telah ditolak
- Alasan penolakan
- Saran untuk membuat lelang baru
```

## 🎨 **UI/UX Features**

### **1. Status Badges**
- 🟡 **Pending**: Lelang menunggu persetujuan
- 🟢 **Active**: Lelang sudah aktif
- 🔴 **Rejected**: Lelang ditolak
- ⚫ **Closed**: Lelang sudah ditutup

### **2. Quick Actions**
- ✅ **Approve Button**: Setujui lelang dengan satu klik
- ❌ **Reject Button**: Tolak lelang dengan modal alasan
- 👁️ **Detail Button**: Lihat detail lelang lengkap

### **3. Filter Tabs**
- **Semua**: Semua lelang
- **Pending**: Hanya lelang pending
- **Aktif**: Hanya lelang aktif
- **Ditolak**: Hanya lelang ditolak

## 🔒 **Keamanan & Validasi**

### **1. Akses Kontrol**
- Hanya user dengan role `dev` yang dapat mengakses
- Middleware `DevMiddleware` untuk proteksi

### **2. Validasi Data**
- Alasan penolakan wajib diisi (max 500 karakter)
- Hanya lelang dengan status 'pending' yang bisa di-approve/reject
- Validasi input untuk semua form

### **3. Audit Trail**
- Log semua aksi approve/reject
- Tracking admin yang melakukan aksi
- Timestamp untuk semua operasi

## 📈 **Statistik & Monitoring**

### **1. Dashboard Statistik**
- Total lelang pending
- Total lelang aktif
- Total lelang ditolak
- Persentase approval rate

### **2. Laporan Moderasi**
- Lelang yang paling sering ditolak
- Alasan penolakan yang umum
- Performa moderasi admin

## 🛠️ **Maintenance**

### **1. Backup Data**
```bash
# Backup tabel auctions
mysqldump -u username -p database_name auctions > auctions_backup.sql

# Backup tabel notifications
mysqldump -u username -p database_name notifications > notifications_backup.sql
```

### **2. Monitoring Performance**
```php
// Cek performa moderasi
$pendingCount = Auction::where('status', 'pending')->count();
$approvalRate = Auction::where('status', 'active')->count() / Auction::count() * 100;
```

## 🎯 **Contoh Skenario**

### **Skenario 1: Lelang Normal**
1. User membuat lelang "Cetak Banner 3x1 meter"
2. Status otomatis menjadi "pending"
3. Admin review lelang
4. Admin approve lelang
5. Status menjadi "active"
6. User mendapat notifikasi approval

### **Skenario 2: Lelang Bermasalah**
1. User membuat lelang dengan konten tidak pantas
2. Status otomatis menjadi "pending"
3. Admin review lelang
4. Admin reject dengan alasan "Konten tidak pantas"
5. Status menjadi "rejected"
6. User mendapat notifikasi dengan alasan

### **Skenario 3: Lelang Spam**
1. User membuat lelang dengan judul spam
2. Status otomatis menjadi "pending"
3. Admin review lelang
4. Admin reject dengan alasan "Judul tidak sesuai"
5. Status menjadi "rejected"
6. User mendapat notifikasi dengan alasan

## 🚀 **Fitur Tambahan yang Bisa Dikembangkan**

### **1. Auto-Moderation**
- AI untuk deteksi konten tidak pantas
- Auto-reject lelang dengan kata kunci tertentu
- Machine learning untuk klasifikasi konten

### **2. Bulk Actions**
- Approve/reject multiple lelang sekaligus
- Template alasan penolakan
- Batch processing untuk efisiensi

### **3. Advanced Filtering**
- Filter berdasarkan kategori
- Filter berdasarkan budget range
- Filter berdasarkan tanggal
- Search lelang berdasarkan keyword

## 📝 **Kesimpulan**

Sistem moderasi lelang ini memberikan kontrol penuh kepada superadmin untuk mengatur konten yang dipublikasikan. Dengan fitur approve/reject yang mudah digunakan, notifikasi yang informatif, dan audit trail yang lengkap, sistem ini memastikan kualitas konten yang baik dan melindungi platform dari konten yang tidak pantas.

Fitur ini terintegrasi penuh dengan sistem lelang yang ada dan tidak mengganggu flow bisnis yang sudah berjalan. Semua aksi moderasi dilakukan secara transparan dengan notifikasi yang jelas kepada semua pihak yang terlibat.

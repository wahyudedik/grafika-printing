# Setup Webhook untuk Development Lokal

## 🚀 Menggunakan ngrok (Recommended)

### 1. Install ngrok
```bash
# Download dari https://ngrok.com/download
# Atau install via package manager
```

### 2. Setup ngrok
```bash
# Daftar akun ngrok (gratis)
# Login ke https://dashboard.ngrok.com
# Dapatkan authtoken dari dashboard

# Set authtoken
ngrok config add-authtoken YOUR_AUTHTOKEN_HERE 
```

### 3. Jalankan ngrok
```bash
# Buka terminal baru, jalankan ngrok
ngrok http 8000

# Output akan seperti ini:
# Forwarding  https://abc123.ngrok.io -> http://localhost:8000
```

### 4. Set Webhook URL di Xendit Dashboard
```
Webhook URL: https://abc123.ngrok.io/xendit/webhook
```

## 🔧 Alternatif: Menggunakan Expose (Laravel)

### 1. Install Expose
```bash
composer global require beyondcode/expose
```

### 2. Jalankan Expose
```bash
# Di terminal terpisah
expose share http://localhost:8000

# Output akan seperti:
# Your share URL is: https://abc123.expose.sh
```

### 3. Set Webhook URL
```
Webhook URL: https://abc123.expose.sh/xendit/webhook
```

## 🛠️ Setup untuk Testing

### 1. Update Environment Variables
```env
# .env
XENDIT_CALLBACK_URL=https://abc123.ngrok.io/xendit/webhook
```

### 2. Test Webhook
```bash
# Test webhook endpoint
curl -X POST https://abc123.ngrok.io/xendit/webhook \
  -H "Content-Type: application/json" \
  -H "x-xendit-signature: your_webhook_token" \
  -d '{"test": "webhook"}'
```

### 3. Monitor Logs
```bash
# Monitor Laravel logs
tail -f storage/logs/laravel.log

# Atau gunakan ngrok web interface
# Buka http://localhost:4040 untuk melihat requests
```

## 📱 Setup di Xendit Dashboard

### 1. Login ke Xendit Dashboard
- Buka https://dashboard.xendit.co
- Login dengan akun Anda

### 2. Navigate ke Webhook Settings
- Klik **Settings** → **Developers** → **Webhooks**
- Atau langsung ke: https://dashboard.xendit.co/settings/developers#webhooks

### 3. Set Webhook URLs
Untuk setiap produk yang Anda gunakan, set URL webhook:

**Payment Links:**
```
Webhook URL: https://abc123.ngrok.io/xendit/webhook
```

**XenPayment:**
```
Webhook URL: https://abc123.ngrok.io/xendit/webhook
```

### 4. Test Webhook
- Klik tombol **"Test and Save"** untuk setiap webhook
- Pastikan status menunjukkan **"Success"**

## 🔍 Debugging Webhook

### 1. Check ngrok Status
```bash
# Buka http://localhost:4040
# Lihat semua requests yang masuk
```

### 2. Monitor Laravel Logs
```bash
# Real-time monitoring
tail -f storage/logs/laravel.log

# Filter webhook logs
tail -f storage/logs/laravel.log | grep -i webhook
```

### 3. Test Webhook Manually
```bash
# Test dengan curl
curl -X POST https://abc123.ngrok.io/xendit/webhook \
  -H "Content-Type: application/json" \
  -H "x-xendit-signature: your_webhook_token" \
  -d '{
    "event": "payment_link.paid",
    "data": {
      "id": "pl_test123",
      "external_id": "test_123",
      "status": "PAID",
      "amount": 100000
    }
  }'
```

## 🚨 Troubleshooting

### Webhook tidak diterima
1. **Check ngrok status** - Pastikan ngrok masih running
2. **Check URL** - Pastikan URL webhook benar
3. **Check firewall** - Pastikan port 8000 tidak diblokir
4. **Check logs** - Lihat error di Laravel logs

### Webhook diterima tapi error
1. **Check signature verification** - Pastikan webhook token benar
2. **Check database connection** - Pastikan database bisa diakses
3. **Check model relationships** - Pastikan relasi model benar

### ngrok connection failed
1. **Check authtoken** - Pastikan authtoken ngrok benar
2. **Check internet connection** - Pastikan koneksi internet stabil
3. **Try different port** - Coba port lain jika 8000 bermasalah

## 📝 Development Workflow

### 1. Start Development Server
```bash
# Terminal 1: Start Laravel
php artisan serve
# Server running on http://localhost:8000
```

### 2. Start ngrok
```bash
# Terminal 2: Start ngrok
ngrok http 8000
# Get public URL: https://abc123.ngrok.io
```

### 3. Update Webhook URLs
- Update di Xendit Dashboard
- Update di .env file
- Test webhook endpoints

### 4. Development
- Develop features
- Test webhook integration
- Monitor logs
- Debug issues

## 🔄 Production Setup

Ketika siap untuk production:

1. **Deploy aplikasi** ke server production
2. **Update webhook URLs** di Xendit Dashboard
3. **Update environment variables**
4. **Test webhook** di production
5. **Monitor logs** untuk memastikan webhook berfungsi

## 💡 Tips

1. **Gunakan ngrok Pro** untuk URL yang lebih stabil
2. **Simpan ngrok URL** untuk development yang konsisten
3. **Monitor ngrok dashboard** untuk melihat semua requests
4. **Gunakan ngrok config** untuk setup yang lebih mudah
5. **Backup webhook URLs** sebelum testing

---

**Next Steps:**
1. Install dan setup ngrok
2. Start Laravel server
3. Start ngrok tunnel
4. Update webhook URLs di Xendit Dashboard
5. Test webhook integration

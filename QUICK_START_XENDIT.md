# 🚀 Quick Start - Xendit Development

## 📋 Prerequisites
- ✅ Laravel application running
- ✅ Xendit account (https://dashboard.xendit.co)
- ✅ ngrok installed (https://ngrok.com/download)

## ⚡ Quick Setup (5 minutes)

### 1. Start Development Environment
```bash
# Windows
start-dev.bat

# Linux/Mac
./start-dev.sh
```

### 2. Get ngrok URL
- Buka http://localhost:4040
- Copy URL yang muncul (contoh: `https://abc123.ngrok.io`)

### 3. Update Environment Variables
```env
# .env
XENDIT_API_KEY=your_xendit_api_key_here
XENDIT_PUBLIC_KEY=your_xendit_public_key_here
XENDIT_WEBHOOK_TOKEN=your_xendit_webhook_token_here
XENDIT_CALLBACK_URL=https://abc123.ngrok.io/xendit/webhook
```

### 4. Set Webhook di Xendit Dashboard
1. Buka https://dashboard.xendit.co/settings/developers#webhooks
2. Set webhook URL: `https://abc123.ngrok.io/xendit/webhook`
3. Klik "Test and Save"

### 5. Test Integration
```bash
# Test webhook
php artisan webhook:test

# Test payment creation
php artisan xendit:test --type=payment_link
```

## 🔧 Manual Setup (Jika Quick Start tidak berfungsi)

### 1. Start Laravel Server
```bash
php artisan serve
# Server running on http://localhost:8000 
```

### 2. Start ngrok (Terminal baru)
```bash
ngrok http 8000
# Copy URL yang muncul
```

### 3. Update .env
```env
XENDIT_CALLBACK_URL=https://your-ngrok-url.ngrok.io/xendit/webhook
```

### 4. Set Webhook di Xendit
- Login ke Xendit Dashboard
- Go to Settings → Developers → Webhooks
- Set URL: `https://your-ngrok-url.ngrok.io/xendit/webhook`
- Test webhook

## 🧪 Testing Commands

### Test Webhook Endpoint
```bash
php artisan webhook:test
```

### Test Payment Creation
```bash
# Test Payment Link
php artisan xendit:test --type=payment_link

# Test XenPayment
php artisan xendit:test --type=xenpayment
```

### Monitor Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log

# Filter webhook logs
tail -f storage/logs/laravel.log | grep -i webhook
```

## 🔍 Troubleshooting

### Webhook tidak diterima
1. **Check ngrok status** - Pastikan ngrok masih running
2. **Check URL** - Pastikan URL webhook benar
3. **Check logs** - Lihat error di Laravel logs

### ngrok connection failed
1. **Check authtoken** - `ngrok config add-authtoken YOUR_TOKEN`
2. **Check port** - Pastikan port 8000 tidak digunakan
3. **Restart ngrok** - Stop dan start ulang ngrok

### Xendit API errors
1. **Check API keys** - Pastikan API keys benar
2. **Check permissions** - Pastikan akun Xendit sudah verified
3. **Check logs** - Lihat error details di logs

## 📱 Useful URLs

- **Laravel App**: http://localhost:8000
- **ngrok Dashboard**: http://localhost:4040
- **Xendit Dashboard**: https://dashboard.xendit.co/settings/developers#webhooks
- **Webhook Endpoint**: https://your-ngrok-url.ngrok.io/xendit/webhook

## 🎯 Next Steps

1. **Test Payment Flow**
   - Create payment link
   - Test payment process
   - Verify webhook processing

2. **Integration Testing**
   - Test dengan auction system
   - Test wallet integration
   - Test order creation

3. **Production Setup**
   - Deploy ke server production
   - Update webhook URLs
   - Test production webhooks

## 💡 Tips

- **Simpan ngrok URL** untuk development yang konsisten
- **Monitor ngrok dashboard** untuk melihat semua requests
- **Gunakan ngrok Pro** untuk URL yang lebih stabil
- **Backup webhook URLs** sebelum testing

---

**Status**: ✅ Ready untuk testing!

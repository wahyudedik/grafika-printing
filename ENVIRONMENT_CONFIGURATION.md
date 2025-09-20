# Environment Configuration Guide

## NGROK_URL Configuration

### Development (Lokal dengan ngrok)
```env
# Untuk development lokal dengan ngrok
NGROK_URL=https://332deb15a4e6.ngrok-free.app
APP_URL=https://332deb15a4e6.ngrok-free.app
XENDIT_CALLBACK_URL=https://332deb15a4e6.ngrok-free.app/xendit/webhook
WEBHOOK_URL=https://332deb15a4e6.ngrok-free.app/api/xendit/webhook
ASSET_URL=https://332deb15a4e6.ngrok-free.app
SANCTUM_STATEFUL_DOMAINS=https://332deb15a4e6.ngrok-free.app
```

### Production (VPS/Server)
```env
# Untuk production di VPS, kosongkan atau hapus NGROK_URL
NGROK_URL=
APP_URL=https://yourdomain.com
XENDIT_CALLBACK_URL=https://yourdomain.com/xendit/webhook
WEBHOOK_URL=https://yourdomain.com/api/xendit/webhook
ASSET_URL=https://yourdomain.com
SANCTUM_STATEFUL_DOMAINS=https://yourdomain.com
```

## Penjelasan Variabel Environment

### NGROK_URL
- **Development**: URL ngrok untuk akses lokal dari internet
- **Production**: Kosongkan atau hapus (tidak diperlukan)

### APP_URL
- **Development**: URL ngrok atau localhost
- **Production**: Domain VPS Anda (contoh: https://grafika.noteds.com)

### XENDIT_CALLBACK_URL
- **Development**: URL ngrok + /xendit/webhook
- **Production**: Domain VPS + /xendit/webhook

### WEBHOOK_URL
- **Development**: URL ngrok + /api/xendit/webhook
- **Production**: Domain VPS + /api/xendit/webhook

### ASSET_URL
- **Development**: URL ngrok untuk akses assets
- **Production**: Domain VPS untuk akses assets

### SANCTUM_STATEFUL_DOMAINS
- **Development**: URL ngrok untuk Sanctum authentication
- **Production**: Domain VPS untuk Sanctum authentication

## Konfigurasi untuk VPS

1. **Upload aplikasi ke VPS**
2. **Copy .env.example ke .env**
3. **Update konfigurasi database**
4. **Update domain dan URL**
5. **Jalankan migration dan seeder**

### Contoh .env untuk VPS:
```env
APP_NAME=Grafika-Printing
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database VPS
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=grafika_printing
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Xendit Production
XENDIT_API_KEY=your_production_xendit_key
XENDIT_PUBLIC_KEY=your_production_public_key
XENDIT_WEBHOOK_TOKEN=your_production_webhook_token
XENDIT_CALLBACK_URL=https://yourdomain.com/xendit/webhook

# Kosongkan NGROK_URL untuk production
NGROK_URL=
WEBHOOK_URL=https://yourdomain.com/api/xendit/webhook
ASSET_URL=https://yourdomain.com
SANCTUM_STATEFUL_DOMAINS=https://yourdomain.com
```

## Langkah-langkah Deployment VPS

1. **Setup Domain dan SSL**
2. **Install PHP, MySQL, Nginx/Apache**
3. **Clone repository**
4. **Install dependencies**: `composer install`
5. **Copy .env.example ke .env**
6. **Update .env dengan konfigurasi VPS**
7. **Generate APP_KEY**: `php artisan key:generate`
8. **Run migration**: `php artisan migrate:fresh --seed`
9. **Setup web server (Nginx/Apache)**
10. **Test aplikasi**

## Catatan Penting

- **NGROK_URL** hanya untuk development lokal
- **Production** tidak memerlukan ngrok
- **SSL Certificate** diperlukan untuk production
- **Domain** harus sudah terkonfigurasi dengan benar
- **Xendit** memerlukan URL yang dapat diakses dari internet

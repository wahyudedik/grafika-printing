# Production Environment Template - grafika.noteds.com

## 🎯 **Complete .env Configuration**

### **Production .env File**
```env
# Application Configuration
APP_NAME="Grafika Printing"
APP_ENV=production
APP_KEY=base64:your_generated_app_key_here
APP_DEBUG=false
APP_TIMEZONE=asia/jakarta
APP_URL=https://grafika.noteds.com

# Localization
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

# Maintenance
APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# PHP Configuration
PHP_CLI_SERVER_WORKERS=4

# Security
BCRYPT_ROUNDS=12

# Logging
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=grafikaprinting
DB_USERNAME=grafika_user
DB_PASSWORD=your_strong_database_password

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.noteds.com

# Broadcasting
BROADCAST_CONNECTION=log

# Filesystem
FILESYSTEM_DISK=local

# Queue Configuration
QUEUE_CONNECTION=redis

# Cache Configuration
CACHE_STORE=redis
CACHE_PREFIX=grafika

# Memory Configuration
MEMCACHED_HOST=127.0.0.1

# Redis Configuration
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS="noreply@grafika.noteds.com"
MAIL_FROM_NAME="${APP_NAME}"

# AWS Configuration (Optional)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Vite Configuration
VITE_APP_NAME="${APP_NAME}"

# Xendit Configuration (Production)
XENDIT_API_KEY=xnd_public_production_your_production_api_key
XENDIT_PUBLIC_KEY=xnd_public_production_your_public_key
XENDIT_WEBHOOK_TOKEN=your_production_webhook_token
XENDIT_BASE_URL=https://api.xendit.co
XENDIT_CALLBACK_URL=https://grafika.noteds.com/api/xendit/webhook

# RajaOngkir Configuration
RAJAONGKIR_API_KEY=your_rajaongkir_api_key
RAJAONGKIR_BASE_URL=https://api.rajaongkir.com/starter

# Security Headers
FORCE_HTTPS=true
ASSET_URL=https://grafika.noteds.com
```

## 🔧 **Configuration Files**

### 1. **config/app.php Updates**
```php
'url' => env('APP_URL', 'https://grafika.noteds.com'),
'asset_url' => env('ASSET_URL', 'https://grafika.noteds.com'),
'force_https' => env('FORCE_HTTPS', true),
```

### 2. **config/session.php Updates**
```php
'domain' => env('SESSION_DOMAIN', '.noteds.com'),
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => env('SESSION_HTTP_ONLY', true),
'same_site' => 'lax',
```

### 3. **config/services.php Updates**
```php
'xendit' => [
    'api_key' => env('XENDIT_API_KEY'),
    'public_key' => env('XENDIT_PUBLIC_KEY'),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    'base_url' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),
    'redirect_url' => env('APP_URL', 'https://grafika.noteds.com'),
    'webhook_url' => env('XENDIT_CALLBACK_URL', 'https://grafika.noteds.com/api/xendit/webhook'),
],
```

## 🔒 **Security Configuration**

### 1. **HTTPS Enforcement**
```php
// app/Http/Middleware/TrustProxies.php
protected $proxies = [
    '127.0.0.1',
    '::1',
    // Add your VPS IP if needed
];

protected $headers = Request::HEADER_X_FORWARDED_FOR |
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PORT |
    Request::HEADER_X_FORWARDED_PROTO |
    Request::HEADER_X_FORWARDED_AWS_ELB;
```

### 2. **CORS Configuration**
```php
// config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['https://grafika.noteds.com'],
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

## 📊 **Performance Configuration**

### 1. **Cache Configuration**
```php
// config/cache.php
'default' => env('CACHE_STORE', 'redis'),

'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
    ],
],
```

### 2. **Queue Configuration**
```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'redis'),

'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],
```

## 🧪 **Testing Configuration**

### 1. **Test Environment Variables**
```bash
# Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Test Xendit configuration
php artisan tinker --execute="echo config('services.xendit.api_key');"

# Test Redis connection
php artisan tinker --execute="Redis::ping();"
```

### 2. **Health Check Endpoints**
```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'redis' => Redis::ping() ? 'connected' : 'disconnected',
    ]);
});
```

## 📋 **Deployment Checklist**

### Pre-Deployment:
- [ ] Domain DNS pointing to VPS
- [ ] SSL certificate installed
- [ ] Database created and configured
- [ ] Xendit production API keys obtained
- [ ] Email configuration tested
- [ ] Redis server running

### Post-Deployment:
- [ ] Application accessible via HTTPS
- [ ] Database connection working
- [ ] Xendit webhook accessible
- [ ] Email sending working
- [ ] Cache working
- [ ] Logs being written
- [ ] File permissions correct

## 🆘 **Troubleshooting**

### Common Issues:
1. **Database connection failed**: Check credentials and permissions
2. **SSL not working**: Check certificate installation
3. **Xendit webhook failing**: Check URL and token
4. **Cache not working**: Check Redis configuration
5. **Email not sending**: Check SMTP configuration

### Debug Commands:
```bash
# Check application status
php artisan about

# Check configuration
php artisan config:show

# Check routes
php artisan route:list

# Check cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📞 **Support**

- **Application**: https://grafika.noteds.com
- **Admin Panel**: https://grafika.noteds.com/administrator
- **API Documentation**: https://grafika.noteds.com/api/documentation
- **Health Check**: https://grafika.noteds.com/health

---

**Production Environment Configuration selesai!** 🎉

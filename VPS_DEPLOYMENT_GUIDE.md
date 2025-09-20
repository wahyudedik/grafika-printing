# VPS Deployment Guide - grafika.noteds.com

## 🎯 **Overview**
Panduan lengkap untuk deployment aplikasi Grafika Printing ke VPS dengan domain `grafika.noteds.com`.

## 📋 **Prerequisites**

### Server Requirements
- **OS**: Ubuntu 20.04+ / CentOS 8+ / Debian 11+
- **PHP**: 8.1+ dengan extensions: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD, MySQL
- **Database**: MySQL 8.0+ / MariaDB 10.6+
- **Web Server**: Nginx / Apache
- **SSL**: Let's Encrypt / Cloudflare
- **Domain**: grafika.noteds.com

### Software yang Diperlukan
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1+ dengan extensions
sudo apt install php8.1-fpm php8.1-mysql php8.1-xml php8.1-gd php8.1-curl php8.1-mbstring php8.1-zip php8.1-bcmath php8.1-intl php8.1-redis php8.1-imagick -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js & NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y

# Install Nginx
sudo apt install nginx -y

# Install MySQL
sudo apt install mysql-server -y

# Install Redis (optional, untuk caching)
sudo apt install redis-server -y
```

## 🔧 **Server Configuration**

### 1. **Nginx Configuration**
```nginx
# /etc/nginx/sites-available/grafika.noteds.com
server {
    listen 80;
    server_name grafika.noteds.com www.grafika.noteds.com;
    root /var/www/grafika-printing/public;
    index index.php;

    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name grafika.noteds.com www.grafika.noteds.com;
    root /var/www/grafika-printing/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/grafika.noteds.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/grafika.noteds.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Laravel Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\.(ht|env) {
        deny all;
    }
}
```

### 2. **Enable Site**
```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/grafika.noteds.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🗄️ **Database Setup**

### 1. **Create Database**
```sql
-- Login to MySQL
sudo mysql -u root -p

-- Create database and user
CREATE DATABASE grafikaprinting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'grafika_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON grafikaprinting.* TO 'grafika_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 📁 **Application Deployment**

### 1. **Clone Repository**
```bash
# Create application directory
sudo mkdir -p /var/www/grafika-printing
sudo chown -R $USER:$USER /var/www/grafika-printing

# Clone repository
cd /var/www/grafika-printing
git clone https://github.com/your-username/grafika-printing.git .

# Set proper permissions
sudo chown -R www-data:www-data /var/www/grafika-printing
sudo chmod -R 755 /var/www/grafika-printing
sudo chmod -R 775 /var/www/grafika-printing/storage
sudo chmod -R 775 /var/www/grafika-printing/bootstrap/cache
```

### 2. **Install Dependencies**
```bash
cd /var/www/grafika-printing

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install
npm run build

# Set environment
cp .env.example .env
```

## ⚙️ **Environment Configuration**

### 1. **Production .env File**
```env
APP_NAME="Grafika Printing"
APP_ENV=production
APP_KEY=base64:your_app_key_here
APP_DEBUG=false
APP_TIMEZONE=asia/jakarta
APP_URL=https://grafika.noteds.com

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=grafikaprinting
DB_USERNAME=grafika_user
DB_PASSWORD=strong_password_here

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.noteds.com

# Cache Configuration
CACHE_STORE=redis
CACHE_PREFIX=grafika

# Queue Configuration
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@grafika.noteds.com"
MAIL_FROM_NAME="${APP_NAME}"

# Xendit Configuration (Production)
XENDIT_API_KEY=xnd_public_production_your_production_api_key
XENDIT_PUBLIC_KEY=xnd_public_production_your_public_key
XENDIT_WEBHOOK_TOKEN=your_production_webhook_token
XENDIT_BASE_URL=https://api.xendit.co
XENDIT_CALLBACK_URL=https://grafika.noteds.com/api/xendit/webhook

# RajaOngkir Configuration
RAJAONGKIR_API_KEY=your_rajaongkir_api_key
RAJAONGKIR_BASE_URL=https://api.rajaongkir.com/starter

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Security
BCRYPT_ROUNDS=12
LOG_LEVEL=error
```

### 2. **Generate Application Key**
```bash
cd /var/www/grafika-printing
php artisan key:generate
```

## 🚀 **Laravel Setup**

### 1. **Database Migration**
```bash
# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force
```

### 2. **Cache Optimization**
```bash
# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear application cache
php artisan cache:clear
```

### 3. **Storage Link**
```bash
# Create storage link
php artisan storage:link
```

## 🔒 **SSL Certificate**

### 1. **Let's Encrypt SSL**
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d grafika.noteds.com -d www.grafika.noteds.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## 🔄 **Xendit Production Configuration**

### 1. **Update Xendit Webhook URL**
Di Xendit Dashboard:
- **Webhook URL**: `https://grafika.noteds.com/api/xendit/webhook`
- **Environment**: Production
- **Events**: payment.paid, payment.expired, payment.failed

### 2. **Update Environment Variables**
```bash
# Update Xendit configuration
XENDIT_API_KEY=xnd_public_production_your_production_key
XENDIT_WEBHOOK_TOKEN=your_production_webhook_token
XENDIT_CALLBACK_URL=https://grafika.noteds.com/api/xendit/webhook
```

## 📊 **Monitoring & Logs**

### 1. **Log Rotation**
```bash
# Configure log rotation
sudo nano /etc/logrotate.d/grafika-printing

# Content:
/var/www/grafika-printing/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
```

### 2. **Systemd Service (Optional)**
```bash
# Create systemd service for queue workers
sudo nano /etc/systemd/system/grafika-queue.service

# Content:
[Unit]
Description=Grafika Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/grafika-printing/artisan queue:work --sleep=3 --tries=3 --max-time=3600
WorkingDirectory=/var/www/grafika-printing

[Install]
WantedBy=multi-user.target

# Enable service
sudo systemctl enable grafika-queue
sudo systemctl start grafika-queue
```

## 🔧 **Security Hardening**

### 1. **Firewall Configuration**
```bash
# Install UFW
sudo apt install ufw -y

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 2. **File Permissions**
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/grafika-printing
sudo find /var/www/grafika-printing -type f -exec chmod 644 {} \;
sudo find /var/www/grafika-printing -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/grafika-printing/storage
sudo chmod -R 775 /var/www/grafika-printing/bootstrap/cache
```

## 🚀 **Deployment Script**

### 1. **Create Deployment Script**
```bash
# Create deployment script
sudo nano /var/www/deploy.sh

# Content:
#!/bin/bash
cd /var/www/grafika-printing

# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

# Set permissions
sudo chown -R www-data:www-data /var/www/grafika-printing
sudo chmod -R 775 /var/www/grafika-printing/storage
sudo chmod -R 775 /var/www/grafika-printing/bootstrap/cache

# Restart services
sudo systemctl reload nginx
sudo systemctl restart php8.1-fpm

echo "Deployment completed successfully!"

# Make executable
sudo chmod +x /var/www/deploy.sh
```

## ✅ **Verification**

### 1. **Test Application**
```bash
# Test application
curl -I https://grafika.noteds.com

# Test Xendit webhook
curl -X POST https://grafika.noteds.com/api/xendit/webhook \
  -H "Content-Type: application/json" \
  -d '{"test": "webhook"}'
```

### 2. **Check Services**
```bash
# Check Nginx
sudo systemctl status nginx

# Check PHP-FPM
sudo systemctl status php8.1-fpm

# Check MySQL
sudo systemctl status mysql

# Check Redis
sudo systemctl status redis
```

## 📝 **Post-Deployment Checklist**

- [ ] Domain DNS pointing to VPS IP
- [ ] SSL certificate installed and working
- [ ] Database connection working
- [ ] Xendit webhook URL updated
- [ ] Email configuration working
- [ ] File permissions set correctly
- [ ] Logs are being written
- [ ] Cache is working
- [ ] Queue workers running (if using)
- [ ] Backup strategy in place

## 🆘 **Troubleshooting**

### Common Issues:
1. **Permission denied**: Check file ownership and permissions
2. **Database connection failed**: Verify database credentials
3. **SSL not working**: Check certificate installation
4. **Xendit webhook failing**: Verify webhook URL and token
5. **Cache not working**: Check Redis configuration

### Log Locations:
- Application logs: `/var/www/grafika-printing/storage/logs/`
- Nginx logs: `/var/log/nginx/`
- PHP logs: `/var/log/php8.1-fpm.log`
- System logs: `/var/log/syslog`

## 📞 **Support**

Untuk bantuan deployment, hubungi:
- Email: support@grafika.noteds.com
- Documentation: [Link to docs]
- GitHub Issues: [Link to repository]

---

**Deployment selesai! Aplikasi Grafika Printing sudah live di https://grafika.noteds.com** 🎉

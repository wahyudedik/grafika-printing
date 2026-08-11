#!/bin/bash
#
# ╔══════════════════════════════════════════════════════════════╗
# ║  Grafika Printing - First-Time Deployment Script            ║
# ║  Domain: grafika.noteds.com                                 ║
# ║  Server: Ubuntu 20.04+ / Debian 11+                        ║
# ╚══════════════════════════════════════════════════════════════╝
#
# Script ini digunakan untuk deployment pertama kali ke VPS.
# Untuk update/upgrade, gunakan update.sh
#
# Usage: sudo bash deploy.sh
#

set -e

# ============================================
# KONFIGURASI
# ============================================
APP_DIR="/var/www/grafika-printing"
APP_NAME="grafika-printing"
APP_USER="www-data"
APP_GROUP="www-data"
DOMAIN="grafika.noteds.com"
DB_NAME="grafika_printing"
DB_USER="grafika_user"
DB_PASSWORD=""  # Akan diminta saat runtime
PHP_VERSION="8.2"
NODE_VERSION="20"
REPO_URL="https://github.com/wahyuedv/grafika-printing.git"
BRANCH="main"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ============================================
# HELPER FUNCTIONS
# ============================================
print_header() {
    echo ""
    echo -e "${BLUE}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║${NC}  $1"
    echo -e "${BLUE}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# ============================================
# PRE-FLIGHT CHECKS
# ============================================
preflight_checks() {
    print_header "PRE-FLIGHT CHECKS"

    # Check if running as root
    if [[ $EUID -ne 0 ]]; then
        print_error "Script ini harus dijalankan sebagai root (sudo)"
        exit 1
    fi
    print_success "Running as root"

    # Check OS
    if ! grep -q "Ubuntu\|Debian" /etc/os-release 2>/dev/null; then
        print_warning "OS mungkin tidak didukung. Script ini diuji di Ubuntu/Debian."
    fi
    print_success "OS check passed"

    # Check if app directory already exists
    if [ -d "$APP_DIR" ]; then
        print_warning "Directory $APP_DIR sudah ada."
        echo "  Jika ini fresh install, hapus directory tersebut terlebih dahulu."
        echo "  Jika ingin UPDATE, jalankan: sudo bash update.sh"
        read -p "  Lanjutkan deployment? (y/N): " confirm
        if [[ $confirm != [yY] ]]; then
            exit 0
        fi
    fi
}

# ============================================
# INSTALL SYSTEM DEPENDENCIES
# ============================================
install_system_deps() {
    print_header "INSTALLING SYSTEM DEPENDENCIES"

    # Update system
    print_info "Updating system packages..."
    apt update -y && apt upgrade -y

    # Install essential packages
    print_info "Installing essential packages..."
    apt install -y software-properties-common curl wget git unzip

    # Add PHP repository
    print_info "Adding PHP repository..."
    add-apt-repository -y ppa:ondrej/php

    # Install PHP
    print_info "Installing PHP ${PHP_VERSION}..."
    apt update -y
    apt install -y \
        php${PHP_VERSION}-fpm \
        php${PHP_VERSION}-mysql \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-zip \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-intl \
        php${PHP_VERSION}-redis \
        php${PHP_VERSION}-imagick \
        php${PHP_VERSION}-opcache

    print_success "PHP ${PHP_VERSION} installed"

    # Install Composer
    print_info "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    print_success "Composer installed"

    # Install Node.js 20.x (required for Vite 6)
    print_info "Installing Node.js 20.x..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt install -y nodejs
    print_success "Node.js $(node -v) installed"

    # Install Nginx
    print_info "Installing Nginx..."
    apt install -y nginx
    print_success "Nginx installed"

    # Install MySQL
    print_info "Installing MySQL..."
    apt install -y mysql-server
    print_success "MySQL installed"

    # Install Redis (optional but recommended)
    print_info "Installing Redis..."
    apt install -y redis-server
    systemctl enable redis-server
    systemctl start redis-server
    print_success "Redis installed"

    # Install Certbot for SSL
    print_info "Installing Certbot..."
    apt install -y certbot python3-certbot-nginx
    print_success "Certbot installed"
}

# ============================================
# SETUP DATABASE
# ============================================
setup_database() {
    print_header "SETTING UP DATABASE"

    # Prompt for database password
    if [ -z "$DB_PASSWORD" ]; then
        read -sp "Masukkan password untuk database user '$DB_USER': " DB_PASSWORD
        echo ""
        if [ -z "$DB_PASSWORD" ]; then
            print_error "Database password tidak boleh kosong"
            exit 1
        fi
    fi

    # Create database and user
    print_info "Creating database and user..."
    mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
EOF

    print_success "Database '$DB_NAME' and user '$DB_USER' created"
}

# ============================================
# SETUP APPLICATION
# ============================================
setup_application() {
    print_header "SETTING UP APPLICATION"

    # Clone repository
    print_info "Cloning repository..."
    if [ -d "$APP_DIR" ]; then
        rm -rf "$APP_DIR"
    fi
    git clone -b $BRANCH $REPO_URL $APP_DIR
    cd $APP_DIR

    print_success "Repository cloned to $APP_DIR"

    # Install PHP dependencies
    print_info "Installing PHP dependencies..."
    composer install --optimize-autoloader --no-dev
    print_success "Composer dependencies installed"

    # Install Node.js dependencies and build
    print_info "Installing Node.js dependencies and building assets..."
    npm ci --no-audit --no-fund
    npm run build
    print_success "NPM dependencies installed and assets built"

    # Setup .env file
    print_info "Setting up .env file..."
    if [ ! -f ".env" ]; then
        cp .env.example .env
    fi

    # Generate APP_KEY if not set
    if ! grep -q "APP_KEY=base64:[a-zA-Z0-9]" .env 2>/dev/null; then
        php artisan key:generate
        print_success "APP_KEY generated"
    fi

    # Update .env with production settings
    sed -i 's/APP_ENV=local/APP_ENV=production/' .env
    sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env
    sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN}|" .env
    sed -i "s|FORCE_HTTPS=.*|FORCE_HTTPS=true|" .env
    sed -i "s|ASSET_URL=.*|ASSET_URL=https://${DOMAIN}|" .env
    sed -i 's/SESSION_DOMAIN=null/SESSION_DOMAIN=.noteds.com/' .env

    print_success ".env configured for production"

    # Run landlord migrations (multi-tenant)
    print_info "Running landlord migrations..."
    php artisan tenants:migrate --force 2>/dev/null || php artisan migrate --force
    print_success "Landlord migrations completed"

    # Run tenant migrations
    print_info "Running tenant migrations..."
    php artisan tenants:migrate --tenant=* --force 2>/dev/null || true
    print_success "Tenant migrations completed"

    # Seed database (optional)
    read -p "  Jalankan database seeder? (y/N): " seed_confirm
    if [[ $seed_confirm == [yY] ]]; then
        php artisan db:seed --force
        php artisan tenants:seed --force 2>/dev/null || true
        print_success "Database seeded"
    fi

    # Create storage link
    print_info "Creating storage link..."
    php artisan storage:link --force
    print_success "Storage link created"

    # Cache optimization
    print_info "Optimizing caches..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    php artisan icons:cache 2>/dev/null || true
    print_success "Caches optimized"
}

# ============================================
# SETUP NGINX
# ============================================
setup_nginx() {
    print_header "SETTING UP NGINX"

    NGINX_CONF="/etc/nginx/sites-available/${DOMAIN}"

    cat > "$NGINX_CONF" <<'NGINX_EOF'
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

    # SSL Configuration (will be updated by Certbot)
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'" always;

    # Laravel Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\.(ht|env) {
        deny all;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
NGINX_EOF

    # Enable site
    ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/

    # Remove default site if exists
    rm -f /etc/nginx/sites-enabled/default

    # Test Nginx config
    nginx -t
    systemctl reload nginx

    print_success "Nginx configured for $DOMAIN"
}

# ============================================
# SETUP PERMISSIONS
# ============================================
setup_permissions() {
    print_header "SETTING UP PERMISSIONS"

    # Set ownership
    chown -R ${APP_USER}:${APP_GROUP} "$APP_DIR"

    # Set file permissions
    find "$APP_DIR" -type f -exec chmod 644 {} \;
    find "$APP_DIR" -type d -exec chmod 755 {} \;

    # Laravel-specific permissions
    chmod -R 775 "$APP_DIR/storage"
    chmod -R 775 "$APP_DIR/bootstrap/cache"

    print_success "Permissions set correctly"
}

# ============================================
# SETUP SSL
# ============================================
setup_ssl() {
    print_header "SETTING UP SSL CERTIFICATE"

    read -p "  Install SSL certificate via Let's Encrypt? (y/N): " ssl_confirm
    if [[ $ssl_confirm == [yY] ]]; then
        certbot --nginx -d "$DOMAIN" -d "www.$DOMAIN" --non-interactive --agree-tos --email "admin@$DOMAIN"
        print_success "SSL certificate installed"

        # Setup auto-renewal
        echo "0 12 * * * /usr/bin/certbot renew --quiet" | crontab -
        print_success "Auto-renewal configured"
    else
        print_warning "SSL belum dikonfigurasi. Jalankan nanti: certbot --nginx -d $DOMAIN"
    fi
}

# ============================================
# SETUP CRON & QUEUE
# ============================================
setup_cron_queue() {
    print_header "SETTING UP CRON & QUEUE WORKER"

    # Setup Laravel scheduler cron
    print_info "Setting up Laravel scheduler..."
    CRON_LINE="* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"
    (crontab -l 2>/dev/null | grep -v "artisan schedule:run"; echo "$CRON_LINE") | crontab -
    print_success "Laravel scheduler cron added"

    # Setup queue worker service
    print_info "Setting up queue worker service..."
    cat > /etc/systemd/system/grafika-queue.service <<'SERVICE_EOF'
[Unit]
Description=Grafika Printing Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/grafika-printing/artisan queue:work --sleep=3 --tries=3 --max-time=3600
WorkingDirectory=/var/www/grafika-printing

[Install]
WantedBy=multi-user.target
SERVICE_EOF

    systemctl daemon-reload
    systemctl enable grafika-queue
    systemctl start grafika-queue
    print_success "Queue worker service created and started"

    # Setup log rotation
    print_info "Setting up log rotation..."
    cat > /etc/logrotate.d/grafika-printing <<'LOGROTATE_EOF'
/var/www/grafika-printing/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
LOGROTATE_EOF
    print_success "Log rotation configured"
}

# ============================================
# SETUP FIREWALL
# ============================================
setup_firewall() {
    print_header "SETTING UP FIREWALL"

    read -p "  Aktifkan UFW firewall? (y/N): " fw_confirm
    if [[ $fw_confirm == [yY] ]]; then
        apt install -y ufw
        ufw default deny incoming
        ufw default allow outgoing
        ufw allow ssh
        ufw allow 'Nginx Full'
        ufw --force enable
        print_success "Firewall configured"
    else
        print_warning "Firewall belum diaktifkan. Aktifkan manual untuk keamanan."
    fi
}

# ============================================
# VERIFICATION
# ============================================
verify_deployment() {
    print_header "VERIFICATION"

    # Check services
    print_info "Checking services..."

    if systemctl is-active --quiet nginx; then
        print_success "Nginx is running"
    else
        print_error "Nginx is NOT running"
    fi

    if systemctl is-active --quiet php${PHP_VERSION}-fpm; then
        print_success "PHP-FPM is running"
    else
        print_error "PHP-FPM is NOT running"
    fi

    if systemctl is-active --quiet mysql; then
        print_success "MySQL is running"
    else
        print_error "MySQL is NOT running"
    fi

    if systemctl is-active --quiet redis-server; then
        print_success "Redis is running"
    else
        print_warning "Redis is not running"
    fi

    if systemctl is-active --quiet grafika-queue; then
        print_success "Queue worker is running"
    else
        print_warning "Queue worker is not running"
    fi

    # Test application
    print_info "Testing application..."
    cd "$APP_DIR"
    php artisan --version > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        print_success "Laravel is working"
    else
        print_error "Laravel has issues"
    fi
}

# ============================================
# MAIN EXECUTION
# ============================================
main() {
    echo ""
    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║     GRAFIKA PRINTING - FIRST-TIME DEPLOYMENT               ║${NC}"
    echo -e "${GREEN}║     Domain: grafika.noteds.com                             ║${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo "Script ini akan:"
    echo "  1. Install system dependencies (PHP, Nginx, MySQL, Redis, etc.)"
    echo "  2. Setup database"
    echo "  3. Clone & configure application"
    echo "  4. Setup Nginx virtual host"
    echo "  5. Setup SSL certificate"
    echo "  6. Setup cron, queue worker, & firewall"
    echo ""
    read -p "Lanjutkan deployment? (y/N): " start_confirm
    if [[ $start_confirm != [yY] ]]; then
        echo "Deployment dibatalkan."
        exit 0
    fi

    preflight_checks
    install_system_deps
    setup_database
    setup_application
    setup_nginx
    setup_permissions
    setup_ssl
    setup_cron_queue
    setup_firewall
    verify_deployment

    echo ""
    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║     ✅ DEPLOYMENT SELESAI!                                  ║${NC}"
    echo -e "${GREEN}║                                                              ║${NC}"
    echo -e "${GREEN}║     Aplikasi Grafika Printing sudah live di:                 ║${NC}"
    echo -e "${GREEN}║     https://${DOMAIN}                                    ║${NC}"
    echo -e "${GREEN}║                                                              ║${NC}"
    echo -e "${GREEN}║     Langkah selanjutnya:                                     ║${NC}"
    echo -e "${GREEN}║     1. Pastikan DNS domain sudah pointing ke VPS IP          ║${NC}"
    echo -e "${GREEN}║     2. Update Xendit webhook URL di dashboard Xendit         ║${NC}"
    echo -e "${GREEN}║     3. Test pembayaran dengan Xendit sandbox                 ║${NC}"
    echo -e "${GREEN}║     4. Update .env dengan API keys production                 ║${NC}"
    echo -e "${GREEN}║     5. Setup vendor tenants via admin panel                   ║${NC}"
    echo -e "${GREEN}║     6. Jalankan: php artisan tenants:migrate --tenant=*       ║${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════════════════════════╝${NC}"
}

# Run main function
main "$@"

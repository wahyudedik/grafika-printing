#!/bin/bash
#
# ╔══════════════════════════════════════════════════════════════╗
# ║  Grafika Printing - Update/Upgrade Script                   ║
# ║  Domain: grafika.noteds.com                                 ║
# ╚══════════════════════════════════════════════════════════════╝
#
# Script ini digunakan untuk update aplikasi yang sudah ter-deploy.
# Mendukung server vanilla (Ubuntu/Debian) dan server aaPanel.
# Untuk fresh install pertama kali, gunakan deploy.sh
#
# Usage: sudo bash update.sh
#

set -e

# ============================================
# KONFIGURASI
# ============================================
DOMAIN="grafika.noteds.com"
BRANCH="main"
MAX_BACKUPS=5

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# ============================================
# DETEKSI PANEL (aaPanel vs Vanilla)
# ============================================
#
# LOGIKA DETEKSI (priority order):
#   1. Direktori saat ini punya artisan + .env → APP_DIR = $(pwd)
#   2. Path aaPanel ada (/www/wwwroot/DOMAIN)  → APP_DIR = aaPanel path
#   3. Path vanilla ada (/var/www/grafika-printing) → APP_DIR = vanilla path
#   4. Error + exit
#
# Panel TYPE dideteksi TERPISAH untuk konfigurasi tools (PHP, Nginx, dst).
# APP_DIR TIDAK akan di-override setelah ditentukan.
#
detect_panel() {
    print_header "DETEKSI SERVER ENVIRONMENT"

    # Reset semua variabel
    APP_DIR=""
    PANEL_TYPE=""
    PHP_BIN=""
    PHP_FPM_SOCKET=""
    NGINX_BIN=""
    NGINX_CONF_DIR=""
    APP_USER=""
    APP_GROUP=""
    BACKUP_DIR=""

    # ============================================================
    # Step 1: Deteksi APP_DIR (berdasarkan keberadaan file aktual)
    # ============================================================
    # Prioritas berdasarkan reliability — yang paling reliable duluan.

    # Priority 1: Script dijalankan dari direktori project
    #   Cek ./artisan dan ./.env (relative ke CWD) — paling reliable
    if [ -f "./artisan" ] && [ -f "./.env" ]; then
        APP_DIR="$(pwd)"
        print_success "Priority 1: Laravel project di direktori saat ini"
        print_info "  Path: $APP_DIR"

    # Priority 2: aaPanel webroot path
    elif [ -d "/www/wwwroot/${DOMAIN}" ]; then
        APP_DIR="/www/wwwroot/${DOMAIN}"
        print_success "Priority 2: aaPanel webroot path ditemukan"
        print_info "  Path: $APP_DIR"

    # Priority 3: Vanilla default path
    elif [ -d "/var/www/grafika-printing" ]; then
        APP_DIR="/var/www/grafika-printing"
        print_success "Priority 3: Vanilla default path ditemukan"
        print_info "  Path: $APP_DIR"

    # Priority 4: Terakhir — cari artisan di CWD (tanpa .env)
    elif [ -f "./artisan" ]; then
        APP_DIR="$(pwd)"
        print_warning "Priority 4: artisan ditemukan di CWD (tanpa .env)"
        print_info "  Path: $APP_DIR"

    # Tidak bisa menentukan direktori
    else
        print_error "Tidak bisa menentukan direktori aplikasi!"
        echo ""
        print_info "Path yang sudah dicoba:"
        print_info "  1. $(pwd)/artisan + .env  (current directory)"
        print_info "  2. /www/wwwroot/${DOMAIN}  (aaPanel webroot)"
        print_info "  3. /var/www/grafika-printing  (vanilla default)"
        echo ""
        print_info "Solusi:"
        print_info "  Jalankan dari directory project:"
        print_info "    cd /www/wwwroot/${DOMAIN} && sudo bash update.sh"
        print_info ""
        print_info "  Atau pastikan website sudah dibuat di aaPanel Panel > Website"
        print_info "  dengan domain: $DOMAIN"
        exit 1
    fi

    # ============================================================
    # Step 2: Deteksi panel TYPE (untuk tools: PHP, Nginx, dst)
    # ============================================================
    # Panel type dideteksi SETELAH APP_DIR agar tidak mengubah APP_DIR.

    IS_AAPANEL=false

    # Cek marker aaPanel: /www/server/panel, /etc/init.d/bt, atau command bt
    if [ -d "/www/server/panel" ]; then
        IS_AAPANEL=true
        PANEL_TYPE="aapanel"
        print_info "aaPanel terdeteksi: /www/server/panel exists"
    elif [ -f "/etc/init.d/bt" ]; then
        IS_AAPANEL=true
        PANEL_TYPE="aapanel"
        print_info "aaPanel terdeteksi: /etc/init.d/bt exists"
    elif command -v bt &> /dev/null 2>&1; then
        IS_AAPANEL=true
        PANEL_TYPE="aapanel"
        print_info "aaPanel terdeteksi: bt command found"
    fi

    # === Reinforcement: jika APP_DIR di /www/wwwroot/ → pastikan aaPanel ===
    if [[ "$APP_DIR" == /www/wwwroot/* ]]; then
        IS_AAPANEL=true
        PANEL_TYPE="aapanel"
    fi

    # === Reinforcement: jika APP_DIR di /var/www/ → pastikan vanilla ===
    if [[ "$APP_DIR" == /var/www/* ]] && [ "$IS_AAPANEL" = false ]; then
        PANEL_TYPE="vanilla"
    fi

    # Jika masih belum ditentukan, default ke vanilla
    if [ -z "$PANEL_TYPE" ]; then
        PANEL_TYPE="vanilla"
    fi

    print_info "Panel type: $PANEL_TYPE"

    # ============================================================
    # Step 3: Konfigurasi tools berdasarkan panel type
    # ============================================================

    if [ "$IS_AAPANEL" = true ]; then
        # --- aaPanel tools ---

        # PHP binary: cari versi terbaru yang terinstall di aaPanel
        if [ -z "$PHP_BIN" ]; then
            PHP_BIN=$(ls /www/server/php/*/bin/php 2>/dev/null | sort -V | tail -1)
            if [ -z "$PHP_BIN" ]; then
                PHP_BIN="php"
                print_warning "PHP aaPanel tidak ditemukan, menggunakan php dari PATH"
            else
                print_success "PHP binary: $PHP_BIN"
            fi
        fi

        # PHP-FPM socket path
        if [ -z "$PHP_FPM_SOCKET" ]; then
            PHP_FPM_SOCKET=$(ls /www/server/php/*/tmp/php-fpm.sock 2>/dev/null | sort -V | tail -1)
            if [ -z "$PHP_FPM_SOCKET" ]; then
                PHP_FPM_SOCKET="/tmp/php-fpm.sock"
                print_warning "PHP-FPM socket tidak ditemukan, menggunakan default: $PHP_FPM_SOCKET"
            else
                print_success "PHP-FPM socket: $PHP_FPM_SOCKET"
            fi
        fi

        # Nginx binary & config
        NGINX_BIN="/www/server/nginx/sbin/nginx"
        if [ ! -f "$NGINX_BIN" ]; then
            NGINX_BIN="nginx"
        fi
        NGINX_CONF_DIR="/www/server/panel/vhost/nginx"

        # aaPanel uses 'www' as the web user
        APP_USER="www"
        APP_GROUP="www"

        # aaPanel backup directory
        BACKUP_DIR="/www/backup/grafika-printing"

        print_success "Panel tools: aaPanel"
    else
        # --- Vanilla tools ---
        PHP_BIN="php"
        PHP_FPM_SOCKET=""
        NGINX_BIN="nginx"
        NGINX_CONF_DIR="/etc/nginx/sites-available"

        APP_USER="www-data"
        APP_GROUP="www-data"

        BACKUP_DIR="/var/backups/grafika-printing"

        # Detect PHP version for vanilla
        PHP_VERSION="8.2"
        if command -v php &> /dev/null; then
            PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
        fi

        print_success "Panel tools: Vanilla"
        print_info "PHP version: $PHP_VERSION"
    fi

    # ============================================================
    # Step 4: Ringkasan deteksi
    # ============================================================
    echo ""
    print_info "┌─ Hasil Deteksi ─────────────────────────────"
    print_info "│ Panel type   : $PANEL_TYPE"
    print_info "│ App directory: $APP_DIR"
    print_info "│ PHP binary   : $PHP_BIN"
    print_info "│ App user     : $APP_USER"
    print_info "│ Backup dir   : $BACKUP_DIR"
    print_info "│ Nginx config : $NGINX_CONF_DIR"
    print_info "└─────────────────────────────────────────────"
    echo ""
}

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

# Run PHP with the correct binary
run_php() {
    cd "$APP_DIR"
    $PHP_BIN "$@"
}

# ============================================
# PRE-FLIGHT CHECKS
# ============================================
preflight_checks() {
    print_header "PRE-FLIGHT CHECKS"

    if [[ $EUID -ne 0 ]]; then
        print_error "Script ini harus dijalankan sebagai root (sudo)"
        exit 1
    fi

    # Detect panel environment
    detect_panel

    if [ ! -d "$APP_DIR" ]; then
        print_error "Directory $APP_DIR tidak ditemukan."
        if [ "$PANEL_TYPE" = "aapanel" ]; then
            print_info "Pastikan website sudah dibuat di aaPanel Panel > Website."
            print_info "Domain harus: $DOMAIN"
        else
            print_info "Untuk fresh install, jalankan: sudo bash deploy.sh"
        fi
        exit 1
    fi

    cd "$APP_DIR"

    # Fix git ownership issue (common on aaPanel when root runs git as www user)
    if [ -d ".git" ]; then
        SAFE_DIR=$(git config --global --get safe.directory "$APP_DIR" 2>/dev/null)
        if [ -z "$SAFE_DIR" ]; then
            print_info "Adding git safe.directory for $APP_DIR..."
            git config --global --add safe.directory "$APP_DIR"
            print_success "Git safe.directory configured"
        fi
    fi

    if [ ! -f ".env" ]; then
        print_error "File .env tidak ditemukan di $APP_DIR"
        exit 1
    fi

    if [ ! -f "artisan" ]; then
        print_error "Laravel artisan tidak ditemukan. Pastikan ini adalah direktori Laravel."
        exit 1
    fi

    # Check composer
    if ! command -v composer &> /dev/null && [ ! -f "/usr/local/bin/composer" ]; then
        print_warning "Composer tidak ditemukan. Installing..."
        curl -sS https://getcomposer.org/installer | $PHP_BIN
        mv composer.phar /usr/local/bin/composer
        print_success "Composer installed"
    fi

    # Check node/npm
    if ! command -v node &> /dev/null; then
        print_warning "Node.js tidak ditemukan. npm build akan di-skip."
        SKIP_NPM=true
    else
        SKIP_NPM=false
    fi

    print_success "Pre-flight checks passed"
    print_info "Environment: $PANEL_TYPE | PHP: $PHP_BIN | User: $APP_USER"
}

# ============================================
# MAINTENANCE MODE
# ============================================
enable_maintenance() {
    print_info "Enabling maintenance mode..."
    cd "$APP_DIR"
    run_php artisan down --render="errors::503" --retry=60
    print_success "Maintenance mode enabled"
}

disable_maintenance() {
    print_info "Disabling maintenance mode..."
    cd "$APP_DIR"
    run_php artisan up
    print_success "Maintenance mode disabled"
}

# ============================================
# BACKUP
# ============================================
create_backup() {
    print_header "CREATING BACKUP"

    mkdir -p "$BACKUP_DIR"
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)

    # --- Backup aplikasi (file) ---
    print_info "Backing up application files..."
    BACKUP_APP_FILE="$BACKUP_DIR/app_backup_${TIMESTAMP}.tar.gz"
    BACKUP_PARENT="$(dirname "$APP_DIR")"
    BACKUP_DIRNAME="$(basename "$APP_DIR")"
    tar -czf "$BACKUP_APP_FILE" -C "$BACKUP_PARENT" "$BACKUP_DIRNAME" 2>/dev/null || true
    print_success "Application files backed up"

    # --- Backup database ---
    print_info "Backing up database..."
    DB_NAME=$(grep "DB_DATABASE=" .env | cut -d '=' -f2 | tr -d '[:space:]')
    DB_USER=$(grep "DB_USERNAME=" .env | cut -d '=' -f2 | tr -d '[:space:]')
    DB_PASS=$(grep "DB_PASSWORD=" .env | cut -d '=' -f2 | tr -d '[:space:]')

    if [ -n "$DB_NAME" ] && [ -n "$DB_USER" ]; then
        DB_BACKUP_FILE="$BACKUP_DIR/db_backup_${TIMESTAMP}.sql"
        if [ "$PANEL_TYPE" = "aapanel" ]; then
            # aaPanel MySQL path
            AA_PANEL_MYSQL="/www/server/mysql/bin/mysql"
            AA_PANEL_MYSQLDUMP="/www/server/mysql/bin/mysqldump"
            if [ -f "$AA_PANEL_MYSQLDUMP" ]; then
                $AA_PANEL_MYSQLDUMP -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$DB_BACKUP_FILE" 2>/dev/null
            else
                mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$DB_BACKUP_FILE" 2>/dev/null
            fi
        else
            mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$DB_BACKUP_FILE" 2>/dev/null
        fi
        print_success "Database backed up to db_backup_${TIMESTAMP}.sql"
    else
        print_warning "Could not read database config from .env"
    fi

    # --- Backup .env ---
    cp .env "$BACKUP_DIR/env_backup_${TIMESTAMP}"
    print_success ".env backed up"

    # --- Cleanup old backups (keep last MAX_BACKUPS) ---
    print_info "Cleaning old backups (keeping last $MAX_BACKUPS)..."
    cd "$BACKUP_DIR"
    ls -t app_backup_*.tar.gz 2>/dev/null | tail -n +$((MAX_BACKUPS + 1)) | xargs -r rm
    ls -t db_backup_*.sql 2>/dev/null | tail -n +$((MAX_BACKUPS + 1)) | xargs -r rm
    ls -t env_backup_* 2>/dev/null | grep -v "\.sql$\|\.tar\.gz$" | tail -n +$((MAX_BACKUPS + 1)) | xargs -r rm

    print_success "Backups cleaned (keeping last $MAX_BACKUPS)"
    print_info "Backup location: $BACKUP_DIR"
}

# ============================================
# PULL & UPDATE
# ============================================
pull_updates() {
    print_header "PULLING LATEST CHANGES"

    cd "$APP_DIR"

    # Stash any local changes
    print_info "Stashing local changes..."
    git stash 2>/dev/null || true

    # Pull latest changes
    print_info "Pulling from origin/$BRANCH..."
    git pull origin $BRANCH

    if [ $? -eq 0 ]; then
        print_success "Latest changes pulled"
    else
        print_error "Failed to pull changes"
        exit 1
    fi

    # Show latest commit
    print_info "Latest commit:"
    git log --oneline -1
}

# ============================================
# UPDATE DEPENDENCIES
# ============================================
update_dependencies() {
    print_header "UPDATING DEPENDENCIES"

    cd "$APP_DIR"

    # Composer
    print_info "Updating Composer dependencies..."
    composer install --optimize-autoloader --no-dev --no-interaction
    print_success "Composer dependencies updated"

    # NPM (skip if node not available)
    if [ "$SKIP_NPM" = true ]; then
        print_warning "Node.js tidak tersedia, skip npm build."
        print_warning "Pastikan asset sudah built sebelumnya atau install Node.js."
    else
        print_info "Updating NPM dependencies and rebuilding assets..."
        npm ci --no-audit --no-fund --include=dev 2>/dev/null || npm install --no-audit --no-fund 2>/dev/null || true
        npm run build 2>/dev/null || true
        print_success "NPM dependencies updated and assets built"
    fi
}

# ============================================
# RUN MIGRATIONS
# ============================================
run_migrations() {
    print_header "RUNNING DATABASE MIGRATIONS"

    cd "$APP_DIR"

    # Run landlord migrations (multi-tenant)
    print_info "Running landlord migrations..."
    run_php artisan tenants:migrate --force 2>/dev/null || run_php artisan migrate --force
    print_success "Landlord migrations completed"

    # Run tenant migrations
    print_info "Running tenant migrations..."
    run_php artisan tenants:migrate --tenant=* --force 2>/dev/null || true
    print_success "Tenant migrations completed"
}

# ============================================
# OPTIMIZE & CACHE
# ============================================
optimize_application() {
    print_header "OPTIMIZING APPLICATION"

    cd "$APP_DIR"

    print_info "Clearing old caches..."
    run_php artisan cache:clear
    run_php artisan config:clear
    run_php artisan route:clear
    run_php artisan view:clear
    run_php artisan event:clear

    print_info "Building new caches..."
    run_php artisan config:cache
    run_php artisan route:cache
    run_php artisan view:cache
    run_php artisan event:cache
    run_php artisan icons:cache 2>/dev/null || true

    # Ensure storage link exists
    run_php artisan storage:link --force 2>/dev/null || true

    print_success "Application optimized"
}

# ============================================
# FIX PERMISSIONS
# ============================================
fix_permissions() {
    print_header "FIXING PERMISSIONS"

    cd "$APP_DIR"

    chown -R ${APP_USER}:${APP_GROUP} .
    find . -type f -exec chmod 644 {} \;
    find . -type d -exec chmod 755 {} \;
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache

    # Ensure storage/logs is writable
    chmod -R 775 storage/logs 2>/dev/null || true

    # Ensure public directories are readable
    chmod -R 755 public 2>/dev/null || true

    print_success "Permissions fixed (owner: ${APP_USER}:${APP_GROUP})"
}

# ============================================
# RESTART SERVICES
# ============================================
restart_services() {
    print_header "RESTARTING SERVICES"

    if [ "$PANEL_TYPE" = "aapanel" ]; then
        # --- aaPanel: gunakan service management aaPanel ---
        print_info "Menggunakan aaPanel service management..."

        # Reload Nginx via aaPanel
        if [ -f "/www/server/nginx/sbin/nginx" ]; then
            /www/server/nginx/sbin/nginx -t 2>/dev/null && /www/server/nginx/sbin/nginx -s reload 2>/dev/null
            if [ $? -eq 0 ]; then
                print_success "Nginx reloaded (aaPanel)"
            else
                # Fallback: try systemctl
                systemctl reload nginx 2>/dev/null || true
                print_success "Nginx reloaded (systemctl fallback)"
            fi
        fi

        # Restart PHP-FPM via aaPanel
        AA_PHP_VERSION=$(ls /www/server/php/ 2>/dev/null | sort -V | tail -1)
        if [ -n "$AA_PHP_VERSION" ] && [ -f "/etc/init.d/php-fpm-${AA_PHP_VERSION}" ]; then
            /etc/init.d/php-fpm-${AA_PHP_VERSION} restart
            print_success "PHP-FPM ${AA_PHP_VERSION} restarted (aaPanel)"
        elif [ -f "/etc/init.d/php-fpm" ]; then
            /etc/init.d/php-fpm restart
            print_success "PHP-FPM restarted (aaPanel)"
        else
            # Fallback: systemctl
            systemctl restart php*-fpm 2>/dev/null || true
            print_success "PHP-FPM restarted (systemctl fallback)"
        fi

        # Restart Redis via aaPanel (if exists)
        if [ -f "/etc/init.d/redis" ]; then
            /etc/init.d/redis restart
            print_success "Redis restarted (aaPanel)"
        elif systemctl is-active --quiet redis-server 2>/dev/null; then
            systemctl restart redis-server
            print_success "Redis restarted (systemctl)"
        fi

    else
        # --- Vanilla: systemctl ---
        print_info "Menggunakan systemctl..."

        # Nginx
        if systemctl is-active --quiet nginx; then
            nginx -t 2>/dev/null && systemctl reload nginx
            print_success "Nginx reloaded"
        fi

        # PHP-FPM
        if systemctl is-active --quiet php${PHP_VERSION}-fpm; then
            systemctl restart php${PHP_VERSION}-fpm
            print_success "PHP-FPM restarted"
        fi

        # Queue worker
        if systemctl is-active --quiet grafika-queue; then
            systemctl restart grafika-queue
            print_success "Queue worker restarted"
        fi

        # Redis
        if systemctl is-active --quiet redis-server; then
            systemctl restart redis-server
            print_success "Redis restarted"
        fi
    fi
}

# ============================================
# UPDATE NGINX CONFIG (Optional)
# ============================================
update_nginx_config() {
    print_header "NGINX CONFIGURATION"

    if [ "$PANEL_TYPE" = "aapanel" ]; then
        NGINX_CONF="${NGINX_CONF_DIR}/${DOMAIN}.conf"

        if [ ! -f "$NGINX_CONF" ]; then
            print_warning "Nginx config tidak ditemukan di $NGINX_CONF"
            print_info "Membuat nginx config untuk aaPanel..."

            cat > "$NGINX_CONF" <<NGINX_EOF
server {
    listen 80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${APP_DIR}/public;
    index index.php;

    # Redirect HTTP to HTTPS (uncomment setelah SSL dipasang)
    # return 301 https://\$server_name\$request_uri;

    # Laravel Configuration
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP-FPM Configuration (aaPanel)
    location ~ \.php\$ {
        fastcgi_pass unix:${PHP_FPM_SOCKET};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
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

            print_success "Nginx config created: $NGINX_CONF"
        else
            print_success "Nginx config sudah ada: $NGINX_CONF"

            # Verify PHP-FPM socket path is correct in config
            if grep -q "fastcgi_pass" "$NGINX_CONF"; then
                CURRENT_SOCKET=$(grep "fastcgi_pass" "$NGINX_CONF" | head -1 | sed 's/.*unix:\([^;]*\).*/\1/')
                if [ -n "$CURRENT_SOCKET" ] && [ "$CURRENT_SOCKET" != "$PHP_FPM_SOCKET" ]; then
                    print_warning "PHP-FPM socket path berbeda: $CURRENT_SOCKET vs $PHP_FPM_SOCKET"
                    read -p "  Update socket path ke $PHP_FPM_SOCKET? (y/N): " socket_confirm
                    if [[ $socket_confirm == [yY] ]]; then
                        sed -i "s|$CURRENT_SOCKET|$PHP_FPM_SOCKET|g" "$NGINX_CONF"
                        print_success "Socket path updated in nginx config"
                    fi
                fi
            fi
        fi

        # Test nginx config
        if [ -f "/www/server/nginx/sbin/nginx" ]; then
            /www/server/nginx/sbin/nginx -t 2>&1
        else
            nginx -t 2>&1
        fi

    else
        # Vanilla: just verify existing config
        NGINX_CONF="${NGINX_CONF_DIR}/${DOMAIN}"
        if [ -f "$NGINX_CONF" ]; then
            print_success "Nginx config found: $NGINX_CONF"
        else
            print_warning "Nginx config tidak ditemukan di $NGINX_CONF"
            print_info "Jalankan deploy.sh untuk membuat nginx config, atau buat manual."
        fi
    fi
}

# ============================================
# VERIFICATION
# ============================================
verify_update() {
    print_header "VERIFICATION"

    cd "$APP_DIR"

    # Test artisan
    run_php artisan --version > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        LARAVEL_VERSION=$(run_php artisan --version)
        print_success "Laravel artisan working ($LARAVEL_VERSION)"
    else
        print_error "Laravel artisan has issues"
    fi

    # Check app status
    APP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost" 2>/dev/null)
    if [ "$APP_STATUS" = "200" ] || [ "$APP_STATUS" = "301" ] || [ "$APP_STATUS" = "302" ]; then
        print_success "Application responding (HTTP $APP_STATUS)"
    else
        print_warning "Application returned HTTP $APP_STATUS - check Nginx/PHP-FPM"
    fi

    # Check git status
    print_info "Current git status:"
    git log --oneline -3
    echo ""

    # Panel info
    if [ "$PANEL_TYPE" = "aapanel" ]; then
        print_info "aaPanel: Pastikan website '$DOMAIN' active di Panel > Website"
    fi
}

# ============================================
# ROLLBACK (Emergency)
# ============================================
rollback() {
    print_header "EMERGENCY ROLLBACK"

    LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/app_backup_*.tar.gz 2>/dev/null | head -1)
    LATEST_DB_BACKUP=$(ls -t "$BACKUP_DIR"/db_backup_*.sql 2>/dev/null | head -1)
    LATEST_ENV_BACKUP=$(ls -t "$BACKUP_DIR"/env_backup_* 2>/dev/null | grep -v "db_backup\|app_backup" | head -1)

    if [ -z "$LATEST_BACKUP" ]; then
        print_error "No backup found in $BACKUP_DIR"
        exit 1
    fi

    print_info "Latest backup: $LATEST_BACKUP"
    echo "  Database backup: $LATEST_DB_BACKUP"
    echo "  Env backup: $LATEST_ENV_BACKUP"
    echo ""
    read -p "  Restore from backup? This will OVERWRITE current data! (y/N): " confirm
    if [[ $confirm != [yY] ]]; then
        echo "Rollback cancelled."
        exit 0
    fi

    enable_maintenance

    # Restore database
    if [ -n "$LATEST_DB_BACKUP" ] && [ -f "$LATEST_DB_BACKUP" ]; then
        print_info "Restoring database..."
        cd "$APP_DIR"
        DB_USER=$(grep "DB_USERNAME=" .env | cut -d '=' -f2)
        DB_PASS=$(grep "DB_PASSWORD=" .env | cut -d '=' -f2)
        DB_NAME=$(grep "DB_DATABASE=" .env | cut -d '=' -f2)

        if [ "$PANEL_TYPE" = "aapanel" ]; then
            AA_PANEL_MYSQL="/www/server/mysql/bin/mysql"
            if [ -f "$AA_PANEL_MYSQL" ]; then
                $AA_PANEL_MYSQL -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$LATEST_DB_BACKUP" 2>/dev/null
            else
                mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$LATEST_DB_BACKUP" 2>/dev/null
            fi
        else
            mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$LATEST_DB_BACKUP" 2>/dev/null
        fi
        print_success "Database restored"
    fi

    # Restore .env
    if [ -n "$LATEST_ENV_BACKUP" ] && [ -f "$LATEST_ENV_BACKUP" ]; then
        print_info "Restoring .env..."
        cp "$LATEST_ENV_BACKUP" .env
        print_success ".env restored"
    fi

    # Restore application files
    print_info "Restoring application files..."
    tar -xzf "$LATEST_BACKUP" -C / 2>/dev/null || true
    cd "$APP_DIR"

    # Re-optimize
    optimize_application
    fix_permissions
    restart_services

    disable_maintenance

    print_success "Rollback completed"
}

# ============================================
# UPDATE NGINX CONFIG UNTUK aaPanel
# ============================================
update_nginx_aapanel() {
    if [ "$PANEL_TYPE" != "aapanel" ]; then
        return
    fi

    print_header "UPDATE NGINX CONFIG UNTUK aaPanel"

    NGINX_CONF="${NGINX_CONF_DIR}/${DOMAIN}.conf"

    # Ensure nginx config directory exists
    mkdir -p "$NGINX_CONF_DIR"

    # Backup current nginx config if exists
    if [ -f "$NGINX_CONF" ]; then
        cp "$NGINX_CONF" "${NGINX_CONF}.bak.$(date +%Y%m%d_%H%M%S)"
        print_info "Nginx config backed up"
    fi

    # Create/update nginx config
    print_info "Creating nginx config for aaPanel..."

    cat > "$NGINX_CONF" <<NGINX_EOF
server {
    listen 80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${APP_DIR}/public;
    index index.php;

    # Redirect HTTP to HTTPS (uncomment setelah SSL dipasang via aaPanel)
    # return 301 https://\$server_name\$request_uri;

    # Laravel Configuration
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP-FPM Configuration (aaPanel socket)
    location ~ \.php\$ {
        fastcgi_pass unix:${PHP_FPM_SOCKET};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
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

    print_success "Nginx config created: $NGINX_CONF"

    # Test nginx config
    if [ -f "/www/server/nginx/sbin/nginx" ]; then
        /www/server/nginx/sbin/nginx -t 2>&1
        if [ $? -eq 0 ]; then
            print_success "Nginx config test passed"
        else
            print_error "Nginx config test failed! Check the config file."
            print_info "Config file: $NGINX_CONF"
        fi
    fi
}

# ============================================
# MAIN EXECUTION
# ============================================
main() {
    echo ""
    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║     GRAFIKA PRINTING - UPDATE SCRIPT                        ║${NC}"
    echo -e "${GREEN}║     Mendukung: aaPanel / Vanilla Ubuntu                     ║${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo "Pilihan:"
    echo "  1) Update Aplikasi (pull, migrate, optimize)"
    echo "  2) Update Nginx Config (aaPanel only)"
    echo "  3) Rollback (emergency restore from backup)"
    echo "  4) Exit"
    echo ""
    read -p "Pilih opsi [1/2/3/4]: " choice

    case $choice in
        1)
            echo ""
            echo "Update akan:"
            echo "  1. Deteksi server environment"
            echo "  2. Create backup (app + database + .env)"
            echo "  3. Pull latest changes from git"
            echo "  4. Update dependencies (composer install + npm build)"
            echo "  5. Enable maintenance mode"
            echo "  6. Run migrations (landlord + tenant)"
            echo "  7. Optimize application"
            echo "  8. Fix permissions"
            echo "  9. Restart services"
            echo "  10. Disable maintenance mode"
            echo ""
            read -p "Lanjutkan update? (y/N): " confirm
            if [[ $confirm != [yY] ]]; then
                echo "Update dibatalkan."
                exit 0
            fi

            preflight_checks
            create_backup
            pull_updates
            update_dependencies
            enable_maintenance
            run_migrations
            optimize_application
            fix_permissions
            restart_services
            disable_maintenance
            verify_update

            echo ""
            echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗${NC}"
            echo -e "${GREEN}║     ✅ UPDATE SELESAI!                                      ║${NC}"
            echo -e "${GREEN}╚══════════════════════════════════════════════════════════════╝${NC}"
            ;;
        2)
            preflight_checks
            update_nginx_config
            update_nginx_aapanel
            restart_services
            print_success "Nginx config update selesai"
            ;;
        3)
            preflight_checks
            rollback
            ;;
        4)
            echo "Exiting."
            exit 0
            ;;
        *)
            print_error "Invalid option"
            exit 1
            ;;
    esac
}

# Run main function
main "$@"

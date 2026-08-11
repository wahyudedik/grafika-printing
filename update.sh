#!/bin/bash
#
# ╔══════════════════════════════════════════════════════════════╗
# ║  Grafika Printing - Update/Upgrade Script                   ║
# ║  Domain: grafika.noteds.com                                 ║
# ╚══════════════════════════════════════════════════════════════╝
#
# Script ini digunakan untuk update aplikasi yang sudah ter-deploy.
# Untuk fresh install pertama kali, gunakan deploy.sh
#
# Usage: sudo bash update.sh
#

set -e

# ============================================
# KONFIGURASI
# ============================================
APP_DIR="/var/www/grafika-printing"
APP_USER="www-data"
APP_GROUP="www-data"
PHP_VERSION="8.2"
BRANCH="main"
BACKUP_DIR="/var/backups/grafika-printing"
MAX_BACKUPS=5

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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

    if [[ $EUID -ne 0 ]]; then
        print_error "Script ini harus dijalankan sebagai root (sudo)"
        exit 1
    fi

    if [ ! -d "$APP_DIR" ]; then
        print_error "Directory $APP_DIR tidak ditemukan."
        print_info "Untuk fresh install, jalankan: sudo bash deploy.sh"
        exit 1
    fi

    cd "$APP_DIR"

    if [ ! -f ".env" ]; then
        print_error "File .env tidak ditemukan di $APP_DIR"
        exit 1
    fi

    if [ ! -f "artisan" ]; then
        print_error "Laravel artisan tidak ditemukan. Pastikan ini adalah direktori Laravel."
        exit 1
    fi

    print_success "Pre-flight checks passed"
}

# ============================================
# MAINTENANCE MODE
# ============================================
enable_maintenance() {
    print_info "Enabling maintenance mode..."
    cd "$APP_DIR"
    php artisan down --render="errors::503" --retry=60
    print_success "Maintenance mode enabled"
}

disable_maintenance() {
    print_info "Disabling maintenance mode..."
    cd "$APP_DIR"
    php artisan up
    print_success "Maintenance mode disabled"
}

# ============================================
# BACKUP
# ============================================
create_backup() {
    print_header "CREATING BACKUP"

    mkdir -p "$BACKUP_DIR"
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_FILE="$BACKUP_DIR/backup_${TIMESTAMP}.tar.gz"

    # Backup database
    print_info "Backing up database..."
    DB_NAME=$(grep "DB_DATABASE=" .env | cut -d '=' -f2)
    DB_USER=$(grep "DB_USERNAME=" .env | cut -d '=' -f2)
    DB_PASS=$(grep "DB_PASSWORD=" .env | cut -d '=' -f2)

    if [ -n "$DB_NAME" ] && [ -n "$DB_USER" ]; then
        mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_DIR/db_backup_${TIMESTAMP}.sql" 2>/dev/null
        print_success "Database backed up to db_backup_${TIMESTAMP}.sql"
    else
        print_warning "Could not read database config from .env"
    fi

    # Backup .env
    cp .env "$BACKUP_DIR/env_backup_${TIMESTAMP}"
    print_success ".env backed up"

    # Cleanup old backups (keep last MAX_BACKUPS)
    print_info "Cleaning old backups (keeping last $MAX_BACKUPS)..."
    cd "$BACKUP_DIR"
    ls -t backup_*.tar.gz 2>/dev/null | tail -n +$((MAX_BACKUPS + 1)) | xargs -r rm
    ls -t db_backup_*.sql 2>/dev/null | tail -n +$((MAX_BACKUPS + 1)) | xargs -r rm
    ls -t env_backup_* 2>/dev/null | grep -v "\.sql$\|\.tar\.gz$" | tail -n +$((MAX_BACKUPS + 1)) | xargs -r rm

    print_success "Backups cleaned (keeping last $MAX_BACKUPS)"
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
        disable_maintenance
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

    # NPM
    print_info "Updating NPM dependencies and rebuilding assets..."
    npm ci --no-audit --no-fund --include=dev
    npm run build
    print_success "NPM dependencies updated and assets built"
}

# ============================================
# RUN MIGRATIONS
# ============================================
run_migrations() {
    print_header "RUNNING DATABASE MIGRATIONS"

    cd "$APP_DIR"

    # Run landlord migrations (multi-tenant)
    print_info "Running landlord migrations..."
    php artisan tenants:migrate --force 2>/dev/null || php artisan migrate --force
    print_success "Landlord migrations completed"

    # Run tenant migrations
    print_info "Running tenant migrations..."
    php artisan tenants:migrate --tenant=* --force 2>/dev/null || true
    print_success "Tenant migrations completed"
}

# ============================================
# OPTIMIZE & CACHE
# ============================================
optimize_application() {
    print_header "OPTIMIZING APPLICATION"

    cd "$APP_DIR"

    print_info "Clearing old caches..."
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan event:clear

    print_info "Building new caches..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    php artisan icons:cache 2>/dev/null || true

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

    print_success "Permissions fixed"
}

# ============================================
# RESTART SERVICES
# ============================================
restart_services() {
    print_header "RESTARTING SERVICES"

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
}

# ============================================
# VERIFICATION
# ============================================
verify_update() {
    print_header "VERIFICATION"

    cd "$APP_DIR"

    # Test artisan
    php artisan --version > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        print_success "Laravel artisan working"
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
}

# ============================================
# ROLLBACK (Emergency)
# ============================================
rollback() {
    print_header "EMERGENCY ROLLBACK"

    LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/backup_*.tar.gz 2>/dev/null | head -1)
    LATEST_DB_BACKUP=$(ls -t "$BACKUP_DIR"/db_backup_*.sql 2>/dev/null | head -1)
    LATEST_ENV_BACKUP=$(ls -t "$BACKUP_DIR"/env_backup_* 2>/dev/null | grep -v "db_backup\|backup_" | head -1)

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
        DB_USER=$(grep "DB_USERNAME=" .env | cut -d '=' -f2)
        DB_PASS=$(grep "DB_PASSWORD=" .env | cut -d '=' -f2)
        DB_NAME=$(grep "DB_DATABASE=" .env | cut -d '=' -f2)
        mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$LATEST_DB_BACKUP" 2>/dev/null
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
    tar -xzf "$LATEST_BACKUP" -C "$APP_DIR" 2>/dev/null || true
    cd "$APP_DIR"

    # Re-optimize
    optimize_application
    fix_permissions
    restart_services

    disable_maintenance

    print_success "Rollback completed"
}

# ============================================
# MAIN EXECUTION
# ============================================
main() {
    echo ""
    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║     GRAFIKA PRINTING - UPDATE SCRIPT                        ║${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo "Pilihan:"
    echo "  1) Update Aplikasi (pull, migrate, optimize)"
    echo "  2) Rollback (emergency restore from backup)"
    echo "  3) Exit"
    echo ""
    read -p "Pilih opsi [1/2/3]: " choice

    case $choice in
        1)
            echo ""
            echo "Update akan:"
            echo "  1. Enable maintenance mode"
            echo "  2. Create backup (database + .env)"
            echo "  3. Pull latest changes from git"
            echo "  4. Update dependencies"
            echo "  5. Run migrations (landlord + tenant)"
            echo "  6. Optimize application"
            echo "  7. Fix permissions"
            echo "  8. Restart services"
            echo "  9. Disable maintenance mode"
            echo ""
            read -p "Lanjutkan update? (y/N): " confirm
            if [[ $confirm != [yY] ]]; then
                echo "Update dibatalkan."
                exit 0
            fi

            preflight_checks
            enable_maintenance
            create_backup
            pull_updates
            update_dependencies
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
            rollback
            ;;
        3)
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

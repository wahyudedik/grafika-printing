# Recovery Commands untuk Server aaPanel

> Dokumen ini berisi command untuk memperbaiki error dpkg, git ownership, dan
> masalah umum lainnya di server VPS aaPanel (grafika.noteds.com).

---

## Fix dpkg mariadb errors

Command yang perlu dijalankan sebagai **root** di VPS:

```bash
# Force remove broken mariadb packages
dpkg --remove --force-remove-reinstreq --force-depends mariadb-client mariadb-client-core libmariadb3 mariadb-common 2>/dev/null

# Buat file yang hilang agar dpkg bisa configure
mkdir -p /etc/mysql
cat > /etc/mysql/mariadb.cnf << 'EOF'
#
# The MariaDB configuration file.
# One can use all long options that the program supports.
# Run program with --help to get a list of available options.
#

[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

[client]
default-character-set = utf8mb4

!includedir /etc/mysql/mariadb.conf.d/
EOF

# Sekarang configure
dpkg --configure -a
apt --fix-broken install -y

# Hapus PPA ondrej/php
add-apt-repository --remove -y ppa:ondrej/php 2>/dev/null
rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-*

# Hapus PHP 8.2 dari apt
apt remove --purge -y 'php8.2-*' 2>/dev/null
rm -rf /etc/php/8.2/

# Bersihkan
apt autoremove --purge -y
apt update -y
apt clean

# Fix git ownership
git config --global --add safe.directory /www/wwwroot/grafika.noteds.com

# Verifikasi
dpkg --audit 2>&1
echo "---"
systemctl status nginx | head -3
echo "---"
ls /www/wwwroot/grafika.noteds.com/artisan && echo "✅ App found"
```

---

## Fix git "dubious ownership" error

Jika menjalankan `git` sebagai root di direktori yang dimiliki oleh user lain:

```bash
# Tambahkan safe directory
git config --global --add safe.directory /www/wwwroot/grafika.noteds.com

# Verifikasi
git config --global --get safe.directory /www/wwwroot/grafika.noteds.com
# Harus output: /www/wwwroot/grafika.noteds.com
```

---

## Fix permission setelah update

```bash
cd /www/wwwroot/grafika.noteds.com

# Fix ownership
chown -R www:www .

# Fix permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 storage/logs

echo "✅ Permissions fixed"
```

---

## Fix Nginx 502 Bad Gateway

```bash
# Cek PHP-FPM status
/etc/init.d/php-fpm-8.1 status

# Restart PHP-FPM
/etc/init.d/php-fpm-8.1 restart

# Atau cari versi PHP yang terinstall
ls /www/server/php/
# Contoh output: 74  80  81  82

# Restart PHP-FPM versi yang sesuai
/etc/init.d/php-fpm-81 restart

# Test nginx config
/www/server/nginx/sbin/nginx -t

# Reload nginx
/www/server/nginx/sbin/nginx -s reload
```

---

## Fix "No such file or directory" saat jalankan update.sh

```bash
# Pastikan ada di direktori yang benar
cd /www/wwwroot/grafika.noteds.com

# Cek apakah artisan dan .env ada
ls -la artisan .env

# Jalankan update
sudo bash update.sh
```

---

## Quick Update Command

```bash
cd /www/wwwroot/grafika.noteds.com && sudo bash update.sh
```

---

## Verifikasi Setelah Recovery

```bash
# Cek aplikasi
curl -s -o /dev/null -w "%{http_code}" http://localhost
# Expected: 200

# Cek artisan
cd /www/wwwroot/grafika.noteds.com
/www/server/php/*/bin/php artisan --version

# Cek nginx
/www/server/nginx/sbin/nginx -t

# Cek dpkg
dpkg --audit 2>&1

# Cek git status
cd /www/wwwroot/grafika.noteds.com
git status
git log --oneline -3
```

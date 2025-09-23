# 🔧 Troubleshooting Guide - Grafika Printing

## 🚨 **Masalah Umum & Solusi Cepat**

### **1. Fitur "Kadang Bisa Kadang Tidak"**

#### **Penyebab:**
- Cache tidak ter-update
- Relasi database tidak konsisten
- Tenant context tidak aktif
- Route cache bermasalah

#### **Solusi Cepat:**
```bash
# Clear semua cache
php artisan quick:fix --issue=cache

# Fix relasi vendor-user
php artisan quick:fix --issue=vendor-relationships

# Fix route issues
php artisan quick:fix --issue=routes

# Fix semua masalah sekaligus
php artisan quick:fix --all
```

### **2. Vendor Dashboard Redirect Loop**

#### **Penyebab:**
- Middleware `tenants` tidak aktif
- Relasi vendor-user tidak ada
- Tenant context tidak ter-set

#### **Solusi:**
```bash
# Fix relasi vendor-user
php artisan fix:vendor-user-relationships --force

# Clear cache
php artisan cache:clear
php artisan route:clear
```

### **3. API Error "Method Not Found"**

#### **Penyebab:**
- Method tidak ada di controller
- Route tidak terdaftar
- Cache route bermasalah

#### **Solusi:**
```bash
# Clear route cache
php artisan route:clear
php artisan route:cache

# Check route yang tersedia
php artisan route:list --name=vendor
```

### **4. Database Relationship Errors**

#### **Penyebab:**
- Relasi tidak terdefinisi dengan benar
- Data orphaned
- Foreign key constraints

#### **Solusi:**
```bash
# Clean up orphaned records
php artisan quick:fix --issue=orphaned

# Run migrations
php artisan quick:fix --issue=migrations
```

## 🛠️ **Command Troubleshooting**

### **Diagnosis Komprehensif**
```bash
# Jalankan diagnosis lengkap
php artisan diagnose:application --detailed

# Fix masalah otomatis
php artisan diagnose:application --fix
```

### **Test Fitur**
```bash
# Test semua fitur
php artisan test:features --detailed

# Test fitur spesifik
php artisan test:features --feature=vendor_dashboard --detailed
php artisan test:features --feature=authentication --detailed
```

### **Monitoring Real-time**
```bash
# Monitor aplikasi selama 5 menit
php artisan monitor:application --interval=30 --duration=300
```

### **Quick Fixes**
```bash
# Lihat semua quick fixes
php artisan quick:fix

# Fix masalah spesifik
php artisan quick:fix --issue=cache
php artisan quick:fix --issue=routes
php artisan quick:fix --issue=vendor-relationships
php artisan quick:fix --issue=wallets
php artisan quick:fix --issue=orphaned
php artisan quick:fix --issue=permissions
php artisan quick:fix --issue=storage
php artisan quick:fix --issue=config
php artisan quick:fix --issue=migrations
php artisan quick:fix --issue=seeds

# Fix semua masalah
php artisan quick:fix --all
```

## 🔍 **Debugging Steps**

### **1. Check Application Health**
```bash
php artisan diagnose:application --detailed
```

### **2. Test Specific Features**
```bash
php artisan test:features --feature=authentication --detailed
php artisan test:features --feature=vendor_dashboard --detailed
php artisan test:features --feature=auction_system --detailed
php artisan test:features --feature=payment_system --detailed
```

### **3. Monitor in Real-time**
```bash
php artisan monitor:application --interval=10 --duration=60
```

### **4. Apply Quick Fixes**
```bash
php artisan quick:fix --all
```

## 🚀 **Prevention Tips**

### **1. Regular Maintenance**
```bash
# Jalankan setiap hari
php artisan quick:fix --issue=cache
php artisan quick:fix --issue=orphaned

# Jalankan setiap minggu
php artisan diagnose:application --detailed
php artisan test:features --detailed
```

### **2. Monitor Performance**
```bash
# Monitor aplikasi secara berkala
php artisan monitor:application --interval=60 --duration=600
```

### **3. Keep Dependencies Updated**
```bash
composer update
npm update
```

## 📊 **Common Issues & Solutions**

| Issue | Symptoms | Quick Fix |
|-------|----------|-----------|
| Redirect Loop | Vendor dashboard tidak bisa diakses | `php artisan quick:fix --issue=vendor-relationships` |
| API Error | Method not found | `php artisan quick:fix --issue=routes` |
| Cache Issues | Data tidak ter-update | `php artisan quick:fix --issue=cache` |
| Database Errors | Relationship errors | `php artisan quick:fix --issue=orphaned` |
| File Permissions | Storage errors | `php artisan quick:fix --issue=permissions` |
| Configuration | Config not loaded | `php artisan quick:fix --issue=config` |

## 🆘 **Emergency Recovery**

### **Jika Aplikasi Tidak Bisa Diakses:**
```bash
# 1. Clear semua cache
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear

# 2. Fix relasi database
php artisan fix:vendor-user-relationships --force

# 3. Run migrations
php artisan migrate --force

# 4. Recreate storage symlink
php artisan storage:link

# 5. Fix permissions
php artisan quick:fix --issue=permissions
```

### **Jika Database Corrupt:**
```bash
# 1. Backup database
mysqldump -u username -p database_name > backup.sql

# 2. Reset database
php artisan migrate:fresh --force

# 3. Run seeders
php artisan db:seed --force

# 4. Fix relationships
php artisan fix:vendor-user-relationships --force
```

## 📞 **Support Commands**

### **Get Help:**
```bash
# Lihat semua command yang tersedia
php artisan list

# Lihat help untuk command spesifik
php artisan help diagnose:application
php artisan help test:features
php artisan help quick:fix
php artisan help monitor:application
```

### **Check System Status:**
```bash
# Check Laravel version
php artisan --version

# Check PHP version
php --version

# Check database connection
php artisan tinker --execute="DB::connection()->getPdo();"
```

## 🎯 **Best Practices**

### **1. Regular Health Checks**
- Jalankan `php artisan diagnose:application` setiap hari
- Monitor aplikasi dengan `php artisan monitor:application`
- Test fitur dengan `php artisan test:features`

### **2. Proactive Maintenance**
- Clear cache secara berkala
- Monitor orphaned records
- Check file permissions
- Update dependencies

### **3. Documentation**
- Catat semua perubahan
- Backup database secara berkala
- Test fitur setelah perubahan

## 🔧 **Advanced Troubleshooting**

### **Debug Mode:**
```bash
# Enable debug mode
php artisan config:clear
# Set APP_DEBUG=true in .env
```

### **Log Analysis:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check specific errors
grep "ERROR" storage/logs/laravel.log
```

### **Database Analysis:**
```bash
# Check database connections
php artisan tinker --execute="DB::connection()->getPdo();"

# Check table structure
php artisan tinker --execute="DB::select('DESCRIBE users');"
```

---

## 📝 **Notes**

- Selalu backup database sebelum melakukan perubahan besar
- Test fitur setelah setiap perubahan
- Monitor aplikasi secara berkala
- Dokumentasikan semua perubahan

**Happy Coding! 🚀**

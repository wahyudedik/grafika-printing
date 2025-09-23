# Arsitektur Multi-Tenant Grafika Printing

## 🏗️ **OVERVIEW**

Aplikasi Grafika Printing menggunakan **Shared Database dengan Tenant Context** untuk mengisolasi data antar pengguna. Setiap user dan vendor memiliki data yang terisolasi sepenuhnya, sementara admin/dev memiliki akses global.

## 🎯 **TENANT TYPES**

### 1. **User (Pembeli)** - ✅ TENANT
- **Data yang diisolasi**: Auctions, Payments, Ratings, Delivery Confirmations
- **Scope**: Hanya dapat melihat data milik sendiri
- **Model**: Menggunakan `UserTenantModel`

### 2. **Vendor (Penjual)** - ✅ TENANT  
- **Data yang diisolasi**: Products, Transactions, Orders, Customers
- **Scope**: Hanya dapat melihat data vendor mereka
- **Model**: Menggunakan `TenantModel`

### 3. **Dev/Admin/Superadmin** - ❌ BUKAN TENANT
- **Data yang diakses**: Semua data (global access)
- **Scope**: Dapat melihat semua data dari semua tenant
- **Model**: Menggunakan `Model` biasa

## 🔧 **IMPLEMENTASI TEKNIS**

### **1. Middleware SetTenantContext**

```php
// app/Http/Middleware/SetTenantContext.php
public function handle(Request $request, Closure $next)
{
    if (Auth::check()) {
        $user = Auth::user();

        switch ($user->usertype) {
            case 'vendor':
                // Set vendor tenant context
                $vendorUser = $user->vendorUser->first();
                if ($vendorUser) {
                    Tenant::setVendor($vendorUser);
                }
                break;

            case 'user':
                // Set user tenant context
                Tenant::setUser($user);
                break;

            case 'dev':
            case 'admin':
            case 'superadmin':
                // No tenant context (global access)
                break;
        }
    }

    return $next($request);
}
```

### **2. TenantManager Service**

```php
// app/Services/TenantManager.php
class TenantManager
{
    // Vendor Context
    public function setVendor(Vendor $vendor)
    public function getVendor()
    public function hasVendorContext()
    public function clearVendorContext()

    // User Context  
    public function setUser(User $user)
    public function getUser()
    public function hasUserContext()
    public function clearUserContext()

    // Global Context
    public function clearAllContexts()
}
```

### **3. Model Inheritance**

#### **UserTenantModel** (untuk User data)
```php
// app/Models/User/UserTenantModel.php
abstract class UserTenantModel extends Model
{
    protected static function booted()
    {
        // Auto-apply user_id filter
        static::addGlobalScope('user_tenant', function (Builder $builder) {
            $tenantManager = app(TenantManager::class);
            if ($tenantManager->hasUserContext()) {
                $builder->where('user_id', $tenantManager->getUserId());
            }
        });

        // Auto-set user_id on create
        static::creating(function ($model) {
            $tenantManager = app(TenantManager::class);
            if ($tenantManager->hasUserContext() && !$model->user_id) {
                $model->user_id = $tenantManager->getUserId();
            }
        });
    }
}
```

#### **TenantModel** (untuk Vendor data)
```php
// app/Models/Vendor/TenantModel.php
abstract class TenantModel extends Model
{
    protected static function booted()
    {
        // Auto-apply vendor_id filter
        static::addGlobalScope('vendor_tenant', function (Builder $builder) {
            $tenantManager = app(TenantManager::class);
            if ($tenantManager->hasVendorContext()) {
                $builder->where('vendor_id', $tenantManager->getVendorId());
            }
        });

        // Auto-set vendor_id on create
        static::creating(function ($model) {
            $tenantManager = app(TenantManager::class);
            if ($tenantManager->hasVendorContext() && !$model->vendor_id) {
                $model->vendor_id = $tenantManager->getVendorId();
            }
        });
    }
}
```

## 📊 **DATA ISOLATION MATRIX**

| Model | User Context | Vendor Context | Admin Context |
|-------|-------------|----------------|---------------|
| `Auction` | ✅ UserTenantModel | ❌ | ✅ Global |
| `XenditPayment` | ✅ UserTenantModel | ❌ | ✅ Global |
| `DeliveryConfirmation` | ✅ UserTenantModel | ❌ | ✅ Global |
| `ShippingInvoice` | ✅ UserTenantModel | ❌ | ✅ Global |
| `VendorRating` | ✅ UserTenantModel | ❌ | ✅ Global |
| `Produk` | ❌ | ✅ TenantModel | ✅ Global |
| `Transaksi` | ❌ | ✅ TenantModel | ✅ Global |
| `Bahan` | ❌ | ✅ TenantModel | ✅ Global |
| `Alat` | ❌ | ✅ TenantModel | ✅ Global |

## 🧪 **TESTING TENANT CONTEXT**

### **Test Command**
```bash
# Test user tenant context
php artisan test:tenant-context --user-type=user

# Test vendor tenant context  
php artisan test:tenant-context --user-type=vendor

# Test dev global access
php artisan test:tenant-context --user-type=dev
```

### **Test Results**
```
🧪 Testing tenant context for user type: user
👤 Testing User Tenant Context...
✅ User tenant context set: user-tenant@example.com
🔍 Testing user data isolation...
✅ Created auction: Test User Auction
📊 User can see 1 auctions
📊 Other user can see 0 auctions
✅ User data isolation working correctly!

🏢 Testing Vendor Tenant Context...
✅ Vendor tenant context set: Test Vendor Tenant
📊 Vendor: Test Vendor Tenant
📊 Vendor ID: 5
✅ Tenant context active: Test Vendor Tenant
✅ Vendor data isolation working correctly!

👨‍💻 Testing Dev Tenant Context...
✅ Dev user created: dev-tenant@example.com
ℹ️  Dev users have global access (no tenant context)
📊 Dev can see 4 users
📊 Dev can see 2 vendors  
📊 Dev can see 3 auctions
✅ Dev has global access to all data
```

## 🔒 **KEAMANAN & ISOLASI**

### **1. Data Isolation**
- ✅ **User**: Hanya melihat auctions, payments, ratings milik sendiri
- ✅ **Vendor**: Hanya melihat products, transactions, customers milik vendor mereka
- ✅ **Admin**: Dapat melihat semua data untuk monitoring

### **2. Automatic Scoping**
- ✅ **Global Scopes**: Otomatis diterapkan pada semua query
- ✅ **Auto-fill**: `user_id`/`vendor_id` otomatis diisi saat create
- ✅ **Session Fallback**: Tenant context tersimpan di session

### **3. Security Benefits**
- ✅ **SQL Injection Protection**: Data terisolasi per tenant
- ✅ **Data Leakage Prevention**: User tidak bisa akses data user lain
- ✅ **Audit Trail**: Semua akses data tercatat dengan tenant context

## 🚀 **PERFORMA & OPTIMASI**

### **1. Database Indexing**
```sql
-- Index untuk tenant context
CREATE INDEX idx_auctions_user_id ON auctions(user_id);
CREATE INDEX idx_produks_vendor_id ON produks(vendor_id);
CREATE INDEX idx_transaksis_vendor_id ON transaksis(vendor_id);
```

### **2. Query Optimization**
```php
// Efficient tenant queries
$userAuctions = Auction::forCurrentUser()->with('bids')->get();
$vendorProducts = Produk::forCurrentVendor()->active()->get();
```

### **3. Caching Strategy**
```php
// Cache tenant-specific data
$cacheKey = "user_{$userId}_auctions";
$auctions = Cache::remember($cacheKey, 3600, function() {
    return Auction::forCurrentUser()->get();
});
```

## 📋 **BEST PRACTICES**

### **1. Model Usage**
```php
// ✅ Correct - User data
class Auction extends UserTenantModel
{
    // Automatically scoped to current user
}

// ✅ Correct - Vendor data  
class Produk extends TenantModel
{
    // Automatically scoped to current vendor
}

// ✅ Correct - Admin data
class AdminFeeSetting extends Model
{
    // Global access for admin
}
```

### **2. Controller Usage**
```php
// ✅ Correct - User controller
public function myAuctions()
{
    // Automatically filtered to current user
    $auctions = Auction::forCurrentUser()->get();
    return view('user.auctions', compact('auctions'));
}

// ✅ Correct - Vendor controller
public function myProducts()
{
    // Automatically filtered to current vendor
    $products = Produk::forCurrentVendor()->get();
    return view('vendor.products', compact('products'));
}
```

### **3. Testing**
```php
// ✅ Test tenant isolation
public function testUserDataIsolation()
{
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    // Set tenant context for user1
    Tenant::setUser($user1);
    
    // Create auction for user1
    $auction = Auction::create(['user_id' => $user1->id, ...]);
    
    // User1 should see the auction
    $this->assertEquals(1, Auction::forCurrentUser()->count());
    
    // Switch to user2 context
    Tenant::setUser($user2);
    
    // User2 should not see user1's auction
    $this->assertEquals(0, Auction::forCurrentUser()->count());
}
```

## 🎯 **KESIMPULAN**

### **✅ BENEFITS**
1. **Data Security**: Setiap user/vendor hanya melihat data milik sendiri
2. **Scalability**: Dapat menangani ribuan user dan vendor
3. **Performance**: Query teroptimasi dengan proper indexing
4. **Maintainability**: Kode lebih bersih dan mudah dipahami
5. **Compliance**: Memenuhi standar keamanan data

### **🏗️ ARCHITECTURE SUMMARY**
- **Database**: Shared database dengan tenant context
- **Isolation**: User dan Vendor memiliki data terisolasi
- **Admin Access**: Dev/Admin memiliki akses global
- **Security**: Automatic scoping mencegah data leakage
- **Performance**: Optimized queries dengan proper indexing

**Aplikasi Grafika Printing sekarang memiliki arsitektur multi-tenant yang benar, aman, dan scalable!** 🚀

# 🌱 Seeder Guide - Grafika Printing

## 📋 Overview

Grafika Printing menggunakan **Simple Seeder** yang hanya membuat 3 user untuk testing manual yang bersih dan minimalis.

## 🚀 Quick Start

### Simple Seeder (Default)

```bash
# Method 1: Using default seeder (recommended)
php artisan migrate:fresh --seed

# Method 2: Using seeder class directly
php artisan db:seed --class=SimpleTestSeeder

# Method 3: Using artisan command
php artisan seed:simple
```

## 👥 Simple Seeder - Test Users

### 🔧 DEV User (Super Admin)
- **Email:** `dev@grafika-printing.com`
- **Password:** `password`
- **URL:** `/admin`
- **Features:** Full admin access, user management, vendor management, auction management, payment management, etc.

### 👤 USER (Regular User)
- **Email:** `user@example.com`
- **Password:** `password`
- **URL:** `/user`
- **Features:** Create auctions, make payments, track orders, delivery confirmation

### 🏢 VENDOR (Vendor Owner)
- **Email:** `vendor@example.com`
- **Password:** `password`
- **URL:** `/vendor`
- **Features:** POS system, product management, auction bidding, order tracking, wallet management

## 📊 What's Included

The simple seeder creates:

- ✅ **3 Users:** 1 Dev, 1 User, 1 Vendor
- ✅ **1 Vendor Company:** Linked to vendor user
- ✅ **Clean Database:** No dummy data, ready for manual testing
- ✅ **All Features Available:** Can test all features with these 3 users

## 🛠️ Manual Testing Workflow

### 1. Start with Fresh Database
```bash
php artisan migrate:fresh --seed
```

### 2. Test Each User Type

#### Test DEV Features:
1. Login as `dev@grafika-printing.com`
2. Go to `/admin`
3. Test: User Management, Vendor Management, Auction Management, Payment Management

#### Test USER Features:
1. Login as `user@example.com`
2. Go to `/user`
3. Test: Create Auction, Make Payment, Track Orders

#### Test VENDOR Features:
1. Login as `vendor@example.com`
2. Go to `/vendor`
3. Test: POS System, Product Management, Auction Bidding

### 3. Test Integration Features
- Payment Gateway (Xendit)
- Shipping Calculator (RajaOngkir)
- Multi-tenant Architecture
- API Endpoints

## 🔄 Reset Database

If you need to start fresh:

```bash
# Reset and seed with simple data (recommended)
php artisan migrate:fresh --seed

# Or just reset without seeding
php artisan migrate:fresh
```

## 📝 Notes

- **Simple Seeder** is safe to run multiple times (uses `firstOrCreate`)
- All users have `password` as password for easy testing
- All users are email verified by default
- Vendor user is linked to vendor company automatically
- Database is clean with only essential data for testing

## 🎯 Testing Checklist

### Admin Features
- [ ] User Management
- [ ] Vendor Management
- [ ] Auction Management
- [ ] Payment Management
- [ ] Admin Fee Settings
- [ ] Mediation System
- [ ] Audit Logs
- [ ] Analytics

### Vendor Features
- [ ] POS System
- [ ] Product Management
- [ ] Customer Management
- [ ] Transaction Management
- [ ] Auction Bidding
- [ ] Order Tracking
- [ ] Wallet Management
- [ ] Withdrawal System

### User Features
- [ ] Auction Creation
- [ ] Payment Confirmation
- [ ] Order Tracking
- [ ] Delivery Confirmation

### Integration Features
- [ ] Xendit Payment Gateway
- [ ] RajaOngkir Shipping
- [ ] Multi-tenant Architecture
- [ ] API Endpoints

---

**Happy Testing! 🚀**

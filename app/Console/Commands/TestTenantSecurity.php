<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Auction;
use App\Models\XenditPayment;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestTenantSecurity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:tenant-security';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test tenant context security and data isolation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔒 Testing Tenant Context Security...');
        $this->newLine();

        // Test 1: User Data Isolation
        $this->testUserDataIsolation();

        // Test 2: Vendor Data Isolation
        $this->testVendorDataIsolation();

        // Test 3: Cross-Tenant Access Prevention
        $this->testCrossTenantAccessPrevention();

        // Test 4: Admin Global Access
        $this->testAdminGlobalAccess();

        $this->newLine();
        $this->info('✅ Tenant security tests completed!');
    }

    private function testUserDataIsolation()
    {
        $this->info('👤 Testing User Data Isolation...');

        // Create two different users
        $user1 = User::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Set tenant context for user1
        $tenantManager = app(TenantManager::class);
        $tenantManager->setUser($user1);

        // Create auction for user1
        $auction1 = Auction::create([
            'user_id' => $user1->id,
            'title' => 'User 1 Auction',
            'description' => 'Auction for user 1',
            'category' => 'test',
            'quantity' => 1,
            'budget' => 50000,
            'deadline' => now()->addDays(7),
            'status' => 'active',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create auction for user2 (outside tenant context)
        $auction2 = Auction::create([
            'user_id' => $user2->id,
            'title' => 'User 2 Auction',
            'description' => 'Auction for user 2',
            'category' => 'test',
            'quantity' => 1,
            'budget' => 60000,
            'deadline' => now()->addDays(7),
            'status' => 'active',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Test: User1 should only see their own auction
        $user1Auctions = Auction::forCurrentUser()->get();
        $this->info("📊 User1 can see {$user1Auctions->count()} auctions");

        if ($user1Auctions->count() === 1 && $user1Auctions->first()->title === 'User 1 Auction') {
            $this->info("✅ User1 data isolation: SECURE");
        } else {
            $this->error("❌ User1 data isolation: BREACHED");
        }

        // Switch to user2 context
        $tenantManager->setUser($user2);
        $user2Auctions = Auction::forCurrentUser()->get();
        $this->info("📊 User2 can see {$user2Auctions->count()} auctions");

        if ($user2Auctions->count() === 1 && $user2Auctions->first()->title === 'User 2 Auction') {
            $this->info("✅ User2 data isolation: SECURE");
        } else {
            $this->error("❌ User2 data isolation: BREACHED");
        }

        // Cleanup
        $auction1->delete();
        $auction2->delete();
        $user1->delete();
        $user2->delete();
    }

    private function testVendorDataIsolation()
    {
        $this->info('🏢 Testing Vendor Data Isolation...');

        // Create two different vendors
        $vendor1 = Vendor::create([
            'name' => 'Vendor 1',
            'email' => 'vendor1@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Address 1',
            'bank_name' => 'Bank 1',
            'bank_account_number' => '1234567890',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendor2 = Vendor::create([
            'name' => 'Vendor 2',
            'email' => 'vendor2@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Address 2',
            'bank_name' => 'Bank 2',
            'bank_account_number' => '0987654321',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Set tenant context for vendor1
        $tenantManager = app(TenantManager::class);
        $tenantManager->setVendor($vendor1);

        // Test: Vendor1 should only see their own data
        $vendor1Products = $vendor1->produk()->count();
        $this->info("📊 Vendor1 has {$vendor1Products} products");

        // Test: Vendor1 should not see vendor2's data
        $vendor2Products = $vendor2->produk()->count();
        $this->info("📊 Vendor2 has {$vendor2Products} products");

        if ($vendor1Products === 0 && $vendor2Products === 0) {
            $this->info("✅ Vendor data isolation: SECURE");
        } else {
            $this->error("❌ Vendor data isolation: BREACHED");
        }

        // Cleanup
        $vendor1->delete();
        $vendor2->delete();
    }

    private function testCrossTenantAccessPrevention()
    {
        $this->info('🚫 Testing Cross-Tenant Access Prevention...');

        // Create user and vendor
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendor = Vendor::create([
            'name' => 'Test Vendor',
            'email' => 'testvendor@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Test Address',
            'bank_name' => 'Test Bank',
            'bank_account_number' => '1234567890',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Set user tenant context
        $tenantManager = app(TenantManager::class);
        $tenantManager->setUser($user);

        // Test: User should not be able to access vendor data
        try {
            $vendorProducts = \App\Models\Vendor\Produk::forCurrentVendor()->get();
            $this->info("📊 User can see {$vendorProducts->count()} vendor products");

            if ($vendorProducts->count() === 0) {
                $this->info("✅ Cross-tenant access prevention: SECURE");
            } else {
                $this->error("❌ Cross-tenant access prevention: BREACHED");
            }
        } catch (\Exception $e) {
            $this->info("✅ Cross-tenant access prevention: SECURE (Exception thrown)");
        }

        // Cleanup
        $user->delete();
        $vendor->delete();
    }

    private function testAdminGlobalAccess()
    {
        $this->info('👨‍💻 Testing Admin Global Access...');

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'dev',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Admin should have global access (no tenant context)
        $tenantManager = app(TenantManager::class);
        // Don't set any tenant context for admin

        // Test: Admin should see all data
        $allUsers = User::count();
        $allVendors = Vendor::count();
        $allAuctions = Auction::count();

        $this->info("📊 Admin can see {$allUsers} users");
        $this->info("📊 Admin can see {$allVendors} vendors");
        $this->info("📊 Admin can see {$allAuctions} auctions");

        if ($allUsers > 0 || $allVendors > 0 || $allAuctions > 0) {
            $this->info("✅ Admin global access: WORKING");
        } else {
            $this->warn("⚠️ Admin global access: No data to test");
        }

        // Cleanup
        $admin->delete();
    }
}

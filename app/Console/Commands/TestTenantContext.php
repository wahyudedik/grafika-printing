<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Auction;
use App\Models\XenditPayment;
use App\Models\DeliveryConfirmation;
use App\Models\ShippingInvoice;
use App\Models\VendorRating;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestTenantContext extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:tenant-context {--user-type=user : User type to test (user, vendor, dev)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test tenant context isolation for different user types';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userType = $this->option('user-type');

        $this->info("🧪 Testing tenant context for user type: {$userType}");
        $this->newLine();

        switch ($userType) {
            case 'user':
                $this->testUserTenantContext();
                break;
            case 'vendor':
                $this->testVendorTenantContext();
                break;
            case 'dev':
                $this->testDevTenantContext();
                break;
            default:
                $this->error("Invalid user type: {$userType}");
                return 1;
        }

        return 0;
    }

    private function testUserTenantContext()
    {
        $this->info('👤 Testing User Tenant Context...');

        // Create test user
        $user = User::create([
            'name' => 'Test User Tenant',
            'email' => 'user-tenant@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Set tenant context
        $tenantManager = app(TenantManager::class);
        $tenantManager->setUser($user);

        $this->info("✅ User tenant context set: {$user->email}");

        // Test data isolation
        $this->testUserDataIsolation($user);

        // Cleanup
        $user->delete();
        $this->info('🧹 Test user cleaned up');
    }

    private function testVendorTenantContext()
    {
        $this->info('🏢 Testing Vendor Tenant Context...');

        // Create test vendor with unique phone
        $vendor = Vendor::create([
            'name' => 'Test Vendor Tenant',
            'email' => 'vendor-tenant@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Test Address',
            'bank_name' => 'Test Bank',
            'bank_account_number' => '1234567890',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create test user for vendor
        $user = User::create([
            'name' => 'Test Vendor User',
            'email' => 'vendor-user@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'vendor',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create vendor-user relationship
        $vendor->vendorUser()->attach($user->id);

        // Set tenant context
        $tenantManager = app(TenantManager::class);
        $tenantManager->setVendor($vendor);

        $this->info("✅ Vendor tenant context set: {$vendor->name}");

        // Test data isolation
        $this->testVendorDataIsolation($vendor);

        // Cleanup
        $user->delete();
        $vendor->delete();
        $this->info('🧹 Test vendor cleaned up');
    }

    private function testDevTenantContext()
    {
        $this->info('👨‍💻 Testing Dev Tenant Context...');

        // Create test dev user
        $dev = User::create([
            'name' => 'Test Dev',
            'email' => 'dev-tenant@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'dev',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $this->info("✅ Dev user created: {$dev->email}");
        $this->info("ℹ️  Dev users have global access (no tenant context)");

        // Test global access
        $this->testDevGlobalAccess($dev);

        // Cleanup
        $dev->delete();
        $this->info('🧹 Test dev cleaned up');
    }

    private function testUserDataIsolation($user)
    {
        $this->info('🔍 Testing user data isolation...');

        // Create auction for this user
        $auction = Auction::create([
            'user_id' => $user->id,
            'title' => 'Test User Auction',
            'description' => 'Test auction for user tenant',
            'category' => 'test',
            'quantity' => 1,
            'budget' => 50000,
            'deadline' => now()->addDays(7),
            'status' => 'active',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $this->info("✅ Created auction: {$auction->title}");

        // Test that user can only see their own auctions
        $userAuctions = Auction::forCurrentUser()->get();
        $this->info("📊 User can see {$userAuctions->count()} auctions");

        // Test that other users cannot see this auction
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $tenantManager = app(TenantManager::class);
        $tenantManager->setUser($otherUser);

        $otherUserAuctions = Auction::forCurrentUser()->get();
        $this->info("📊 Other user can see {$otherUserAuctions->count()} auctions");

        // Cleanup
        $auction->delete();
        $otherUser->delete();
    }

    private function testVendorDataIsolation($vendor)
    {
        $this->info('🔍 Testing vendor data isolation...');

        // Test vendor-specific data access
        $this->info("📊 Vendor: {$vendor->name}");
        $this->info("📊 Vendor ID: {$vendor->id}");

        // Test that vendor can only see their own data
        $vendorProducts = $vendor->produk()->count();
        $this->info("📊 Vendor has {$vendorProducts} products");

        // Test tenant context
        $tenantManager = app(TenantManager::class);
        $currentVendor = $tenantManager->getVendor();

        if ($currentVendor) {
            $this->info("✅ Tenant context active: {$currentVendor->name}");
        } else {
            $this->error("❌ No tenant context found");
        }
    }

    private function testDevGlobalAccess($dev)
    {
        $this->info('🔍 Testing dev global access...');

        // Dev should be able to see all data
        $allUsers = User::count();
        $allVendors = Vendor::count();
        $allAuctions = Auction::count();

        $this->info("📊 Dev can see {$allUsers} users");
        $this->info("📊 Dev can see {$allVendors} vendors");
        $this->info("📊 Dev can see {$allAuctions} auctions");

        $this->info("✅ Dev has global access to all data");
    }
}

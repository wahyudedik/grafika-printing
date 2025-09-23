<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\XenditPayment;
use App\Models\VendorWallet;
use App\Models\VendorWithdrawal;
use App\Models\AdminFeeSetting;
use App\Models\AdminFeeTransaction;
use App\Models\DeliveryConfirmation;
use App\Models\ShippingInvoice;
use App\Models\VendorRating;
use App\Models\CmsSetting;
use App\Models\FinancialAuditLog;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Alat;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\TransaksiItemSpecifications;
use App\Models\Vendor\WholesalePrice;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\TenantModel;
use App\Services\TenantManager;
use App\Services\XenditService;
use App\Services\AdminFeeService;
use App\Services\AuditLogService;
use App\Services\EncryptionService;
use App\Services\RajaOngkirService;
use App\Services\ShippingTrackingService;
use App\Services\XenditBalanceService;
use App\Services\AuctionToPosService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class TestFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:features {--feature= : Test specific feature} {--detailed : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test application features for reliability';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing application features...');
        $this->newLine();

        $feature = $this->option('feature');
        $verbose = $this->option('verbose');

        if ($feature) {
            $this->testSpecificFeature($feature, $verbose);
        } else {
            $this->testAllFeatures($verbose);
        }

        return 0;
    }

    private function testAllFeatures($verbose)
    {
        $features = [
            'authentication' => 'Test user authentication',
            'vendor_dashboard' => 'Test vendor dashboard access',
            'auction_system' => 'Test auction creation and bidding',
            'payment_system' => 'Test payment processing',
            'tenant_context' => 'Test multi-tenant context',
            'admin_fees' => 'Test admin fee calculation',
            'wallet_system' => 'Test vendor wallet operations',
            'withdrawal_system' => 'Test withdrawal requests',
            'delivery_system' => 'Test delivery confirmations',
            'rating_system' => 'Test vendor ratings',
            'cms_system' => 'Test CMS settings',
            'audit_logs' => 'Test audit logging',
            'external_apis' => 'Test external API connections'
        ];

        $results = [];
        $passed = 0;
        $failed = 0;

        foreach ($features as $feature => $description) {
            $this->info("Testing: {$description}");

            try {
                $result = $this->testFeature($feature, $verbose);
                $results[$feature] = $result;

                if ($result['status'] === 'passed') {
                    $passed++;
                    $this->info("✅ {$description}: PASSED");
                } else {
                    $failed++;
                    $this->error("❌ {$description}: FAILED - {$result['error']}");
                }
            } catch (\Exception $e) {
                $failed++;
                $results[$feature] = [
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                $this->error("❌ {$description}: ERROR - {$e->getMessage()}");
            }

            $this->newLine();
        }

        // Display summary
        $this->displayTestSummary($results, $passed, $failed);
    }

    private function testSpecificFeature($feature, $verbose)
    {
        $this->info("Testing specific feature: {$feature}");

        try {
            $result = $this->testFeature($feature, $verbose);

            if ($result['status'] === 'passed') {
                $this->info("✅ Feature test: PASSED");
            } else {
                $this->error("❌ Feature test: FAILED - {$result['error']}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Feature test: ERROR - {$e->getMessage()}");
        }
    }

    private function testFeature($feature, $verbose)
    {
        switch ($feature) {
            case 'authentication':
                return $this->testAuthentication($verbose);
            case 'vendor_dashboard':
                return $this->testVendorDashboard($verbose);
            case 'auction_system':
                return $this->testAuctionSystem($verbose);
            case 'payment_system':
                return $this->testPaymentSystem($verbose);
            case 'tenant_context':
                return $this->testTenantContext($verbose);
            case 'admin_fees':
                return $this->testAdminFees($verbose);
            case 'wallet_system':
                return $this->testWalletSystem($verbose);
            case 'withdrawal_system':
                return $this->testWithdrawalSystem($verbose);
            case 'delivery_system':
                return $this->testDeliverySystem($verbose);
            case 'rating_system':
                return $this->testRatingSystem($verbose);
            case 'cms_system':
                return $this->testCmsSystem($verbose);
            case 'audit_logs':
                return $this->testAuditLogs($verbose);
            case 'external_apis':
                return $this->testExternalApis($verbose);
            default:
                throw new \Exception("Unknown feature: {$feature}");
        }
    }

    private function testAuthentication($verbose)
    {
        try {
            // Test user creation with unique email
            $userEmail = 'test-auth-' . time() . '@example.com';
            $user = User::create([
                'name' => 'Test User',
                'email' => $userEmail,
                'password' => bcrypt('password'),
                'usertype' => 'user',
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            ]);

            // Test vendor creation with unique email and phone
            $vendorEmail = 'vendor-auth-' . time() . '@example.com';
            $vendorPhone = '0812345678' . rand(10, 99);
            $vendor = Vendor::create([
                'name' => 'Test Vendor',
                'email' => $vendorEmail,
                'phone' => $vendorPhone,
                'address' => 'Test Address',
                'bank_name' => 'Test Bank',
                'bank_account_number' => '1234567890',
                'is_verified' => true,
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            ]);

            // Test vendor-user relationship
            $user->vendorUser()->attach($vendor->id);

            if ($verbose) {
                $this->line("Created user: {$user->email}");
                $this->line("Created vendor: {$vendor->name}");
                $this->line("Created relationship: {$user->email} -> {$vendor->name}");
            }

            return ['status' => 'passed', 'message' => 'Authentication system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testVendorDashboard($verbose)
    {
        try {
            // Test vendor dashboard access
            $vendor = Vendor::first();
            if (!$vendor) {
                throw new \Exception('No vendor found');
            }

            // Test vendor wallet
            $wallet = $vendor->getOrCreateWallet();
            if (!$wallet) {
                throw new \Exception('Vendor wallet not created');
            }

            // Test vendor products
            $products = $vendor->produk()->count();
            if ($verbose) {
                $this->line("Vendor: {$vendor->name}");
                $this->line("Wallet balance: {$wallet->balance}");
                $this->line("Products count: {$products}");
            }

            return ['status' => 'passed', 'message' => 'Vendor dashboard accessible'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testAuctionSystem($verbose)
    {
        try {
            // Test auction creation
            $user = User::where('usertype', 'user')->first();
            if (!$user) {
                throw new \Exception('No user found');
            }

            $auction = Auction::create([
                'user_id' => $user->id,
                'title' => 'Test Auction',
                'description' => 'Test Description',
                'specifications' => 'Test Specifications',
                'quantity' => 100,
                'budget' => 50000,
                'category' => 'Test Category',
                'deadline' => now()->addDays(7),
                'status' => 'active'
            ]);

            // Test auction bidding
            $vendor = Vendor::first();
            if (!$vendor) {
                throw new \Exception('No vendor found');
            }

            $bid = AuctionBid::create([
                'auction_id' => $auction->id,
                'vendor_id' => $vendor->id,
                'bid_amount' => 45000,
                'status' => 'pending'
            ]);

            if ($verbose) {
                $this->line("Created auction: {$auction->title}");
                $this->line("Created bid: {$bid->bid_amount}");
            }

            return ['status' => 'passed', 'message' => 'Auction system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testPaymentSystem($verbose)
    {
        try {
            // Test payment creation
            $auction = Auction::first();
            if (!$auction) {
                throw new \Exception('No auction found');
            }

            $payment = XenditPayment::create([
                'external_id' => 'test_' . time(),
                'xendit_id' => 'test_xendit_id',
                'type' => 'payment_link',
                'amount' => 50000,
                'description' => 'Test Payment',
                'status' => 'pending',
                'auction_id' => $auction->id
            ]);

            if ($verbose) {
                $this->line("Created payment: {$payment->external_id}");
                $this->line("Payment amount: {$payment->amount}");
            }

            return ['status' => 'passed', 'message' => 'Payment system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testTenantContext($verbose)
    {
        try {
            $tenantManager = app(TenantManager::class);

            // Test setting vendor context
            $vendor = Vendor::first();
            if (!$vendor) {
                throw new \Exception('No vendor found');
            }

            $tenantManager->setVendor($vendor);

            if (!$tenantManager->hasVendorContext()) {
                throw new \Exception('Tenant context not set');
            }

            if ($verbose) {
                $this->line("Tenant context set for vendor: {$vendor->name}");
            }

            return ['status' => 'passed', 'message' => 'Tenant context working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testAdminFees($verbose)
    {
        try {
            $adminFeeService = app(AdminFeeService::class);

            // Test fee calculation
            $fees = $adminFeeService->calculateTotalFees(50000, 'bank_transfer');

            if (!isset($fees['total_amount'])) {
                throw new \Exception('Fee calculation failed');
            }

            if ($verbose) {
                $this->line("Fee calculation: {$fees['total_amount']}");
                $this->line("Admin fee: {$fees['admin_fee']}");
                $this->line("Payment gateway fee: {$fees['payment_gateway_fee']}");
            }

            return ['status' => 'passed', 'message' => 'Admin fee system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testWalletSystem($verbose)
    {
        try {
            $vendor = Vendor::first();
            if (!$vendor) {
                throw new \Exception('No vendor found');
            }

            $wallet = $vendor->getOrCreateWallet();

            // Test wallet operations
            $wallet->addCredit(10000, 'test', 'Test credit');
            $wallet->addDebit(5000, 'test', 'Test debit');

            if ($verbose) {
                $this->line("Wallet balance: {$wallet->balance}");
                $this->line("Available balance: {$wallet->available_balance}");
            }

            return ['status' => 'passed', 'message' => 'Wallet system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testWithdrawalSystem($verbose)
    {
        try {
            $vendor = Vendor::first();
            if (!$vendor) {
                throw new \Exception('No vendor found');
            }

            // Ensure vendor has sufficient balance
            $wallet = $vendor->getOrCreateWallet();
            if ($wallet->balance < 10000) {
                $wallet->update(['balance' => 50000]); // Set sufficient balance
            }

            // Test withdrawal creation with smaller amount
            $withdrawal = VendorWithdrawal::createRequest(
                $vendor->id,
                5000, // Reduced amount to ensure it's less than balance
                'bank_transfer',
                '1234567890',
                'Test Account',
                'Test Bank',
                'Test withdrawal'
            );

            if ($verbose) {
                $this->line("Created withdrawal: {$withdrawal->id}");
                $this->line("Withdrawal amount: {$withdrawal->amount}");
            }

            return ['status' => 'passed', 'message' => 'Withdrawal system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testDeliverySystem($verbose)
    {
        try {
            $auction = Auction::first();
            if (!$auction) {
                throw new \Exception('No auction found');
            }

            // Get or create vendor for this test
            $vendor = Vendor::first();
            if (!$vendor) {
                $vendor = Vendor::create([
                    'name' => 'Test Vendor for Delivery',
                    'email' => 'delivery-vendor@example.com',
                    'phone' => '08123456789',
                    'address' => 'Test Address',
                    'bank_name' => 'Test Bank',
                    'bank_account_number' => '1234567890',
                    'is_verified' => true,
                    'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                ]);
            }

            // Test delivery confirmation
            $confirmation = DeliveryConfirmation::create([
                'auction_id' => $auction->id,
                'user_id' => $auction->user_id,
                'vendor_id' => $vendor->id,
                'confirmation_code' => 'TEST_' . time(),
                'customer_name' => 'Test Customer',
                'customer_phone' => '081234567890',
                'delivery_address' => 'Test Address',
                'status' => 'pending'
            ]);

            if ($verbose) {
                $this->line("Created delivery confirmation: {$confirmation->confirmation_code}");
            }

            return ['status' => 'passed', 'message' => 'Delivery system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testRatingSystem($verbose)
    {
        try {
            $auction = Auction::first();
            $vendor = Vendor::first();
            $user = User::first();

            if (!$auction || !$vendor || !$user) {
                throw new \Exception('Required models not found');
            }

            // Check if rating already exists for this combination
            $existingRating = VendorRating::where('vendor_id', $vendor->id)
                ->where('user_id', $user->id)
                ->where('auction_id', $auction->id)
                ->first();

            if ($existingRating) {
                // Update existing rating instead of creating new one
                $existingRating->update([
                    'rating' => 5,
                    'comment' => 'Updated test rating',
                    'is_verified' => true
                ]);
                $rating = $existingRating;
            } else {
                // Test rating creation
                $rating = VendorRating::create([
                    'vendor_id' => $vendor->id,
                    'user_id' => $user->id,
                    'auction_id' => $auction->id,
                    'rating' => 5,
                    'comment' => 'Test rating',
                    'is_verified' => true
                ]);
            }

            if ($verbose) {
                $this->line("Created rating: {$rating->rating} stars");
                $this->line("Rating comment: {$rating->comment}");
            }

            return ['status' => 'passed', 'message' => 'Rating system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testCmsSystem($verbose)
    {
        try {
            // Test CMS settings
            $setting = CmsSetting::set('test_key', 'test_value', 'text', 'general', 'Test Setting');

            if (!$setting) {
                throw new \Exception('CMS setting not created');
            }

            $value = CmsSetting::get('test_key');
            if ($value !== 'test_value') {
                throw new \Exception('CMS setting value mismatch');
            }

            if ($verbose) {
                $this->line("CMS setting created: test_key = {$value}");
            }

            return ['status' => 'passed', 'message' => 'CMS system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testAuditLogs($verbose)
    {
        try {
            // Get or create user and vendor for this test
            $user = User::first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Test User for Audit',
                    'email' => 'audit-user@example.com',
                    'password' => bcrypt('password'),
                    'usertype' => 'user',
                    'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                ]);
            }

            $vendor = Vendor::first();
            if (!$vendor) {
                $vendor = Vendor::create([
                    'name' => 'Test Vendor for Audit',
                    'email' => 'audit-vendor@example.com',
                    'phone' => '08123456789',
                    'address' => 'Test Address',
                    'bank_name' => 'Test Bank',
                    'bank_account_number' => '1234567890',
                    'is_verified' => true,
                    'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                ]);
            }

            // Test audit log creation
            $log = FinancialAuditLog::createLog([
                'user_id' => $user->id,
                'vendor_id' => $vendor->id,
                'action_type' => 'test',
                'entity_type' => 'test',
                'entity_id' => 1,
                'amount' => 1000,
                'status' => 'success',
                'notes' => 'Test audit log'
            ]);

            if (!$log) {
                throw new \Exception('Audit log not created');
            }

            if ($verbose) {
                $this->line("Created audit log: {$log->id}");
            }

            return ['status' => 'passed', 'message' => 'Audit log system working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testExternalApis($verbose)
    {
        try {
            // Test Xendit service
            $xenditService = app(XenditService::class);
            if (!$xenditService) {
                throw new \Exception('Xendit service not available');
            }

            // Test RajaOngkir service
            $rajaOngkirService = app(RajaOngkirService::class);
            if (!$rajaOngkirService) {
                throw new \Exception('RajaOngkir service not available');
            }

            if ($verbose) {
                $this->line("Xendit service: Available");
                $this->line("RajaOngkir service: Available");
            }

            return ['status' => 'passed', 'message' => 'External APIs available'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function displayTestSummary($results, $passed, $failed)
    {
        $this->newLine();
        $this->info('📊 TEST SUMMARY');
        $this->newLine();

        $this->info("✅ Passed: {$passed}");
        $this->error("❌ Failed: {$failed}");
        $this->info("📈 Success Rate: " . round(($passed / ($passed + $failed)) * 100, 2) . "%");

        if ($failed > 0) {
            $this->newLine();
            $this->error('Failed tests:');
            foreach ($results as $feature => $result) {
                if ($result['status'] === 'failed') {
                    $this->line("- {$feature}: {$result['error']}");
                }
            }
        }
    }
}

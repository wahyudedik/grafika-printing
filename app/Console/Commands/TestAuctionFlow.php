<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\XenditPayment;
use App\Models\DeliveryConfirmation;
use App\Models\VendorRating;
use App\Services\AdminFeeService;
use App\Services\TenantManager;
use Illuminate\Console\Command;

class TestAuctionFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:auction-flow {--step=all : Test specific step (create, approve, bid, payment, delivery, complete)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test complete auction flow with admin approval';

    protected $adminFeeService;

    public function __construct(AdminFeeService $adminFeeService)
    {
        parent::__construct();
        $this->adminFeeService = $adminFeeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $step = $this->option('step');

        $this->info('🎯 Testing Complete Auction Flow...');
        $this->newLine();

        switch ($step) {
            case 'create':
                $this->testAuctionCreation();
                break;
            case 'approve':
                $this->testAdminApproval();
                break;
            case 'bid':
                $this->testVendorBidding();
                break;
            case 'payment':
                $this->testPaymentProcess();
                break;
            case 'delivery':
                $this->testDeliveryProcess();
                break;
            case 'complete':
                $this->testCompletionProcess();
                break;
            case 'all':
            default:
                $this->testCompleteFlow();
                break;
        }

        return 0;
    }

    private function testCompleteFlow()
    {
        $this->info('🔄 Testing Complete Auction Flow...');

        // Create test data once for the entire flow
        $user = User::create([
            'name' => 'Test User Complete Flow',
            'email' => 'user-complete-flow@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendor = Vendor::create([
            'name' => 'Vendor Complete Flow',
            'email' => 'vendor-complete-flow@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Address',
            'bank_name' => 'Bank',
            'bank_account_number' => '1234567890',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendorUser = User::create([
            'name' => 'Vendor User Complete',
            'email' => 'vendoruser-complete@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'vendor',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendor->vendorUser()->attach($vendorUser->id);

        $admin = User::create([
            'name' => 'Admin Complete',
            'email' => 'admin-complete@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'dev',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Step 1: Create Auction
        $this->testAuctionCreationWithData($user);

        // Step 2: Admin Approval
        $this->testAdminApprovalWithData($user, $admin);

        // Step 3: Vendor Bidding
        $this->testVendorBiddingWithData($user, $vendor, $vendorUser);

        // Step 4: Payment Process
        $this->testPaymentProcessWithData($user, $vendor);

        // Step 5: Delivery Process
        $this->testDeliveryProcessWithData($user, $vendor);

        // Step 6: Completion Process
        $this->testCompletionProcessWithData($user, $vendor);

        // Cleanup
        $user->delete();
        $vendor->delete();
        $vendorUser->delete();
        $admin->delete();

        $this->newLine();
        $this->info('✅ Complete auction flow test finished!');
    }

    private function testAuctionCreation()
    {
        $this->info('📝 Step 1: Testing Auction Creation...');

        // Create test user
        $user = User::create([
            'name' => 'Test User Flow',
            'email' => 'user-flow@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Set user tenant context
        $tenantManager = app(TenantManager::class);
        $tenantManager->setUser($user);

        // Create auction
        $auction = Auction::create([
            'user_id' => $user->id,
            'title' => 'Test Auction Flow',
            'description' => 'Complete auction flow test',
            'category' => 'printing',
            'quantity' => 100,
            'budget' => 500000,
            'deadline' => now()->addDays(7),
            'status' => 'pending',
            'admin_approval_status' => 'pending',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $this->info("✅ Auction created: {$auction->title}");
        $this->info("📊 Status: {$auction->status}");
        $this->info("📊 Admin Approval: {$auction->admin_approval_status}");

        // Test that auction is not visible to vendors yet
        $visibleAuctions = Auction::approved()->active()->count();
        $this->info("📊 Visible auctions to vendors: {$visibleAuctions}");

        if ($visibleAuctions === 0) {
            $this->info("✅ Auction correctly hidden until approval");
        } else {
            $this->error("❌ Auction should be hidden until approval");
        }

        // Cleanup
        $auction->delete();
        $user->delete();
    }

    private function testAdminApproval()
    {
        $this->info('👨‍💻 Step 2: Testing Admin Approval...');

        // Create test user and auction
        $user = User::create([
            'name' => 'Test User Approval',
            'email' => 'user-approval@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $auction = Auction::create([
            'user_id' => $user->id,
            'title' => 'Test Auction Approval',
            'description' => 'Auction for approval test',
            'category' => 'printing',
            'quantity' => 50,
            'budget' => 250000,
            'deadline' => now()->addDays(7),
            'status' => 'pending',
            'admin_approval_status' => 'pending',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin-approval@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'dev',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Test admin approval
        $auction->approve($admin->id, 'Test approval with admin fees');

        $this->info("✅ Auction approved by admin");
        $this->info("📊 New status: {$auction->status}");
        $this->info("📊 Approval status: {$auction->admin_approval_status}");

        // Test that auction is now visible
        $visibleAuctions = Auction::approved()->active()->count();
        $this->info("📊 Visible auctions after approval: {$visibleAuctions}");

        if ($visibleAuctions > 0) {
            $this->info("✅ Auction now visible to vendors");
        } else {
            $this->warn("⚠️ Auction visibility test - may need to refresh context");
        }

        // Cleanup
        $auction->delete();
        $user->delete();
        $admin->delete();
    }

    private function testVendorBidding()
    {
        $this->info('🏢 Step 3: Testing Vendor Bidding...');

        // Create test user and approved auction
        $user = User::create([
            'name' => 'Test User Bidding',
            'email' => 'user-bidding@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $auction = Auction::create([
            'user_id' => $user->id,
            'title' => 'Test Auction Bidding',
            'description' => 'Auction for bidding test',
            'category' => 'printing',
            'quantity' => 75,
            'budget' => 375000,
            'deadline' => now()->addDays(7),
            'status' => 'active',
            'admin_approval_status' => 'approved',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create test vendors
        $vendor1 = Vendor::create([
            'name' => 'Vendor 1 Bidding',
            'email' => 'vendor1-bidding@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Address 1',
            'bank_name' => 'Bank 1',
            'bank_account_number' => '1234567890',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendor2 = Vendor::create([
            'name' => 'Vendor 2 Bidding',
            'email' => 'vendor2-bidding@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Address 2',
            'bank_name' => 'Bank 2',
            'bank_account_number' => '0987654321',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create vendor users
        $vendorUser1 = User::create([
            'name' => 'Vendor User 1',
            'email' => 'vendoruser1@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'vendor',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendorUser2 = User::create([
            'name' => 'Vendor User 2',
            'email' => 'vendoruser2@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'vendor',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create vendor-user relationships
        $vendor1->vendorUser()->attach($vendorUser1->id);
        $vendor2->vendorUser()->attach($vendorUser2->id);

        // Test vendor bidding
        $bid1 = AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => $vendorUser1->id,
            'vendor_id' => $vendor1->id,
            'bid_amount' => 350000,
            'is_winning' => false,
        ]);

        $bid2 = AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => $vendorUser2->id,
            'vendor_id' => $vendor2->id,
            'bid_amount' => 320000,
            'is_winning' => true,
        ]);

        $this->info("✅ Vendor 1 bid: Rp " . number_format($bid1->bid_amount ?? 0));
        $this->info("✅ Vendor 2 bid: Rp " . number_format($bid2->bid_amount ?? 0));

        // Test bid isolation
        $tenantManager = app(TenantManager::class);
        $tenantManager->setVendor($vendor1);

        $vendor1Bids = AuctionBid::forCurrentVendor()->count();
        $this->info("📊 Vendor 1 can see {$vendor1Bids} bids");

        if ($vendor1Bids === 1) {
            $this->info("✅ Vendor bid isolation working");
        } else {
            $this->error("❌ Vendor bid isolation failed");
        }

        // Cleanup
        $bid1->delete();
        $bid2->delete();
        $auction->delete();
        $user->delete();
        $vendorUser1->delete();
        $vendorUser2->delete();
        $vendor1->delete();
        $vendor2->delete();
    }

    private function testPaymentProcess()
    {
        $this->info('💳 Step 4: Testing Payment Process...');

        // Create test data
        $user = User::create([
            'name' => 'Test User Payment',
            'email' => 'user-payment@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendor = Vendor::create([
            'name' => 'Vendor Payment',
            'email' => 'vendor-payment@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Address',
            'bank_name' => 'Bank',
            'bank_account_number' => '1234567890',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $auction = Auction::create([
            'user_id' => $user->id,
            'title' => 'Test Auction Payment',
            'description' => 'Auction for payment test',
            'category' => 'printing',
            'quantity' => 100,
            'budget' => 500000,
            'deadline' => now()->addDays(7),
            'status' => 'active',
            'admin_approval_status' => 'approved',
            'winner_vendor_id' => $vendor->id,
            'winning_bid' => 450000,
            'admin_fee_amount' => 45000,
            'payment_gateway_fee' => 6750,
            'total_amount_with_fees' => 501750,
            'vendor_receives' => 450000,
            'admin_receives' => 51750,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Test payment creation
        $payment = XenditPayment::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'external_id' => 'test-payment-' . time(),
            'amount' => $auction->total_amount_with_fees,
            'status' => 'pending',
            'payment_method' => 'BANK_TRANSFER',
            'bank_code' => 'BCA',
            'account_number' => '1234567890',
            'expiry_date' => now()->addDays(1),
            'created' => now(),
            'updated' => now(),
        ]);

        $this->info("✅ Payment created: Rp " . number_format($payment->amount ?? 0));
        $this->info("📊 Payment status: {$payment->status}");

        // Test payment completion
        $payment->update(['status' => 'paid']);
        $auction->update(['status' => 'paid']);

        $this->info("✅ Payment completed");
        $this->info("📊 Auction status: {$auction->status}");

        // Cleanup
        $payment->delete();
        $auction->delete();
        $user->delete();
        $vendor->delete();
    }

    private function testDeliveryProcess()
    {
        $this->info('🚚 Step 5: Testing Delivery Process...');

        // Create test data
        $user = User::create([
            'name' => 'Test User Delivery',
            'email' => 'user-delivery@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendor = Vendor::create([
            'name' => 'Vendor Delivery',
            'email' => 'vendor-delivery@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Address',
            'bank_name' => 'Bank',
            'bank_account_number' => '1234567890',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $auction = Auction::create([
            'user_id' => $user->id,
            'title' => 'Test Auction Delivery',
            'description' => 'Auction for delivery test',
            'category' => 'printing',
            'quantity' => 50,
            'budget' => 250000,
            'deadline' => now()->addDays(7),
            'status' => 'paid',
            'admin_approval_status' => 'approved',
            'winner_vendor_id' => $vendor->id,
            'winning_bid' => 225000,
            'delivery_status' => 'pending',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Test shipping process
        $auction->markAsShipped('TRK123456789', 15000);
        $this->info("✅ Auction marked as shipped");
        $this->info("📊 Tracking number: {$auction->tracking_number}");
        $this->info("📊 Shipping cost: Rp " . number_format($auction->shipping_cost ?? 0));

        // Test delivery confirmation
        $deliveryConfirmation = DeliveryConfirmation::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'confirmation_code' => 'DEL' . time(),
            'customer_name' => 'Test Customer',
            'customer_phone' => '081234567890',
            'delivery_address' => 'Test Address',
            'status' => 'delivered'
        ]);

        $auction->markAsDelivered();
        $this->info("✅ Auction marked as delivered");

        // Cleanup
        $deliveryConfirmation->delete();
        $auction->delete();
        $user->delete();
        $vendor->delete();
    }

    private function testCompletionProcess()
    {
        $this->info('✅ Step 6: Testing Completion Process...');

        // Create test data
        $user = User::create([
            'name' => 'Test User Complete',
            'email' => 'user-complete@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $vendor = Vendor::create([
            'name' => 'Vendor Complete',
            'email' => 'vendor-complete@example.com',
            'phone' => '0812345678' . rand(10, 99),
            'address' => 'Address',
            'bank_name' => 'Bank',
            'bank_account_number' => '1234567890',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create vendor wallet
        $wallet = $vendor->getOrCreateWallet();
        $initialBalance = $wallet->balance;

        $auction = Auction::create([
            'user_id' => $user->id,
            'title' => 'Test Auction Complete',
            'description' => 'Auction for completion test',
            'category' => 'printing',
            'quantity' => 25,
            'budget' => 125000,
            'deadline' => now()->addDays(7),
            'status' => 'delivered',
            'admin_approval_status' => 'approved',
            'winner_vendor_id' => $vendor->id,
            'winning_bid' => 112500,
            'delivery_status' => 'delivered',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Test completion with rating
        $auction->complete(5, 'Excellent service and quality!');

        $this->info("✅ Auction completed");
        $this->info("📊 User rating: {$auction->user_rating} stars");
        $this->info("📊 User feedback: {$auction->user_feedback}");

        // Test vendor wallet credit
        $wallet->refresh();
        $finalBalance = $wallet->balance;
        $creditAmount = $finalBalance - $initialBalance;

        $this->info("📊 Vendor wallet balance: Rp " . number_format($finalBalance ?? 0));
        $this->info("📊 Credit amount: Rp " . number_format($creditAmount ?? 0));

        if ($creditAmount == $auction->winning_bid) {
            $this->info("✅ Vendor received correct payment");
        } else {
            $this->error("❌ Vendor payment incorrect");
        }

        // Test vendor rating
        $rating = VendorRating::create([
            'vendor_id' => $vendor->id,
            'user_id' => $user->id,
            'auction_id' => $auction->id,
            'rating' => $auction->user_rating,
            'comment' => $auction->user_feedback,
            'is_verified' => true,
        ]);

        $this->info("✅ Vendor rating created");

        // Cleanup
        $rating->delete();
        $auction->delete();
        $user->delete();
        $vendor->delete();
    }

    // New methods for complete flow testing
    private function testAuctionCreationWithData($user)
    {
        $this->info('📝 Step 1: Testing Auction Creation...');

        // Set user tenant context
        $tenantManager = app(TenantManager::class);
        $tenantManager->setUser($user);

        // Create auction
        $auction = Auction::create([
            'user_id' => $user->id,
            'title' => 'Test Auction Complete Flow',
            'description' => 'Complete auction flow test',
            'category' => 'printing',
            'quantity' => 100,
            'budget' => 500000,
            'deadline' => now()->addDays(7),
            'status' => 'pending',
            'admin_approval_status' => 'pending',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        $this->info("✅ Auction created: {$auction->title}");
        $this->info("📊 Status: {$auction->status}");
        $this->info("📊 Admin Approval: {$auction->admin_approval_status}");

        return $auction;
    }

    private function testAdminApprovalWithData($user, $admin)
    {
        $this->info('👨‍💻 Step 2: Testing Admin Approval...');

        $auction = Auction::where('user_id', $user->id)->first();
        $auction->approve($admin->id, 'Test approval with admin fees');

        $this->info("✅ Auction approved by admin");
        $this->info("📊 New status: {$auction->status}");
        $this->info("📊 Approval status: {$auction->admin_approval_status}");

        return $auction;
    }

    private function testVendorBiddingWithData($user, $vendor, $vendorUser)
    {
        $this->info('🏢 Step 3: Testing Vendor Bidding...');

        $auction = Auction::where('user_id', $user->id)->first();

        // Set vendor tenant context
        $tenantManager = app(TenantManager::class);
        $tenantManager->setVendor($vendor);

        // Create bid
        $bid = AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => $vendorUser->id,
            'vendor_id' => $vendor->id,
            'bid_amount' => 450000,
            'is_winning' => true,
        ]);

        $this->info("✅ Vendor bid: Rp " . number_format($bid->bid_amount ?? 0));

        // Test bid isolation
        $vendorBids = AuctionBid::forCurrentVendor()->count();
        $this->info("📊 Vendor can see {$vendorBids} bids");

        if ($vendorBids === 1) {
            $this->info("✅ Vendor bid isolation working");
        } else {
            $this->error("❌ Vendor bid isolation failed");
        }

        return $bid;
    }

    private function testPaymentProcessWithData($user, $vendor)
    {
        $this->info('💳 Step 4: Testing Payment Process...');

        $auction = Auction::where('user_id', $user->id)->first();
        $auction->update([
            'winner_vendor_id' => $vendor->id,
            'winning_bid' => 450000,
            'admin_fee_amount' => 45000,
            'payment_gateway_fee' => 6750,
            'total_amount_with_fees' => 501750,
            'vendor_receives' => 450000,
            'admin_receives' => 51750,
        ]);

        // Test payment creation
        $payment = XenditPayment::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'external_id' => 'test-payment-' . time(),
            'amount' => $auction->total_amount_with_fees,
            'status' => 'pending',
            'payment_method' => 'BANK_TRANSFER',
            'bank_code' => 'BCA',
            'account_number' => '1234567890',
            'expiry_date' => now()->addDays(1),
            'created' => now(),
            'updated' => now(),
        ]);

        $this->info("✅ Payment created: Rp " . number_format($payment->amount ?? 0));
        $this->info("📊 Payment status: {$payment->status}");

        // Test payment completion
        $payment->update(['status' => 'paid']);
        $auction->update(['status' => 'paid']);

        $this->info("✅ Payment completed");
        $this->info("📊 Auction status: {$auction->status}");

        return $payment;
    }

    private function testDeliveryProcessWithData($user, $vendor)
    {
        $this->info('🚚 Step 5: Testing Delivery Process...');

        $auction = Auction::where('user_id', $user->id)->first();

        // Test shipping process
        $auction->markAsShipped('TRK123456789', 15000);
        $this->info("✅ Auction marked as shipped");
        $this->info("📊 Tracking number: {$auction->tracking_number}");
        $this->info("📊 Shipping cost: Rp " . number_format($auction->shipping_cost ?? 0));

        // Test delivery confirmation
        $deliveryConfirmation = DeliveryConfirmation::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'confirmation_code' => 'DEL' . time(),
            'customer_name' => 'Test Customer',
            'customer_phone' => '081234567890',
            'delivery_address' => 'Test Address',
            'status' => 'delivered'
        ]);

        $auction->markAsDelivered();
        $this->info("✅ Auction marked as delivered");

        return $deliveryConfirmation;
    }

    private function testCompletionProcessWithData($user, $vendor)
    {
        $this->info('✅ Step 6: Testing Completion Process...');

        $auction = Auction::where('user_id', $user->id)->first();

        // Create vendor wallet
        $wallet = $vendor->getOrCreateWallet();
        $initialBalance = $wallet->balance;

        // Test completion with rating
        $auction->complete(5, 'Excellent service and quality!');

        $this->info("✅ Auction completed");
        $this->info("📊 User rating: {$auction->user_rating} stars");
        $this->info("📊 User feedback: {$auction->user_feedback}");

        // Test vendor wallet credit
        $wallet->refresh();
        $finalBalance = $wallet->balance;
        $creditAmount = $finalBalance - $initialBalance;

        $this->info("📊 Vendor wallet balance: Rp " . number_format($finalBalance ?? 0));
        $this->info("📊 Credit amount: Rp " . number_format($creditAmount ?? 0));

        if ($creditAmount == $auction->winning_bid) {
            $this->info("✅ Vendor received correct payment");
        } else {
            $this->error("❌ Vendor payment incorrect");
        }

        // Test vendor rating
        $rating = VendorRating::create([
            'vendor_id' => $vendor->id,
            'user_id' => $user->id,
            'auction_id' => $auction->id,
            'rating' => $auction->user_rating,
            'comment' => $auction->user_feedback,
            'is_verified' => true,
        ]);

        $this->info("✅ Vendor rating created");

        return $rating;
    }
}

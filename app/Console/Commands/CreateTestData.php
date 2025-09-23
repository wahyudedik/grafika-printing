<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\XenditPayment;
use App\Models\DeliveryConfirmation;
use App\Models\ShippingInvoice;
use App\Models\VendorRating;
use App\Models\FinancialAuditLog;
use App\Models\VendorWalletTransaction;
use App\Models\VendorWithdrawal;
use App\Models\AdminFeeTransaction;
use App\Models\CmsSetting;

class CreateTestData extends Command
{
    protected $signature = 'create:test-data {--force : Force creation without confirmation}';
    protected $description = 'Creates proper test data for application testing.';

    public function handle()
    {
        $this->info('🧪 Creating test data...');

        if (!$this->option('force')) {
            if (!$this->confirm('This will create test data for application testing. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'usertype' => 'user',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create test vendor
        $vendor = Vendor::create([
            'name' => 'Test Vendor',
            'email' => 'vendor@example.com',
            'phone' => '08123456789',
            'address' => 'Test Address',
            'bank_name' => 'Test Bank',
            'bank_account_number' => '1234567890',
            'is_verified' => true,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create vendor-user relationship
        $vendor->vendorUser()->attach($user->id);

        // Create vendor wallet
        $wallet = VendorWallet::create([
            'vendor_id' => $vendor->id,
            'balance' => 100000,
            'is_frozen' => false,
        ]);

        // Create test auction
        $auction = Auction::create([
            'user_id' => $user->id,
            'title' => 'Test Auction',
            'description' => 'Test auction description',
            'category' => 'test',
            'quantity' => 1,
            'budget' => 50000,
            'deadline' => now()->addDays(7),
            'status' => 'active',
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Create test bid
        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'bid_amount' => 60000,
            'is_winning' => true,
        ]);

        // Create test payment
        XenditPayment::create([
            'auction_id' => $auction->id,
            'external_id' => 'test-payment-' . time(),
            'amount' => 60000,
            'status' => 'completed',
            'payment_method' => 'BANK_TRANSFER',
            'bank_code' => 'BCA',
            'account_number' => '1234567890',
            'expiry_date' => now()->addDays(1),
            'created' => now(),
            'updated' => now(),
        ]);

        // Create test delivery confirmation
        DeliveryConfirmation::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'status' => 'delivered',
            'notes' => 'Test delivery confirmation',
        ]);

        // Create test shipping invoice
        ShippingInvoice::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'kode' => 'SHIP-' . time(),
            'courier' => 'JNE',
            'service' => 'REG',
            'weight' => 1.0,
            'origin_city' => 'Jakarta',
            'destination_city' => 'Bandung',
            'origin_address' => 'Jl. Test Origin',
            'destination_address' => 'Jl. Test Destination',
            'shipping_cost' => 10000,
            'status' => 'paid',
        ]);

        // Create test rating
        VendorRating::create([
            'vendor_id' => $vendor->id,
            'user_id' => $user->id,
            'auction_id' => $auction->id,
            'rating' => 5,
            'comment' => 'Test rating',
            'is_verified' => true,
        ]);

        // Create test wallet transaction
        VendorWalletTransaction::create([
            'vendor_id' => $vendor->id,
            'vendor_wallet_id' => $wallet->id,
            'transaction_code' => 'TEST-' . time(),
            'type' => 'credit',
            'category' => 'test',
            'amount' => 10000,
            'balance_before' => 0,
            'balance_after' => 10000,
            'description' => 'Test credit',
            'status' => 'completed',
        ]);

        // Create test withdrawal
        VendorWithdrawal::create([
            'vendor_id' => $vendor->id,
            'vendor_wallet_id' => $wallet->id,
            'withdrawal_code' => 'WD-' . time(),
            'amount' => 5000,
            'net_amount' => 5000,
            'account_number' => '1234567890',
            'account_name' => 'Test Account',
            'bank_name' => 'Test Bank',
            'status' => 'pending',
        ]);

        // Create test admin fee transaction
        AdminFeeTransaction::create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'auction_id' => $auction->id,
            'transaction_code' => 'ADMIN-' . time(),
            'auction_amount' => 60000,
            'admin_fee_amount' => 1000,
            'total_amount' => 61000,
            'vendor_receives' => 59000,
            'admin_receives' => 1000,
            'amount' => 1000,
            'status' => 'done',
        ]);

        // Create test audit log
        FinancialAuditLog::create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'action_type' => 'test',
            'entity_type' => 'test',
            'entity_id' => 1,
            'amount' => 1000,
            'status' => 'success',
            'notes' => 'Test audit log',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
        ]);

        // Create test CMS settings
        CmsSetting::create([
            'key' => 'test_setting',
            'value' => 'test_value',
            'type' => 'string',
            'label' => 'Test Setting',
        ]);

        $this->info('✅ Test data created successfully!');
        $this->info('📊 Created:');
        $this->info('  - 1 User');
        $this->info('  - 1 Vendor');
        $this->info('  - 1 Vendor Wallet');
        $this->info('  - 1 Auction');
        $this->info('  - 1 Bid');
        $this->info('  - 1 Payment');
        $this->info('  - 1 Delivery Confirmation');
        $this->info('  - 1 Shipping Invoice');
        $this->info('  - 1 Rating');
        $this->info('  - 1 Wallet Transaction');
        $this->info('  - 1 Withdrawal');
        $this->info('  - 1 Admin Fee Transaction');
        $this->info('  - 1 Audit Log');
        $this->info('  - 1 CMS Setting');

        return 0;
    }
}

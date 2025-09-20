<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SeedComprehensiveData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:comprehensive {--fresh : Reset database before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed comprehensive dummy data for all features';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting comprehensive data seeding...');

        if ($this->option('fresh')) {
            $this->info('🔄 Resetting database...');
            Artisan::call('migrate:fresh');
        }

        $this->info('🌱 Seeding comprehensive dummy data...');
        
        // Run comprehensive seeder
        Artisan::call('db:seed', ['--class' => 'ComprehensiveDummyDataSeeder']);

        $this->info('✅ Comprehensive dummy data created successfully!');
        
        // Display summary
        $this->displaySummary();
    }

    private function displaySummary()
    {
        $this->info('');
        $this->info('📊 Summary:');
        $this->info('👥 Users: ' . \App\Models\User::count());
        $this->info('🏢 Vendors: ' . \App\Models\Vendor::count());
        $this->info('🎯 Auctions: ' . \App\Models\Auction::count());
        $this->info('💸 Auction Bids: ' . \App\Models\AuctionBid::count());
        $this->info('📊 Transactions: ' . \App\Models\Vendor\Transaksi::count());
        $this->info('💳 Xendit Payments: ' . \App\Models\XenditPayment::count());
        $this->info('💼 Wallet Transactions: ' . \App\Models\VendorWalletTransaction::count());
        $this->info('💸 Withdrawals: ' . \App\Models\VendorWithdrawal::count());
        $this->info('📦 Delivery Confirmations: ' . \App\Models\DeliveryConfirmation::count());
        $this->info('⭐ Vendor Ratings: ' . \App\Models\VendorRating::count());
        $this->info('🚚 Shipping Invoices: ' . \App\Models\ShippingInvoice::count());
        $this->info('💰 Admin Fee Transactions: ' . \App\Models\AdminFeeTransaction::count());
        $this->info('');
        $this->info('🎉 All dummy data created successfully!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding...');

        // Run comprehensive dummy data seeder
        $this->call([
            ComprehensiveDummyDataSeeder::class,  // All comprehensive dummy data
        ]);

        $this->command->info('✅ All dummy data created successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Users: ' . \App\Models\User::count());
        $this->command->info('   - Vendors: ' . \App\Models\Vendor::count());
        $this->command->info('   - Auctions: ' . \App\Models\Auction::count());
        $this->command->info('   - Transactions: ' . \App\Models\Vendor\Transaksi::count());
        $this->command->info('   - Withdrawals: ' . \App\Models\VendorWithdrawal::count());
        $this->command->info('   - Delivery Confirmations: ' . \App\Models\DeliveryConfirmation::count());
        $this->command->info('   - Admin Fee Transactions: ' . \App\Models\AdminFeeTransaction::count());
        $this->command->info('   - Xendit Payments: ' . \App\Models\XenditPayment::count());
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting comprehensive database seeding...');
        $this->command->newLine();

        // Core data seeding
        $this->command->info('👥 Seeding users and vendors...');
        $this->call(UserSeeder::class);

        $this->command->info('🏢 Seeding vendor data...');
        $this->call(VendorSeeder::class);

        $this->command->info('💰 Seeding admin fee settings...');
        $this->call(AdminFeeSeeder::class);

        $this->command->info('🎯 Seeding auction data...');
        $this->call(AuctionSeeder::class);

        $this->command->info('💳 Seeding payment data...');
        $this->call(PaymentSeeder::class);

        $this->command->info('🖨️ Seeding POS transaction data...');
        $this->call(POSSeeder::class);

        $this->command->info('📦 Seeding delivery tracking data...');
        $this->call(DeliverySeeder::class);

        $this->command->newLine();
        $this->command->info('✅ All seeders completed successfully!');
        $this->command->newLine();

        $this->displaySeedingSummary();
    }

    private function displaySeedingSummary()
    {
        $this->command->info('📊 SEEDING SUMMARY');
        $this->command->newLine();

        $this->command->info('✅ Users: Dev, Regular, and Vendor users created');
        $this->command->info('✅ Vendors: Complete vendor profiles with wallets');
        $this->command->info('✅ Products: Categories, specifications, materials, equipment');
        $this->command->info('✅ Customers: Vendor customer database');
        $this->command->info('✅ Auctions: With admin approval flow and bidding');
        $this->command->info('✅ Payments: Xendit integration with multiple methods');
        $this->command->info('✅ POS: Thermal printing transactions');
        $this->command->info('✅ Delivery: Order tracking and shipping');
        $this->command->info('✅ Ratings: Vendor rating system');
        $this->command->info('✅ Admin Fees: Comprehensive fee settings');

        $this->command->newLine();
        $this->command->info('🎉 Grafika Printing database is now fully seeded!');
        $this->command->info('💡 You can now test all features with realistic data.');
    }
}

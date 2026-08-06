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
        $this->command->info('🚀 Starting database seeding...');
        $this->command->newLine();

        // 1. Base users & vendor
        $this->command->info('👥 Step 1: Creating users & vendor...');
        $this->call(SimpleTestSeeder::class);

        // 2. CMS Settings
        $this->command->info('📄 Step 2: Creating CMS settings...');
        $this->call(CmsSettingsSeeder::class);

        // 3. Admin Fee Settings
        $this->command->info('💰 Step 3: Creating admin fee settings...');
        $this->call(AdminFeeSettingsSeeder::class);

        // 4. Vendor Wallets & Transactions
        $this->command->info('🏦 Step 4: Creating vendor wallets...');
        $this->call(VendorWalletSeeder::class);

        // 5. Linktree Data
        $this->command->info('🔗 Step 5: Creating linktree data...');
        $this->call(LinktreeSeeder::class);

        // 6. Lelang User Profiles
        $this->command->info('👤 Step 6: Creating lelang user profiles...');
        $this->call(LelangUserProfileSeeder::class);

        // 7. POS Data (Products, Materials, Equipment, etc.)
        $this->command->info('🏪 Step 7: Creating POS data (products, materials, equipment)...');
        $this->call(PosSeeder::class);

        $this->command->newLine();
        $this->command->info('✅ All seeding completed successfully!');
        $this->command->newLine();

        $this->displaySummary();
    }

    private function displaySummary()
    {
        $this->command->info('╔══════════════════════════════════════════════════════════╗');
        $this->command->info('║           SEEDING SUMMARY                               ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->info('║ 👤 Users & Vendor                                      ║');
        $this->command->info('║    DEV: dev@grafika-printing.com (password)             ║');
        $this->command->info('║    USER: user@example.com (password)                    ║');
        $this->command->info('║    VENDOR: vendor@example.com (password)                ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 📄 CMS Settings: Site name, contact, social media       ║');
        $this->command->info('║ 💰 Admin Fees: Auction 5%, Payment Gateway 2.5%        ║');
        $this->command->info('║ 🏦 Wallet: Rp 1,250,000 balance + transactions          ║');
        $this->command->info('║ 🔗 Linktree: /l/grafika-printing (5 links, 4 socials)  ║');
        $this->command->info('║ 👤 Lelang Profiles: Active user profiles                ║');
        $this->command->info('║ 🏪 POS: 6 categories, 10 products, 12 materials        ║');
        $this->command->info('║    6 equipment, 8 customers, 6 wholesale prices         ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->info('║ 🚀 Ready for testing!                                   ║');
        $this->command->info('╚══════════════════════════════════════════════════════════╝');
    }
}

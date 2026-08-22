<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan seeder yang benar (berdasarkan dependencies):
     *
     * 1. ProductionSeeder    — Admin, vendor, customer users, admin fees, CMS settings, kategori, bahan, alat
     * 2. PosCompleteSeeder   — Produk lengkap, spesifikasi, estimasi, wholesale, pelanggan, printer settings
     *                          (bergantung pada vendor dari ProductionSeeder)
     * 3. LinktreeSeeder      — Linktree vendor (bergantung pada vendor dari ProductionSeeder)
     * 4. LinktreeProductSeeder — Produk ke linktree (bergantung pada PosCompleteSeeder + LinktreeSeeder)
     * 5. SimpleTestSeeder    — Test users (dev, user, vendor dengan email berbeda)
     * 6. CmsSettingsSeeder   — CMS settings tambahan (overlap dengan ProductionSeeder, aman karena firstOrCreate)
     * 7. AdminFeeSettingsSeeder — Admin fee settings tambahan (overlap dengan ProductionSeeder)
     * 8. VendorWalletSeeder  — Wallet vendor & transaksi
     * 9. LelangUserProfileSeeder — Profil user lelang
     * 10. PosSeeder          — POS data (overlap dengan PosCompleteSeeder, aman karena firstOrCreate)
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding...');
        $this->command->newLine();

        // 1. Production-ready data (admin, vendor, customer, settings, kategori, bahan, alat)
        //    INI HARUS JALAN PERTAMA karena seeders lain bergantung pada data vendor & users
        $this->command->info('🏗️  Step 1: Creating production-ready data (users, vendor, settings, kategori, bahan, alat)...');
        $this->call(ProductionSeeder::class);

        // 2. POS Complete Data (produk lengkap, spesifikasi, estimasi, wholesale, pelanggan, printer)
        //    Bergantung pada vendor dari ProductionSeeder
        $this->command->info('🏪 Step 2: Creating complete POS data (products, specs, wholesale, customers)...');
        $this->call(PosCompleteSeeder::class);

        // 3. Linktree Data (linktree vendor, links, socials)
        //    Bergantung pada vendor dari ProductionSeeder
        $this->command->info('🔗 Step 3: Creating linktree data...');
        $this->call(LinktreeSeeder::class);

        // 4. Linktree Products (produk POS ke linktree)
        //    Bergantung pada PosCompleteSeeder (produk) + LinktreeSeeder (linktree)
        $this->command->info('🛒 Step 4: Creating linktree products...');
        $this->call(LinktreeProductSeeder::class);

        // 5. Simple Test Users (dev, user, vendor dengan email berbeda)
        $this->command->info('👥 Step 5: Creating simple test users...');
        $this->call(SimpleTestSeeder::class);

        // 6. CMS Settings tambahan (overlap dengan ProductionSeeder, aman karena firstOrCreate)
        $this->command->info('📄 Step 6: Creating CMS settings...');
        $this->call(CmsSettingsSeeder::class);

        // 7. Admin Fee Settings tambahan (overlap dengan ProductionSeeder, aman karena firstOrCreate)
        $this->command->info('💰 Step 7: Creating admin fee settings...');
        $this->call(AdminFeeSettingsSeeder::class);

        // 8. Vendor Wallets & Transactions
        $this->command->info('🏦 Step 8: Creating vendor wallets...');
        $this->call(VendorWalletSeeder::class);

        // 9. Lelang User Profiles
        $this->command->info('👤 Step 9: Creating lelang user profiles...');
        $this->call(LelangUserProfileSeeder::class);

        // 10. POS Data tambahan (overlap dengan PosCompleteSeeder, aman karena firstOrCreate)
        $this->command->info('🛒 Step 10: Creating additional POS data...');
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
        $this->command->info('║ 🏗️  Production: Admin, Vendor, Customer, Settings       ║');
        $this->command->info('║    10 kategori, 18 bahan, 6 alat production-ready       ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 🏪 POS Complete: Produk, spesifikasi, wholesale         ║');
        $this->command->info('║    10+ produk, 15 bahan, 20 wholesale prices            ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 🔗 Linktree: /l/grafika-printing (5 links, 4 socials)  ║');
        $this->command->info('║ 🛒 Linktree Products: Produk POS di linktree            ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 👤 Users & Vendor                                      ║');
        $this->command->info('║    DEV: dev@grafika-printing.com (password)             ║');
        $this->command->info('║    USER: customer@grafika-printing.com (password)       ║');
        $this->command->info('║    VENDOR: vendor@grafika-printing.com (password)       ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 📄 CMS Settings: Site name, contact, social media       ║');
        $this->command->info('║ 💰 Admin Fees: Auction 5%, Payment Gateway 2.5%        ║');
        $this->command->info('║ 🏦 Wallet: Rp 1,250,000 balance + transactions          ║');
        $this->command->info('║ 👤 Lelang Profiles: Active user profiles                ║');
        $this->command->info('║ 🛒 POS: 6 categories, 10 products, 12 materials        ║');
        $this->command->info('║    6 equipment, 8 customers, 6 wholesale prices         ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->info('║ 🚀 Ready for testing!                                   ║');
        $this->command->info('╚══════════════════════════════════════════════════════════╝');
    }
}

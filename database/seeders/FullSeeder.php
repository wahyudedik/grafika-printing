<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Auction;
use App\Models\AuctionBid;

class FullSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting full database seeding...');

        // Clear existing data
        $this->command->info('🧹 Clearing existing data...');
        AuctionBid::truncate();
        Auction::truncate();
        Vendor::where('id', '>', 1)->delete();
        User::where('id', '>', 3)->delete();

        // Run all seeders
        $this->call([
            UserSeeder::class,
            VendorSeeder::class,
            AuctionSeeder::class,
        ]);

        // Show final statistics
        $this->command->info('📊 Final Statistics:');
        $this->command->info('- Users: ' . User::count());
        $this->command->info('- Vendors: ' . Vendor::count());
        $this->command->info('- Auctions: ' . Auction::count());
        $this->command->info('- Bids: ' . AuctionBid::count());

        $this->command->info('✅ Full seeding completed successfully!');
    }
}

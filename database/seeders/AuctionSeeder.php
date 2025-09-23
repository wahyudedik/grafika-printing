<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;

class AuctionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎯 Creating auction data with admin approval flow...');

        $users = User::where('usertype', 'user')->get();
        $vendors = Vendor::all();

        foreach ($users as $user) {
            $this->createAuctionsForUser($user, $vendors);
        }

        $this->command->info('✅ Auction seeding completed successfully!');
    }

    private function createAuctionsForUser($user, $vendors)
    {
        $auctions = [
            [
                'title' => 'Printing Business Card 1000 pcs',
                'description' => 'Membutuhkan jasa printing kartu nama dengan kualitas tinggi untuk perusahaan',
                'category' => 'business_card',
                'quantity' => 1000,
                'budget' => 500000,
                'deadline' => now()->addDays(7),
                'status' => 'pending',
                'admin_approval_status' => 'pending',
            ],
            [
                'title' => 'Printing Flyer A4 5000 pcs',
                'description' => 'Printing flyer promosi untuk event dengan desain yang menarik',
                'category' => 'flyer',
                'quantity' => 5000,
                'budget' => 1500000,
                'deadline' => now()->addDays(10),
                'status' => 'pending',
                'admin_approval_status' => 'pending',
            ],
            [
                'title' => 'Printing Banner 3x1 meter',
                'description' => 'Printing banner untuk event outdoor dengan kualitas tahan cuaca',
                'category' => 'banner',
                'quantity' => 1,
                'budget' => 800000,
                'deadline' => now()->addDays(5),
                'status' => 'pending',
                'admin_approval_status' => 'pending',
            ],
            [
                'title' => 'Printing Sticker Custom 2000 pcs',
                'description' => 'Printing sticker dengan desain custom untuk produk',
                'category' => 'sticker',
                'quantity' => 2000,
                'budget' => 300000,
                'deadline' => now()->addDays(3),
                'status' => 'pending',
                'admin_approval_status' => 'pending',
            ],
            [
                'title' => 'Printing Packaging Box 500 pcs',
                'description' => 'Printing kemasan produk dengan desain menarik',
                'category' => 'packaging',
                'quantity' => 500,
                'budget' => 1200000,
                'deadline' => now()->addDays(14),
                'status' => 'pending',
                'admin_approval_status' => 'pending',
            ]
        ];

        foreach ($auctions as $auctionData) {
            $auction = Auction::create([
                'user_id' => $user->id,
                'title' => $auctionData['title'],
                'description' => $auctionData['description'],
                'category' => $auctionData['category'],
                'quantity' => $auctionData['quantity'],
                'budget' => $auctionData['budget'],
                'deadline' => $auctionData['deadline'],
                'status' => $auctionData['status'],
                'admin_approval_status' => $auctionData['admin_approval_status'],
                'uuid' => Str::uuid()->toString(),
            ]);

            // Create bids for some auctions
            $this->createBidsForAuction($auction, $vendors);
        }
    }

    private function createBidsForAuction($auction, $vendors)
    {
        // Randomly select 2-4 vendors to bid
        $biddingVendors = $vendors->random(rand(2, 4));

        foreach ($biddingVendors as $vendor) {
            $bidAmount = $auction->budget - rand(50000, 200000); // Bid lower than budget

            AuctionBid::create([
                'auction_id' => $auction->id,
                'vendor_id' => $vendor->id,
                'bid_amount' => $bidAmount,
                'status' => 'pending',
                'uuid' => Str::uuid()->toString(),
            ]);
        }
    }
}

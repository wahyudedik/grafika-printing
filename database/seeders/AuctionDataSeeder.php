<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;

class AuctionDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🎯 Creating auction data...');

        // Get users and vendors
        $users = User::where('usertype', 'user')->get();
        $vendors = Vendor::all();

        if ($users->count() == 0 || $vendors->count() == 0) {
            $this->command->warn('❌ No users or vendors found. Please run BasicDummyDataSeeder first.');
            return;
        }

        // Create auctions
        $auctionTitles = [
            'Cetak Banner Spanduk 3x1 meter',
            'Cetak Kartu Nama 1000 lembar',
            'Cetak Brosur A4 5000 lembar',
            'Cetak Sticker Vinyl 100 lembar',
            'Cetak Poster A3 200 lembar',
            'Cetak Undangan Pernikahan 300 lembar',
            'Cetak Buku Tahunan Sekolah 500 eksemplar',
            'Cetak Kalender Dinding 1000 lembar',
            'Cetak Label Produk 2000 lembar',
            'Cetak Flyer A5 10000 lembar',
            'Cetak Banner Roll Up 80x200 cm',
            'Cetak Kartu Undangan 500 lembar',
            'Cetak Sertifikat 200 lembar',
            'Cetak Menu Restoran 1000 lembar',
            'Cetak Pamflet A4 3000 lembar'
        ];

        $categories = [
            'Banner & Spanduk',
            'Kartu Nama',
            'Brosur & Pamflet',
            'Sticker & Label',
            'Poster & Banner',
            'Undangan',
            'Buku & Katalog',
            'Kalender',
            'Sertifikat',
            'Menu & Brosur'
        ];

        $statuses = ['active', 'closed', 'paid', 'completed', 'pending', 'pending', 'pending', 'rejected']; // More pending for moderation
        $auctions = [];

        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $title = $auctionTitles[array_rand($auctionTitles)];
            $category = $categories[array_rand($categories)];
            $status = $statuses[array_rand($statuses)];

            // Calculate dates
            $createdAt = Carbon::now()->subDays(rand(1, 30));
            $deadline = $createdAt->copy()->addDays(rand(3, 14));

            // Calculate admin fees
            $budget = rand(50000, 2000000);
            $adminFeeService = app(\App\Services\AdminFeeService::class);
            $fees = $adminFeeService->calculateTotalFees($budget, 'bank_transfer');

            $auction = Auction::create([
                'user_id' => $user->id,
                'title' => $title,
                'description' => 'Deskripsi lengkap untuk ' . $title . '. Spesifikasi detail akan diberikan setelah deal.',
                'specifications' => 'Spesifikasi teknis:\n- Ukuran: Sesuai permintaan\n- Material: Kualitas terbaik\n- Warna: Full color\n- Finishing: Sesuai kebutuhan',
                'category' => $category,
                'quantity' => rand(100, 5000),
                'budget' => $budget,
                'deadline' => $deadline,
                'status' => $status,
                'kode' => 'AUCTION-' . date('Ymd') . '-' . strtoupper(substr(md5($title . $i), 0, 5)),
                'alamat_pengiriman' => 'Jl. ' . ['Merdeka', 'Sudirman', 'Thamrin', 'Gatot Subroto', 'Menteng'][array_rand(['Merdeka', 'Sudirman', 'Thamrin', 'Gatot Subroto', 'Menteng'])] . ' No. ' . rand(1, 999),
                'no_telp' => '08' . rand(100000000, 999999999),
                'email_pengiriman' => $user->email,
                'catatan_khusus' => 'Catatan khusus: ' . ['Urgent', 'Quality priority', 'Budget friendly', 'Fast delivery'][array_rand(['Urgent', 'Quality priority', 'Budget friendly', 'Fast delivery'])],
                'metode_pembayaran' => 'auction_win',
                'progress_percentage' => $status === 'completed' ? 100 : rand(0, 90),
                'pos_integrated' => $status === 'paid' || $status === 'completed',
                'admin_fee_amount' => $fees['admin_fee'],
                'payment_gateway_fee' => $fees['payment_gateway_fee'],
                'total_amount_with_fees' => $fees['total_amount'],
                'vendor_receives' => $fees['vendor_receives'],
                'admin_receives' => $fees['admin_receives'],
                'fee_breakdown' => json_encode($fees['admin_fee_breakdown']),
                'fees_calculated' => true,
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ]);

            $auctions[] = $auction;

            // Create bids for active/closed auctions
            if (in_array($status, ['active', 'closed', 'paid', 'completed'])) {
                $bidCount = rand(1, min(3, $vendors->count())); // Max 3 bids (jumlah vendor)
                $bidVendors = $vendors->random($bidCount);

                foreach ($bidVendors as $index => $vendor) {
                    $bidAmount = $budget * (0.7 + (rand(0, 30) / 100)); // 70-100% of budget
                    $bidStatus = 'pending';

                    // For closed/paid/completed auctions, one bid should be accepted
                    if (in_array($status, ['closed', 'paid', 'completed']) && $index === 0) {
                        $bidStatus = 'accepted';
                        $auction->update([
                            'winner_vendor_id' => $vendor->id,
                            'winning_bid' => $bidAmount
                        ]);
                    }

                    AuctionBid::create([
                        'auction_id' => $auction->id,
                        'vendor_id' => $vendor->id,
                        'bid_amount' => $bidAmount,
                        'status' => $bidStatus,
                        'created_at' => $createdAt->copy()->addHours(rand(1, 24)),
                        'updated_at' => $createdAt->copy()->addHours(rand(1, 24))
                    ]);
                }
            }
        }

        $this->command->info('✅ Created ' . count($auctions) . ' auctions with bids');

        // Create some completed auctions with delivery confirmations
        $completedAuctions = Auction::where('status', 'completed')->take(3)->get();
        foreach ($completedAuctions as $auction) {
            if ($auction->winner_vendor_id) {
                \App\Models\DeliveryConfirmation::create([
                    'auction_id' => $auction->id,
                    'user_id' => $auction->user_id,
                    'vendor_id' => $auction->winner_vendor_id,
                    'delivery_status' => 'confirmed',
                    'delivery_date' => $auction->updated_at,
                    'user_rating' => rand(4, 5),
                    'user_feedback' => 'Barang sesuai pesanan, kualitas bagus!',
                    'photos' => json_encode(['delivery_proof_1.jpg', 'delivery_proof_2.jpg']),
                    'confirmed_at' => $auction->updated_at
                ]);
            }
        }

        $this->command->info('✅ Created delivery confirmations for completed auctions');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auction;
use App\Models\User;
use App\Models\AuctionBid;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users and vendors
        $users = User::where('usertype', 'user')->get();
        $vendors = Vendor::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        if ($vendors->isEmpty()) {
            $this->command->warn('No vendors found. Please run VendorSeeder first.');
            return;
        }

        $this->command->info('Creating 100 auction requests...');

        // Categories for printing services
        $categories = [
            'Banner & Spanduk',
            'Stiker & Label',
            'Kartu Nama',
            'Brosur & Flyer',
            'Poster & Banner',
            'Undangan',
            'Buku & Katalog',
            'Kemasan & Packaging',
            'Bendera & Umbul-umbul',
            'Baju & Merchandise',
            'Banner Digital',
            'Stiker Kaca',
            'Kartu Undangan',
            'Brosur Promosi',
            'Poster Event'
        ];

        // Sample specifications
        $specifications = [
            'Ukuran: A4, Material: Art Paper 150gsm, Finishing: Laminating Glossy',
            'Ukuran: 50x70cm, Material: Flexi, Finishing: Eyelet',
            'Ukuran: 9x5cm, Material: Ivory 260gsm, Finishing: Spot UV',
            'Ukuran: A5, Material: HVS 80gsm, Finishing: Staples',
            'Ukuran: 100x200cm, Material: Vinyl, Finishing: Grommet',
            'Ukuran: 15x10cm, Material: Ivory 300gsm, Finishing: Emboss',
            'Ukuran: A3, Material: Art Carton 210gsm, Finishing: Laminating Doff',
            'Ukuran: Custom, Material: Kraft, Finishing: Die Cut',
            'Ukuran: 60x90cm, Material: Polyester, Finishing: Tali',
            'Ukuran: S-M-L, Material: Cotton Combed, Finishing: Sablon'
        ];

        // Sample addresses
        $addresses = [
            'Jl. Sudirman No. 123, Jakarta Pusat',
            'Jl. Thamrin No. 456, Jakarta Selatan',
            'Jl. Gatot Subroto No. 789, Jakarta Barat',
            'Jl. HR Rasuna Said No. 321, Jakarta Timur',
            'Jl. MH Thamrin No. 654, Jakarta Utara',
            'Jl. Kebon Jeruk No. 987, Jakarta Barat',
            'Jl. Senayan No. 147, Jakarta Selatan',
            'Jl. Kuningan No. 258, Jakarta Selatan',
            'Jl. Menteng No. 369, Jakarta Pusat',
            'Jl. Cikini No. 741, Jakarta Pusat'
        ];

        // Sample phone numbers
        $phoneNumbers = [
            '08123456789',
            '08134567890',
            '08145678901',
            '08156789012',
            '08167890123',
            '08178901234',
            '08189012345',
            '08190123456',
            '08201234567',
            '08212345678'
        ];

        // Sample email addresses
        $emailAddresses = [
            'user1@example.com',
            'user2@example.com',
            'user3@example.com',
            'user4@example.com',
            'user5@example.com',
            'user6@example.com',
            'user7@example.com',
            'user8@example.com',
            'user9@example.com',
            'user10@example.com'
        ];

        // Sample titles and descriptions
        $titles = [
            'Cetak Banner Promosi Event',
            'Brosur Produk Baru',
            'Kartu Nama Perusahaan',
            'Stiker Label Produk',
            'Poster Acara Musik',
            'Bendera Event',
            'Kemasan Produk Makanan',
            'Undangan Pernikahan',
            'Buku Katalog Produk',
            'Banner Digital LED',
            'Stiker Kaca Gedung',
            'Flyer Promosi Diskon',
            'Poster Film',
            'Banner Toko',
            'Kartu Member VIP',
            'Label Kemasan Kosmetik',
            'Brosur Properti',
            'Poster Konser',
            'Banner Pameran',
            'Stiker Mobil'
        ];

        $descriptions = [
            'Membutuhkan jasa cetak untuk keperluan promosi event yang akan diselenggarakan',
            'Perlu cetak brosur untuk produk terbaru yang akan diluncurkan',
            'Membutuhkan kartu nama dengan desain profesional untuk perusahaan',
            'Cetak stiker label untuk produk yang akan dipasarkan',
            'Poster untuk acara musik yang akan diadakan di venue',
            'Bendera untuk event yang akan diselenggarakan',
            'Kemasan produk makanan yang menarik dan informatif',
            'Undangan pernikahan dengan desain elegan',
            'Buku katalog produk untuk promosi',
            'Banner digital untuk display LED',
            'Stiker kaca untuk gedung perkantoran',
            'Flyer promosi diskon untuk toko',
            'Poster film untuk promosi',
            'Banner toko untuk menarik pelanggan',
            'Kartu member VIP dengan desain eksklusif',
            'Label kemasan kosmetik yang menarik',
            'Brosur properti untuk penjualan',
            'Poster konser musik',
            'Banner pameran seni',
            'Stiker mobil untuk promosi'
        ];

        // Create 100 auctions
        for ($i = 1; $i <= 100; $i++) {
            $user = $users->random();
            $category = $categories[array_rand($categories)];
            $title = $titles[array_rand($titles)] . ' #' . $i;
            $description = $descriptions[array_rand($descriptions)];
            $specification = $specifications[array_rand($specifications)];

            // Random budget between 50,000 to 5,000,000
            $budget = rand(50000, 5000000);

            // Random quantity between 1 to 1000
            $quantity = rand(1, 1000);

            // Random deadline between 1 to 30 days from now
            $deadline = Carbon::now()->addDays(rand(1, 30));

            // Random status distribution
            $statusOptions = ['active', 'active', 'active', 'active', 'active', 'active', 'active', 'waiting_payment', 'paid', 'closed'];
            $status = $statusOptions[array_rand($statusOptions)];

            // Create auction
            $auction = Auction::create([
                'user_id' => $user->id,
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'quantity' => $quantity,
                'budget' => $budget,
                'deadline' => $deadline,
                'specifications' => $specification,
                'alamat_pengiriman' => $addresses[array_rand($addresses)],
                'no_telp' => $phoneNumbers[array_rand($phoneNumbers)],
                'email_pengiriman' => $emailAddresses[array_rand($emailAddresses)],
                'catatan_khusus' => 'Mohon diperhatikan kualitas cetakan dan ketepatan waktu pengiriman.',
                'kode' => 'AUCTION-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'status' => $status,
                'metode_pembayaran' => 'auction_win',
                'progress_percentage' => 0,
                'pos_integrated' => false,
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30))
            ]);

            // Create bids for active auctions
            if ($status === 'active') {
                $bidCount = rand(1, 5); // 1-5 bids per auction

                for ($j = 0; $j < $bidCount; $j++) {
                    $vendor = $vendors->random();
                    $bidAmount = $budget - rand(10000, 100000); // Bid lower than budget

                    $bid = AuctionBid::create([
                        'auction_id' => $auction->id,
                        'vendor_id' => $vendor->id,
                        'bid_amount' => $bidAmount,
                        'status' => 'pending',
                        'message' => 'Penawaran terbaik dengan kualitas premium',
                        'created_at' => Carbon::now()->subDays(rand(0, 29)),
                        'updated_at' => Carbon::now()->subDays(rand(0, 29))
                    ]);
                }
            }

            // For waiting_payment and paid auctions, select a winner
            if (in_array($status, ['waiting_payment', 'paid'])) {
                $bids = $auction->bids;
                if ($bids->count() > 0) {
                    $winnerBid = $bids->random();
                    $winnerBid->update(['status' => 'accepted']);

                    $auction->update([
                        'winner_vendor_id' => $winnerBid->vendor_id,
                        'winning_bid' => $winnerBid->bid_amount
                    ]);
                }
            }

            // Show progress
            if ($i % 10 == 0) {
                $this->command->info("Created {$i} auctions...");
            }
        }

        $this->command->info('✅ Successfully created 100 auction requests!');
        $this->command->info('📊 Statistics:');
        $this->command->info('- Active auctions: ' . Auction::where('status', 'active')->count());
        $this->command->info('- Waiting payment: ' . Auction::where('status', 'waiting_payment')->count());
        $this->command->info('- Paid auctions: ' . Auction::where('status', 'paid')->count());
        $this->command->info('- Closed auctions: ' . Auction::where('status', 'closed')->count());
        $this->command->info('- Total bids: ' . AuctionBid::count());
    }
}

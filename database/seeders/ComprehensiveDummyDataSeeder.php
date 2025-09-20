<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\VendorWithdrawal;
use App\Models\XenditPayment;
use App\Models\DeliveryConfirmation;
use App\Models\AdminFeeTransaction;
use App\Models\VendorRating;
use App\Models\ShippingInvoice;
use App\Models\AdminFeeSetting;
use App\Services\AdminFeeService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComprehensiveDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->command->info('🚀 Starting comprehensive dummy data creation...');

            // Create basic data first
            $this->createUsers();
            $this->createVendors();
            $this->createAdminFeeSettings();
            $this->createVendorWallets();

            // Create auction data
            $this->createAuctions();
            $this->createAuctionBids();

            // Create transaction data
            $this->createTransactions();
            $this->createXenditPayments();

            // Create wallet and withdrawal data
            $this->createWalletTransactions();
            $this->createWithdrawals();

            // Create delivery and rating data
            $this->createDeliveryConfirmations();
            $this->createVendorRatings();
            $this->createShippingInvoices();

            // Create admin fee transactions
            $this->createAdminFeeTransactions();

            // Create POS data
            $this->createPosData();

            DB::commit();

            $this->command->info('✅ All dummy data created successfully!');
            $this->displaySummary();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating comprehensive dummy data: ' . $e->getMessage());
        }
    }

    private function createUsers()
    {
        $this->command->info('👥 Creating users...');

        // Create dev user
        User::firstOrCreate(
            ['email' => 'admin@grafika.com'],
            [
                'name' => 'Admin Grafika',
                'email' => 'admin@grafika.com',
                'password' => Hash::make('password'),
                'usertype' => 'dev',
                'email_verified_at' => now(),
                'last_login_at' => now()->subDays(rand(1, 30))
            ]
        );

        // Create vendor users
        $vendorUsers = [
            ['name' => 'Ahmad Print Shop', 'email' => 'ahmad@printshop.com'],
            ['name' => 'Budi Digital Printing', 'email' => 'budi@digitalprint.com'],
            ['name' => 'Citra Offset Printing', 'email' => 'citra@offset.com'],
            ['name' => 'Diana Print Solutions', 'email' => 'diana@printsolutions.com'],
            ['name' => 'Eko Fast Print', 'email' => 'eko@fastprint.com']
        ];

        foreach ($vendorUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'usertype' => 'vendor',
                    'email_verified_at' => now(),
                    'last_login_at' => now()->subDays(rand(1, 15))
                ]
            );
        }

        // Create regular users
        $regularUsers = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['name' => 'Bob Johnson', 'email' => 'bob@example.com'],
            ['name' => 'Alice Brown', 'email' => 'alice@example.com'],
            ['name' => 'Charlie Wilson', 'email' => 'charlie@example.com'],
            ['name' => 'Diana Lee', 'email' => 'diana@example.com'],
            ['name' => 'Eva Garcia', 'email' => 'eva@example.com']
        ];

        foreach ($regularUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'usertype' => 'user',
                    'email_verified_at' => now(),
                    'last_login_at' => now()->subDays(rand(1, 10))
                ]
            );
        }
    }

    private function createVendors()
    {
        $this->command->info('🏢 Creating vendors...');

        $vendors = [
            [
                'name' => 'Ahmad Print Shop',
                'email' => 'ahmad@printshop.com',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 123, Jakarta',
                'website' => 'https://ahmadprintshop.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => true,
                'auto_withdrawal_date' => 15,
                'auto_withdrawal_amount' => 1000000,
                'auto_withdrawal_method' => 'bank_transfer',
                'auto_withdrawal_account_number' => '1234567890',
                'auto_withdrawal_account_name' => 'Ahmad Print Shop',
                'auto_withdrawal_bank_name' => 'BCA'
            ],
            [
                'name' => 'Budi Digital Printing',
                'email' => 'budi@digitalprint.com',
                'phone' => '081234567891',
                'address' => 'Jl. Sudirman No. 456, Jakarta',
                'website' => 'https://budidigital.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => false
            ],
            [
                'name' => 'Citra Offset Printing',
                'email' => 'citra@offset.com',
                'phone' => '081234567892',
                'address' => 'Jl. Thamrin No. 789, Jakarta',
                'website' => 'https://citraoffset.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => true,
                'auto_withdrawal_date' => 1,
                'auto_withdrawal_amount' => 2000000,
                'auto_withdrawal_method' => 'bank_transfer',
                'auto_withdrawal_account_number' => '0987654321',
                'auto_withdrawal_account_name' => 'Citra Offset Printing',
                'auto_withdrawal_bank_name' => 'BNI'
            ],
            [
                'name' => 'Diana Print Solutions',
                'email' => 'diana@printsolutions.com',
                'phone' => '081234567893',
                'address' => 'Jl. Gatot Subroto No. 321, Jakarta',
                'website' => 'https://dianaprint.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => false
            ],
            [
                'name' => 'Eko Fast Print',
                'email' => 'eko@fastprint.com',
                'phone' => '081234567894',
                'address' => 'Jl. HR Rasuna Said No. 654, Jakarta',
                'website' => 'https://ekofastprint.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => true,
                'auto_withdrawal_date' => 30,
                'auto_withdrawal_amount' => 5000000,
                'auto_withdrawal_method' => 'e_wallet',
                'auto_withdrawal_account_number' => '081234567894',
                'auto_withdrawal_account_name' => 'Eko Fast Print',
                'auto_withdrawal_bank_name' => 'DANA'
            ]
        ];

        foreach ($vendors as $vendorData) {
            Vendor::firstOrCreate(
                ['email' => $vendorData['email']],
                $vendorData
            );
        }
    }

    private function createAdminFeeSettings()
    {
        $this->command->info('💰 Creating admin fee settings...');

        // Get the first dev user
        $devUser = User::where('usertype', 'dev')->first();
        $createdBy = $devUser ? $devUser->id : 1;

        $settings = [
            [
                'name' => 'Biaya Admin Aplikasi - 10%',
                'description' => 'Biaya admin aplikasi 10% untuk lelang normal',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_amount' => 10000,
                'maximum_amount' => 10000000,
                'category' => 'auction',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'effective_until' => null,
                'conditions' => json_encode(['min_amount' => 10000, 'max_amount' => 1000000]),
                'created_by' => $createdBy
            ],
            [
                'name' => 'Biaya Admin Aplikasi - 5%',
                'description' => 'Biaya admin aplikasi 5% untuk lelang besar',
                'type' => 'percentage',
                'value' => 5.00,
                'minimum_amount' => 1000000,
                'maximum_amount' => 50000000,
                'category' => 'auction',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'effective_until' => null,
                'conditions' => json_encode(['min_amount' => 1000000, 'max_amount' => 50000000]),
                'created_by' => $createdBy
            ],
            [
                'name' => 'Biaya Payment Gateway - Bank Transfer',
                'description' => 'Biaya payment gateway untuk bank transfer',
                'type' => 'percentage',
                'value' => 1.5,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'category' => 'payment',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'effective_until' => null,
                'conditions' => json_encode(['payment_method' => 'bank_transfer']),
                'created_by' => $createdBy
            ]
        ];

        foreach ($settings as $settingData) {
            AdminFeeSetting::firstOrCreate(
                ['name' => $settingData['name']],
                $settingData
            );
        }
    }

    private function createVendorWallets()
    {
        $this->command->info('💳 Creating vendor wallets...');

        $vendors = Vendor::all();

        foreach ($vendors as $vendor) {
            VendorWallet::firstOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'balance' => rand(500000, 10000000),
                    'total_earned' => rand(2000000, 50000000),
                    'total_withdrawn' => rand(1000000, 20000000)
                ]
            );
        }
    }

    private function createAuctions()
    {
        $this->command->info('🎯 Creating auctions...');

        $users = User::where('usertype', 'user')->get();
        $categories = ['Banner', 'Sticker', 'Brochure', 'Business Card', 'Poster', 'Flyer', 'Booklet', 'Calendar'];
        $statuses = ['pending', 'active', 'closed', 'waiting_payment', 'paid', 'completed', 'rejected'];

        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $category = $categories[array_rand($categories)];
            $status = $statuses[array_rand($statuses)];
            $budget = rand(50000, 5000000);

            // Calculate admin fees
            $adminFeeService = app(AdminFeeService::class);
            $fees = $adminFeeService->calculateTotalFees($budget, 'bank_transfer');

            Auction::create([
                'user_id' => $user->id,
                'title' => "Permintaan Cetak {$category} - " . ($i + 1),
                'description' => "Deskripsi lengkap untuk permintaan cetak {$category}. Spesifikasi detail dan kebutuhan khusus.",
                'category' => $category,
                'quantity' => rand(100, 5000),
                'budget' => $budget,
                'deadline' => now()->addDays(rand(1, 30)),
                'specifications' => "Spesifikasi teknis: Ukuran A4, Kertas Art Paper 150gsm, Finishing Laminating Glossy",
                'alamat_pengiriman' => "Jl. Contoh No. " . rand(1, 999) . ", Jakarta",
                'no_telp' => '08' . rand(100000000, 999999999),
                'email_pengiriman' => $user->email,
                'catatan_khusus' => 'Catatan khusus untuk produksi',
                'status' => $status,
                'kode' => 'AUCTION-' . date('Ymd') . '-' . strtoupper(substr(md5($i), 0, 5)),
                'admin_fee_amount' => $fees['admin_fee'],
                'payment_gateway_fee' => $fees['payment_gateway_fee'],
                'total_amount_with_fees' => $fees['total_amount'],
                'vendor_receives' => $fees['vendor_receives'],
                'admin_receives' => $fees['admin_receives'],
                'fee_breakdown' => json_encode($fees['admin_fee_breakdown']),
                'fees_calculated' => true,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 30))
            ]);
        }
    }

    private function createAuctionBids()
    {
        $this->command->info('💸 Creating auction bids...');

        $auctions = Auction::where('status', '!=', 'rejected')->get();
        $vendors = Vendor::all();

        foreach ($auctions as $auction) {
            $bidCount = rand(1, 5);
            $bids = [];

            for ($i = 0; $i < $bidCount; $i++) {
                $vendor = $vendors->random();
                $bidAmount = $auction->budget * (0.7 + (rand(0, 30) / 100)); // 70-100% of budget

                $status = 'pending';
                if ($auction->status === 'waiting_payment' || $auction->status === 'paid') {
                    $status = rand(0, 1) ? 'accepted' : 'rejected';
                }

                $bids[] = [
                    'auction_id' => $auction->id,
                    'vendor_id' => $vendor->id,
                    'bid_amount' => $bidAmount,
                    'status' => $status,
                    'message' => 'Penawaran terbaik dengan kualitas premium',
                    'created_at' => $auction->created_at->addHours(rand(1, 48)),
                    'updated_at' => now()
                ];
            }

            // Sort bids by amount (ascending)
            usort($bids, function ($a, $b) {
                return $a['bid_amount'] <=> $b['bid_amount'];
            });

            // Create bids
            foreach ($bids as $bidData) {
                AuctionBid::create($bidData);
            }

            // Set winner if auction is waiting_payment or paid
            if (in_array($auction->status, ['waiting_payment', 'paid', 'completed'])) {
                $winningBid = AuctionBid::where('auction_id', $auction->id)
                    ->where('status', 'accepted')
                    ->first();

                if ($winningBid) {
                    $auction->update([
                        'winner_vendor_id' => $winningBid->vendor_id,
                        'winning_bid' => $winningBid->bid_amount
                    ]);
                }
            }
        }
    }

    private function createTransactions()
    {
        $this->command->info('📊 Creating transactions...');

        $auctions = Auction::whereIn('status', ['paid', 'completed'])->get();
        $vendors = Vendor::all();

        foreach ($auctions as $auction) {
            if ($auction->winner_vendor_id) {
                $vendor = $vendors->find($auction->winner_vendor_id);

                if ($vendor) {
                    // Create customer first
                    $customer = \App\Models\Vendor\Pelanggan::firstOrCreate(
                        [
                            'vendor_id' => $vendor->id,
                            'email' => $auction->user->email
                        ],
                        [
                            'vendor_id' => $vendor->id,
                            'kode' => 'CUST-' . rand(1000, 9999),
                            'nama' => $auction->user->name,
                            'email' => $auction->user->email,
                            'no_telp' => $auction->no_telp ?? '081234567890',
                            'alamat' => $auction->alamat_pengiriman ?? 'Alamat tidak tersedia'
                        ]
                    );

                    // Create transaction in vendor's POS system
                    $transaksi = \App\Models\Vendor\Transaksi::create([
                        'vendor_id' => $vendor->id,
                        'user_id' => $auction->user_id,
                        'pelanggan_id' => $customer->id,
                        'kode' => 'TRX-' . date('Ymd') . '-' . rand(1000, 9999),
                        'total_harga' => $auction->winning_bid,
                        'status' => $auction->status === 'completed' ? 'completed' : 'processing',
                        'payment_method' => 'auction_win',
                        'estimasi_selesai' => now()->addDays(rand(3, 14)),
                        'tanggal_dibuat' => now()->toDateString(),
                        'progress_percentage' => $auction->status === 'completed' ? 100 : rand(20, 80),
                        'auction_id' => $auction->id,
                        'created_at' => $auction->created_at->addDays(rand(1, 5)),
                        'updated_at' => now()
                    ]);

                    // Update auction with transaction ID
                    $auction->update(['transaksi_id' => $transaksi->id]);
                }
            }
        }
    }

    private function createXenditPayments()
    {
        $this->command->info('💳 Creating Xendit payments...');

        $auctions = Auction::whereIn('status', ['waiting_payment', 'paid', 'completed'])->get();
        $paymentStatuses = ['pending', 'paid', 'failed', 'expired'];
        $paymentMethods = ['BCA', 'BNI', 'BRI', 'MANDIRI', 'OVO', 'DANA', 'SHOPEEPAY'];

        foreach ($auctions as $auction) {
            $status = $paymentStatuses[array_rand($paymentStatuses)];
            $method = $paymentMethods[array_rand($paymentMethods)];

            XenditPayment::create([
                'external_id' => 'auction_' . $auction->id . '_' . time(),
                'xendit_id' => 'xendit_' . rand(100000, 999999),
                'type' => 'payment_link',
                'amount' => $auction->total_amount_with_fees ?? $auction->winning_bid,
                'description' => 'Pembayaran Lelang: ' . $auction->title,
                'status' => $status,
                'customer' => json_encode([
                    'given_names' => $auction->user->name,
                    'email' => $auction->user->email
                ]),
                'items' => json_encode([
                    [
                        'name' => $auction->title,
                        'quantity' => $auction->quantity,
                        'price' => $auction->winning_bid,
                        'category' => 'Printing Service'
                    ]
                ]),
                'checkout_url' => 'https://checkout.xendit.co/web/' . rand(100000, 999999),
                'success_redirect_url' => route('user.auctions.show', $auction) . '?payment=success',
                'failure_redirect_url' => route('user.auctions.show', $auction) . '?payment=failed',
                'expires_at' => now()->addHours(24),
                'auction_id' => $auction->id,
                'created_at' => $auction->created_at->addDays(rand(1, 3)),
                'updated_at' => now()
            ]);
        }
    }

    private function createWalletTransactions()
    {
        $this->command->info('💼 Creating wallet transactions...');

        $wallets = VendorWallet::all();
        $categories = ['auction_payment', 'withdrawal', 'refund', 'bonus'];
        $types = ['credit', 'debit'];

        foreach ($wallets as $wallet) {
            $transactionCount = rand(10, 30);
            $currentBalance = $wallet->balance;

            for ($i = 0; $i < $transactionCount; $i++) {
                $type = $types[array_rand($types)];
                $category = $categories[array_rand($categories)];
                $amount = rand(100000, 5000000);

                $balanceBefore = $currentBalance;
                if ($type === 'credit') {
                    $currentBalance += $amount;
                } else {
                    $currentBalance -= $amount;
                }
                $balanceAfter = $currentBalance;

                VendorWalletTransaction::create([
                    'vendor_wallet_id' => $wallet->id,
                    'vendor_id' => $wallet->vendor_id,
                    'transaction_code' => 'TXN-' . date('Ymd') . '-' . rand(100000, 999999),
                    'type' => $type,
                    'amount' => $amount,
                    'category' => $category,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'description' => $this->getTransactionDescription($category, $type),
                    'reference_id' => rand(1000, 9999),
                    'reference_type' => 'auction',
                    'status' => 'completed',
                    'metadata' => json_encode(['source' => 'auction_system']),
                    'created_at' => now()->subDays(rand(1, 90)),
                    'updated_at' => now()
                ]);
            }
        }
    }

    private function createWithdrawals()
    {
        $this->command->info('💸 Creating withdrawals...');

        $vendors = Vendor::all();
        $statuses = ['pending', 'approved', 'processing', 'completed', 'rejected'];
        $methods = ['bank_transfer', 'e_wallet'];

        foreach ($vendors as $vendor) {
            $withdrawalCount = rand(2, 8);

            for ($i = 0; $i < $withdrawalCount; $i++) {
                $status = $statuses[array_rand($statuses)];
                $method = $methods[array_rand($methods)];
                $amount = rand(500000, 5000000);

                VendorWithdrawal::create([
                    'vendor_id' => $vendor->id,
                    'vendor_wallet_id' => $vendor->wallet->id,
                    'withdrawal_code' => 'WD-' . date('Ymd') . '-' . rand(100000, 999999),
                    'amount' => $amount,
                    'fee' => $method === 'bank_transfer' ? $amount * 0.01 : $amount * 0.02,
                    'net_amount' => $method === 'bank_transfer' ? $amount * 0.99 : $amount * 0.98,
                    'method' => $method,
                    'account_number' => $method === 'bank_transfer' ? '1234567890' : '081234567890',
                    'account_name' => $vendor->name,
                    'bank_name' => $method === 'bank_transfer' ? 'BCA' : 'DANA',
                    'status' => $status,
                    'notes' => 'Penarikan saldo dari lelang',
                    'admin_notes' => $status === 'rejected' ? 'Dokumen tidak lengkap' : null,
                    'processed_by' => $status !== 'pending' ? 1 : null,
                    'processed_at' => $status !== 'pending' ? now()->subDays(rand(1, 30)) : null,
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now()
                ]);
            }
        }
    }

    private function createDeliveryConfirmations()
    {
        $this->command->info('📦 Creating delivery confirmations...');

        // Get auctions with winner vendors (both completed and paid status)
        $auctions = Auction::whereIn('status', ['completed', 'paid'])
            ->whereNotNull('winner_vendor_id')
            ->get();

        $deliveryStatuses = ['pending', 'delivered', 'confirmed', 'disputed', 'resolved'];
        $deliveryConfirmations = [];

        foreach ($auctions as $auction) {
            $status = $deliveryStatuses[array_rand($deliveryStatuses)];

            $deliveryConfirmations[] = [
                'auction_id' => $auction->id,
                'user_id' => $auction->user_id,
                'vendor_id' => $auction->winner_vendor_id,
                'delivery_status' => $status,
                'delivery_date' => now()->subDays(rand(1, 30)),
                'confirmed_at' => $status === 'confirmed' ? now()->subDays(rand(1, 15)) : null,
                'user_rating' => $status === 'confirmed' ? rand(3, 5) : null,
                'user_feedback' => $status === 'confirmed' ? 'Pelayanan sangat memuaskan, hasil cetak berkualitas tinggi.' : null,
                'dispute_reason' => $status === 'disputed' ? 'Barang tidak sesuai spesifikasi' : null,
                'created_at' => $auction->created_at->addDays(rand(5, 15)),
                'updated_at' => now()
            ];
        }

        // Create additional delivery confirmations for variety
        for ($i = 0; $i < 20; $i++) {
            $auction = Auction::inRandomOrder()->first();
            if ($auction && $auction->winner_vendor_id) {
                $status = $deliveryStatuses[array_rand($deliveryStatuses)];

                $deliveryConfirmations[] = [
                    'auction_id' => $auction->id,
                    'user_id' => $auction->user_id,
                    'vendor_id' => $auction->winner_vendor_id,
                    'delivery_status' => $status,
                    'delivery_date' => now()->subDays(rand(1, 30)),
                    'confirmed_at' => $status === 'confirmed' ? now()->subDays(rand(1, 15)) : null,
                    'user_rating' => $status === 'confirmed' ? rand(3, 5) : null,
                    'user_feedback' => $status === 'confirmed' ? 'Pelayanan sangat memuaskan, hasil cetak berkualitas tinggi.' : null,
                    'dispute_reason' => $status === 'disputed' ? 'Barang tidak sesuai spesifikasi' : null,
                    'created_at' => $auction->created_at->addDays(rand(5, 15)),
                    'updated_at' => now()
                ];
            }
        }

        // Bulk insert delivery confirmations
        if (!empty($deliveryConfirmations)) {
            DeliveryConfirmation::insert($deliveryConfirmations);
            $this->command->info('✅ Created ' . count($deliveryConfirmations) . ' delivery confirmations');
        }
    }

    private function createVendorRatings()
    {
        $this->command->info('⭐ Creating vendor ratings...');

        $auctions = Auction::where('status', 'completed')->get();

        foreach ($auctions as $auction) {
            if ($auction->winner_vendor_id && $auction->deliveryConfirmation) {
                $rating = rand(3, 5);

                VendorRating::create([
                    'vendor_id' => $auction->winner_vendor_id,
                    'user_id' => $auction->user_id,
                    'auction_id' => $auction->id,
                    'rating' => $rating,
                    'review' => $this->getRatingReview($rating),
                    'is_verified' => true,
                    'created_at' => $auction->deliveryConfirmation->confirmation_date,
                    'updated_at' => now()
                ]);
            }
        }
    }

    private function createShippingInvoices()
    {
        $this->command->info('🚚 Creating shipping invoices...');

        $auctions = Auction::whereIn('status', ['paid', 'completed'])->get();

        foreach ($auctions as $auction) {
            if ($auction->winner_vendor_id) {
                $shippingCost = rand(15000, 50000);
                $status = $auction->status === 'completed' ? 'delivered' : 'shipped';

                ShippingInvoice::create([
                    'kode' => 'SHIP-' . date('Ymd') . '-' . rand(1000, 9999),
                    'auction_id' => $auction->id,
                    'user_id' => $auction->user_id,
                    'vendor_id' => $auction->winner_vendor_id,
                    'courier' => 'JNE',
                    'service' => 'REG',
                    'waybill_number' => 'JNE' . rand(100000000, 999999999),
                    'weight' => rand(100, 2000), // 100g - 2kg
                    'shipping_cost' => $shippingCost,
                    'origin_city' => 'Jakarta',
                    'destination_city' => 'Jakarta',
                    'origin_address' => 'Jl. Vendor No. 123, Jakarta',
                    'destination_address' => $auction->alamat_pengiriman,
                    'shipping_status' => $status,
                    'payment_status' => 'paid',
                    'shipped_at' => $status === 'shipped' ? now()->subDays(rand(1, 10)) : null,
                    'delivered_at' => $status === 'delivered' ? now()->subDays(rand(1, 5)) : null,
                    'notes' => 'Pengiriman via JNE Regular',
                    'created_at' => $auction->created_at->addDays(rand(2, 7)),
                    'updated_at' => now()
                ]);
            }
        }
    }

    private function createAdminFeeTransactions()
    {
        $this->command->info('💰 Creating admin fee transactions...');

        $auctions = Auction::whereIn('status', ['paid', 'completed'])->get();

        foreach ($auctions as $auction) {
            if ($auction->winner_vendor_id && $auction->admin_fee_amount > 0) {
                AdminFeeTransaction::create([
                    'auction_id' => $auction->id,
                    'vendor_id' => $auction->winner_vendor_id,
                    'user_id' => $auction->user_id,
                    'transaction_code' => 'AFT-' . date('Ymd') . '-' . rand(100000, 999999),
                    'auction_amount' => $auction->winning_bid,
                    'admin_fee_amount' => $auction->admin_fee_amount,
                    'payment_gateway_fee' => $auction->payment_gateway_fee,
                    'total_amount' => $auction->total_amount_with_fees,
                    'vendor_receives' => $auction->vendor_receives,
                    'admin_receives' => $auction->admin_receives,
                    'status' => 'paid',
                    'payment_method' => 'bank_transfer',
                    'payment_reference' => 'PAY-' . rand(100000, 999999),
                    'paid_at' => $auction->created_at->addDays(rand(1, 5)),
                    'fee_breakdown' => $auction->fee_breakdown,
                    'created_at' => $auction->created_at->addDays(rand(1, 5)),
                    'updated_at' => now()
                ]);
            }
        }
    }

    private function getTransactionDescription($category, $type)
    {
        $descriptions = [
            'auction_win' => 'Pendapatan dari lelang yang dimenangkan',
            'withdrawal' => 'Penarikan saldo ke rekening bank',
            'refund' => 'Pengembalian dana',
            'bonus' => 'Bonus dari sistem'
        ];

        return $descriptions[$category] ?? 'Transaksi wallet';
    }

    private function getRatingReview($rating)
    {
        $reviews = [
            5 => 'Pelayanan sangat memuaskan, hasil cetak berkualitas tinggi dan sesuai ekspektasi.',
            4 => 'Pelayanan baik, hasil cetak berkualitas dan sesuai pesanan.',
            3 => 'Pelayanan cukup baik, hasil cetak sesuai pesanan dengan sedikit kekurangan.'
        ];

        return $reviews[$rating] ?? 'Pelayanan standar.';
    }

    private function displaySummary()
    {
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('👥 Users: ' . User::count());
        $this->command->info('🏢 Vendors: ' . Vendor::count());
        $this->command->info('🎯 Auctions: ' . Auction::count());
        $this->command->info('💸 Auction Bids: ' . AuctionBid::count());
        $this->command->info('📊 Transactions: ' . \App\Models\Vendor\Transaksi::count());
        $this->command->info('💳 Xendit Payments: ' . XenditPayment::count());
        $this->command->info('💼 Wallet Transactions: ' . VendorWalletTransaction::count());
        $this->command->info('💸 Withdrawals: ' . VendorWithdrawal::count());
        $this->command->info('📦 Delivery Confirmations: ' . DeliveryConfirmation::count());
        $this->command->info('⭐ Vendor Ratings: ' . VendorRating::count());
        $this->command->info('🚚 Shipping Invoices: ' . ShippingInvoice::count());
        $this->command->info('💰 Admin Fee Transactions: ' . AdminFeeTransaction::count());
        $this->command->info('');
        $this->command->info('🎉 All dummy data created successfully!');
    }

    private function createPosData()
    {
        $this->command->info('🛒 Creating POS data...');

        $vendors = Vendor::all();
        $users = User::where('usertype', 'user')->get();

        foreach ($vendors as $vendor) {
            // Set tenant context for vendor
            \App\Facades\Tenant::setVendorId($vendor->id);

            // Create customers for this vendor
            $this->createVendorCustomers($vendor);

            // Create products for this vendor
            $this->createVendorProducts($vendor);

            // Create categories for this vendor
            $this->createVendorCategories($vendor);

            // Create materials for this vendor
            $this->createVendorMaterials($vendor);

            // Create specifications for this vendor
            $this->createVendorSpecifications($vendor);

            // Create tools for this vendor
            $this->createVendorTools($vendor);

            // Create POS transactions for this vendor
            $this->createVendorPosTransactions($vendor, $users);
        }

        \App\Facades\Tenant::clearVendorContext();
        $this->command->info('✅ POS data created successfully!');
    }

    private function createVendorCustomers($vendor)
    {
        $customers = [];
        for ($i = 0; $i < 15; $i++) {
            $customers[] = [
                'vendor_id' => $vendor->id,
                'nama' => fake('id_ID')->name(),
                'no_telp' => fake('id_ID')->phoneNumber(),
                'alamat' => fake('id_ID')->address(),
                'email' => fake('id_ID')->unique()->safeEmail(),
                'kode' => 'CUST-' . $vendor->id . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()
            ];
        }
        \App\Models\Vendor\Pelanggan::insert($customers);
    }

    private function createVendorProducts($vendor)
    {
        $products = [];
        $categories = \App\Models\Vendor\KategoriProduk::where('vendor_id', $vendor->id)->get();

        if ($categories->isEmpty()) {
            // Create categories first
            $this->createVendorCategories($vendor);
            $categories = \App\Models\Vendor\KategoriProduk::where('vendor_id', $vendor->id)->get();
        }

        for ($i = 0; $i < 20; $i++) {
            $products[] = [
                'vendor_id' => $vendor->id,
                'kategori_id' => $categories->random()->id,
                'nama_produk' => fake('id_ID')->words(3, true),
                'deskripsi' => fake('id_ID')->sentence(),
                'gambar' => json_encode([fake()->imageUrl(400, 300, 'business')]),
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()
            ];
        }
        \App\Models\Vendor\Produk::insert($products);
    }

    private function createVendorCategories($vendor)
    {
        $categories = [
            ['nama_kategori' => 'Cetak Digital', 'slug' => 'cetak-digital'],
            ['nama_kategori' => 'Cetak Offset', 'slug' => 'cetak-offset'],
            ['nama_kategori' => 'Cetak Sablon', 'slug' => 'cetak-sablon'],
            ['nama_kategori' => 'Cetak Stiker', 'slug' => 'cetak-stiker'],
            ['nama_kategori' => 'Cetak Banner', 'slug' => 'cetak-banner']
        ];

        foreach ($categories as $category) {
            \App\Models\Vendor\KategoriProduk::create([
                'vendor_id' => $vendor->id,
                'nama_kategori' => $category['nama_kategori'],
                'slug' => $category['slug'],
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()
            ]);
        }
    }

    private function createVendorMaterials($vendor)
    {
        $materials = [];
        $materialNames = ['Kertas HVS', 'Kertas Art Paper', 'Kertas Ivory', 'Kertas Duplex', 'Tinta Hitam', 'Tinta Warna', 'Lem', 'Plastik Laminating'];

        for ($i = 0; $i < 8; $i++) {
            $materials[] = [
                'vendor_id' => $vendor->id,
                'nama_bahan' => $materialNames[$i],
                'hpp' => rand(1000, 50000),
                'stok' => rand(0, 500),
                'satuan' => fake()->randomElement(['kg', 'pcs', 'roll', 'lembar']),
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()
            ];
        }
        \App\Models\Vendor\Bahan::insert($materials);
    }

    private function createVendorSpecifications($vendor)
    {
        $specifications = [
            ['nama_spesifikasi' => 'Ukuran', 'tipe_input' => 'text', 'satuan' => 'cm'],
            ['nama_spesifikasi' => 'Warna', 'tipe_input' => 'select', 'satuan' => 'pilihan'],
            ['nama_spesifikasi' => 'Jumlah', 'tipe_input' => 'number', 'satuan' => 'pcs'],
            ['nama_spesifikasi' => 'Kualitas', 'tipe_input' => 'select', 'satuan' => 'grade']
        ];

        foreach ($specifications as $spec) {
            \App\Models\Vendor\Spesifikasi::create([
                'vendor_id' => $vendor->id,
                'nama_spesifikasi' => $spec['nama_spesifikasi'],
                'tipe_input' => $spec['tipe_input'],
                'satuan' => $spec['satuan'],
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()
            ]);
        }
    }

    private function createVendorTools($vendor)
    {
        $tools = [];
        $toolNames = ['Printer Digital', 'Mesin Offset', 'Mesin Sablon', 'Mesin Laminating', 'Mesin Potong'];
        $brands = ['Canon', 'HP', 'Epson', 'Brother', 'Xerox'];
        $models = ['Model A', 'Model B', 'Model C', 'Model D', 'Model E'];

        for ($i = 0; $i < 5; $i++) {
            $tools[] = [
                'vendor_id' => $vendor->id,
                'nama_alat' => $toolNames[$i],
                'merek' => $brands[$i],
                'model' => $models[$i],
                'spesifikasi_alat' => fake('id_ID')->sentence(),
                'status' => fake()->randomElement(['aktif', 'maintenance', 'rusak']),
                'tanggal_pembelian' => now()->subDays(rand(30, 365)),
                'kapasitas_cetak_per_jam' => rand(100, 1000),
                'tersedia' => fake()->boolean(80),
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()
            ];
        }
        \App\Models\Vendor\Alat::insert($tools);
    }

    private function createVendorPosTransactions($vendor, $users)
    {
        $customers = \App\Models\Vendor\Pelanggan::where('vendor_id', $vendor->id)->get();
        $products = \App\Models\Vendor\Produk::where('vendor_id', $vendor->id)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        $transactions = [];
        for ($i = 0; $i < 30; $i++) {
            $customer = $customers->random();
            $totalAmount = rand(50000, 500000); // Random total amount since we don't have product prices

            $transactions[] = [
                'vendor_id' => $vendor->id,
                'user_id' => $users->random()->id,
                'pelanggan_id' => $customer->id,
                'kode' => 'TXN-' . $vendor->id . '-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'total_harga' => $totalAmount,
                'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
                'payment_method' => fake()->randomElement(['cash', 'transfer', 'credit']),
                'estimasi_selesai' => now()->addDays(rand(1, 7)),
                'tanggal_dibuat' => now()->subDays(rand(1, 30)),
                'progress_percentage' => rand(0, 100),
                'catatan' => fake('id_ID')->sentence(),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()
            ];
        }

        \App\Models\Vendor\Transaksi::insert($transactions);
        $this->command->info("✅ Created 30 POS transactions for vendor: {$vendor->name}");
    }
}

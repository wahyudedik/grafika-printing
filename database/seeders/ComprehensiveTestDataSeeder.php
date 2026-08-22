<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Alat;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\Linktree;
use App\Models\Vendor\LinktreeLink;
use App\Models\Vendor\LinktreeSocial;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * ComprehensiveTestDataSeeder - Seeder data test lengkap untuk development
 *
 * Seeder ini membuat data test komprehensif:
 * - 3 vendor dengan produk, kategori, bahan, spesifikasi
 * - 10 transaksi dengan berbagai status
 * - 5 auction dengan berbagai status
 * - Wallet data untuk setiap vendor
 * - Sample linktree untuk vendor
 *
 * ⚠️  HANYA UNTUK DEVELOPMENT/TESTING, bukan production!
 *
 * Usage:
 *   php artisan db:seed --class=ComprehensiveTestDataSeeder
 */
class ComprehensiveTestDataSeeder extends Seeder
{
    /** @var int Counter untuk kode transaksi */
    protected int $transaksiCounter = 0;

    /** @var int Counter untuk kode pelanggan */
    protected int $pelangganCounter = 0;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧪 ComprehensiveTestDataSeeder: Memulai seeding data test...');
        $this->command->newLine();

        DB::beginTransaction();

        try {
            $vendors = $this->createVendors();
            $this->createKategoriForVendors($vendors);
            $this->createBahansForVendors($vendors);
            $this->createAlatsForVendors($vendors);
            $this->createProduksForVendors($vendors);
            $this->createSpesifikasiForVendors($vendors);
            $this->createPelanggansForVendors($vendors);
            $customers = $this->createCustomers();
            $this->createTransaksis($vendors, $customers);
            $this->createWallets($vendors);
            $this->createAuctions($customers, $vendors);
            $this->createLinktrees($vendors);

            DB::commit();

            $this->command->newLine();
            $this->command->info('✅ ComprehensiveTestDataSeeder: Semua data test berhasil dibuat!');
            $this->displaySummary($vendors, $customers);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ ComprehensiveTestDataSeeder: Gagal - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create 3 vendors dengan user
     */
    protected function createVendors(): array
    {
        $this->command->info('🏭 Creating 3 vendors...');

        $vendorData = [
            ['name' => 'Grafika Prima Press', 'email' => 'prima@grafika-test.com', 'phone' => '081111111111', 'address' => 'Jl. Printing Raya No. 10, Bandung'],
            ['name' => 'Cetak Kilat Digital', 'email' => 'kilat@grafika-test.com', 'phone' => '082222222222', 'address' => 'Jl. Cetak Mandiri No. 25, Surabaya'],
            ['name' => 'Offset Jaya Abadi', 'email' => 'jaya@grafika-test.com', 'phone' => '083333333333', 'address' => 'Jl. Industri No. 50, Semarang'],
        ];

        $vendors = [];
        foreach ($vendorData as $data) {
            $vendorUser = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'usertype' => 'vendor',
                    'email_verified_at' => now(),
                ]
            );

            $vendor = Vendor::withoutGlobalScope('active')->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'is_active' => true,
                ]
            );

            DB::table('vendor_user')->firstOrCreate(
                ['vendor_id' => $vendor->id, 'user_id' => $vendorUser->id],
                ['created_at' => now(), 'updated_at' => now()]
            );

            $vendors[] = $vendor;
            $this->command->info("   ✅ Vendor: {$vendor->name} (ID: {$vendor->id})");
        }

        return $vendors;
    }

    /**
     * Create kategori produk untuk setiap vendor
     */
    protected function createKategoriForVendors(array $vendors): void
    {
        $this->command->info('📂 Creating categories for vendors...');

        $kategoriList = ['Banner', 'Brosur', 'Kartu Nama', 'Stiker', 'Undangan', 'Poster', 'Amplop', 'Kop Surat', 'Dus/Kemasan', 'Label'];

        foreach ($vendors as $vendor) {
            foreach ($kategoriList as $nama) {
                \App\Models\Vendor\KategoriProduk::withoutGlobalScope('active')->firstOrCreate(
                    ['vendor_id' => $vendor->id, 'nama_kategori' => $nama],
                    ['slug' => Str::slug($nama)]
                );
            }
            $this->command->info("   ✅ 10 kategori untuk vendor: {$vendor->name}");
        }
    }

    /**
     * Create bahan untuk setiap vendor
     */
    protected function createBahansForVendors(array $vendors): void
    {
        $this->command->info('📦 Creating materials for vendors...');

        $bahanList = [
            ['nama_bahan' => 'Art Paper 150gsm', 'hpp' => 2500.00, 'satuan' => 'lembar', 'stok' => '500'],
            ['nama_bahan' => 'Art Paper 200gsm', 'hpp' => 3500.00, 'satuan' => 'lembar', 'stok' => '500'],
            ['nama_bahan' => 'Art Paper 230gsm', 'hpp' => 4000.00, 'satuan' => 'lembar', 'stok' => '500'],
            ['nama_bahan' => 'Art Paper 300gsm', 'hpp' => 5500.00, 'satuan' => 'lembar', 'stok' => '500'],
            ['nama_bahan' => 'Ivory 250gsm', 'hpp' => 6000.00, 'satuan' => 'lembar', 'stok' => '300'],
            ['nama_bahan' => 'HVS 70gsm', 'hpp' => 1000.00, 'satuan' => 'lembar', 'stok' => '2000'],
            ['nama_bahan' => 'HVS 80gsm', 'hpp' => 1200.00, 'satuan' => 'lembar', 'stok' => '2000'],
            ['nama_bahan' => 'Vinyl', 'hpp' => 15000.00, 'satuan' => 'meter', 'stok' => '100'],
            ['nama_bahan' => 'Canvas', 'hpp' => 20000.00, 'satuan' => 'meter', 'stok' => '50'],
            ['nama_bahan' => 'Photo Paper', 'hpp' => 5000.00, 'satuan' => 'lembar', 'stok' => '300'],
        ];

        foreach ($vendors as $vendor) {
            foreach ($bahanList as $bahan) {
                \App\Models\Vendor\Bahan::withoutGlobalScope('active')->firstOrCreate(
                    ['vendor_id' => $vendor->id, 'nama_bahan' => $bahan['nama_bahan']],
                    ['hpp' => $bahan['hpp'], 'satuan' => $bahan['satuan'], 'stok' => $bahan['stok']]
                );
            }
            $this->command->info("   ✅ 10 bahan untuk vendor: {$vendor->name}");
        }
    }

    /**
     * Create alat untuk setiap vendor
     */
    protected function createAlatsForVendors(array $vendors): void
    {
        $this->command->info('🔧 Creating tools for vendors...');

        $alatList = [
            ['nama_alat' => 'Mesin Cetak Offset', 'merek' => 'Heidelberg', 'model' => 'Speedmaster 52', 'spesifikasi_alat' => 'Cetak offset 4 warna, max A3', 'status' => 'aktif', 'kapasitas_cetak_per_jam' => 10000],
            ['nama_alat' => 'Mesin Cetak Digital', 'merek' => 'Canon', 'model' => 'imageRUNNER C3530', 'spesifikasi_alat' => 'Cetak digital warna, max A3', 'status' => 'aktif', 'kapasitas_cetak_per_jam' => 1800],
            ['nama_alat' => 'Mesin Potong', 'merek' => 'Guillotine', 'model' => 'EP 76', 'spesifikasi_alat' => 'Potong kertas, kapasitas 76 lembar', 'status' => 'aktif', 'kapasitas_cetak_per_jam' => 500],
            ['nama_alat' => 'Mesin Laminasi', 'merek' => 'Fellowes', 'model' => 'Lunar 35', 'spesifikasi_alat' => 'Laminasi panas/dingin, max A3', 'status' => 'aktif', 'kapasitas_cetak_per_jam' => 200],
            ['nama_alat' => 'Mesin Pond', 'merek' => 'Fiamo', 'model' => 'ECO 80', 'spesifikasi_alat' => 'Pond/kiss cut, area 80x50cm', 'status' => 'aktif', 'kapasitas_cetak_per_jam' => 150],
            ['nama_alat' => 'Mesin Bubut', 'merek' => 'CNC', 'model' => 'Mini Lathe 7x14', 'spesifikasi_alat' => 'Bubut mini untuk parts', 'status' => 'aktif', 'kapasitas_cetak_per_jam' => 50],
        ];

        foreach ($vendors as $vendor) {
            foreach ($alatList as $alat) {
                \App\Models\Vendor\Alat::withoutGlobalScope('active')->firstOrCreate(
                    ['vendor_id' => $vendor->id, 'nama_alat' => $alat['nama_alat']],
                    [
                        'merek' => $alat['merek'],
                        'model' => $alat['model'],
                        'spesifikasi_alat' => $alat['spesifikasi_alat'],
                        'status' => $alat['status'],
                        'tanggal_pembelian' => now()->subYear(),
                        'kapasitas_cetak_per_jam' => $alat['kapasitas_cetak_per_jam'],
                        'tersedia' => true,
                    ]
                );
            }
            $this->command->info("   ✅ 6 alat untuk vendor: {$vendor->name}");
        }
    }

    /**
     * Create produk untuk setiap vendor
     */
    protected function createProduksForVendors(array $vendors): void
    {
        $this->command->info('📦 Creating products for vendors...');

        foreach ($vendors as $vendor) {
            // Ambil kategori
            $kategoriBanner = \App\Models\Vendor\KategoriProduk::withoutGlobalScope('active')
                ->where('vendor_id', $vendor->id)->where('nama_kategori', 'Banner')->first();
            $kategoriBrosur = \App\Models\Vendor\KategoriProduk::withoutGlobalScope('active')
                ->where('vendor_id', $vendor->id)->where('nama_kategori', 'Brosur')->first();
            $kategoriKartuNama = \App\Models\Vendor\KategoriProduk::withoutGlobalScope('active')
                ->where('vendor_id', $vendor->id)->where('nama_kategori', 'Kartu Nama')->first();
            $kategoriStiker = \App\Models\Vendor\KategoriProduk::withoutGlobalScope('active')
                ->where('vendor_id', $vendor->id)->where('nama_kategori', 'Stiker')->first();
            $kategoriPoster = \App\Models\Vendor\KategoriProduk::withoutGlobalScope('active')
                ->where('vendor_id', $vendor->id)->where('nama_kategori', 'Poster')->first();

            $produkList = [
                ['nama_produk' => 'Banner Indoor 1m x 2m', 'deskripsi' => 'Banner indoor bahan vinyl, cetak full color, resolusi tinggi. Cocok untuk display indoor.', 'kategori_id' => $kategoriBanner->id ?? 1],
                ['nama_produk' => 'Banner Outdoor 1m x 3m', 'deskripsi' => 'Banner outdoor tahan cuaca, bahan flexi/outdoor. Sablon tahan air.', 'kategori_id' => $kategoriBanner->id ?? 1],
                ['nama_produk' => 'Brosur A4 Full Color', 'deskripsi' => 'Brosur ukuran A4, kertas Art Paper 150gsm, cetak 2 sisi full color.', 'kategori_id' => $kategoriBrosur->id ?? 2],
                ['nama_produk' => 'Brosur A5 Full Color', 'deskripsi' => 'Brosur ukuran A5, kertas Art Paper 200gsm, cetak 2 sisi full color.', 'kategori_id' => $kategoriBrosur->id ?? 2],
                ['nama_produk' => 'Kartu Nama Standard', 'deskripsi' => 'Kartu nama ukuran 9x5cm, kertas Art Paper 260gsm, laminasi doff/glossy.', 'kategori_id' => $kategoriKartuNama->id ?? 3],
                ['nama_produk' => 'Kartu Nama Premium', 'deskripsi' => 'Kartu nama ukuran 9x5cm, kertas Ivory 300gsm, emoss UV spot.', 'kategori_id' => $kategoriKartuNama->id ?? 3],
                ['nama_produk' => 'Stiker Vinyl Cutting', 'deskripsi' => 'Stiker vinyl potong custom, tahan air, untuk branding/outdoor.', 'kategori_id' => $kategoriStiker->id ?? 4],
                ['nama_produk' => 'Poster A3 Full Color', 'deskripsi' => 'Poster ukuran A3, kertas Photo Paper, cetak full color resolusi tinggi.', 'kategori_id' => $kategoriPoster->id ?? 5],
                ['nama_produk' => 'Banner Roll Up 85cm x 200cm', 'deskripsi' => 'Banner roll up dengan frame, bahan LG 280, portable.', 'kategori_id' => $kategoriBanner->id ?? 1],
                ['nama_produk' => 'Stiker Label A4', 'deskripsi' => 'Stiker label kertas HVS, cetak full color, potong sesuai bentuk.', 'kategori_id' => $kategoriStiker->id ?? 4],
            ];

            foreach ($produkList as $produk) {
                \App\Models\Vendor\Produk::withoutGlobalScope('active')->firstOrCreate(
                    ['vendor_id' => $vendor->id, 'nama_produk' => $produk['nama_produk']],
                    [
                        'deskripsi' => $produk['deskripsi'],
                        'kategori_id' => $produk['kategori_id'],
                    ]
                );
            }
            $this->command->info("   ✅ 10 produk untuk vendor: {$vendor->name}");
        }
    }

    /**
     * Create spesifikasi untuk setiap vendor
     */
    protected function createSpesifikasiForVendors(array $vendors): void
    {
        $this->command->info('⚙️  Creating specifications for vendors...');

        $spesifikasiList = [
            ['nama_spesifikasi' => 'Ukuran', 'tipe_input' => 'select', 'satuan' => 'cm'],
            ['nama_spesifikasi' => 'Jumlah', 'tipe_input' => 'number', 'satuan' => 'pcs'],
            ['nama_spesifikasi' => 'Bahan', 'tipe_input' => 'select', 'satuan' => 'lembar'],
            ['nama_spesifikasi' => 'Finishing', 'tipe_input' => 'select', 'satuan' => '-'],
            ['nama_spesifikasi' => 'Tinta', 'tipe_input' => 'select', 'satuan' => '-'],
        ];

        foreach ($vendors as $vendor) {
            foreach ($spesifikasiList as $spek) {
                \App\Models\Vendor\Spesifikasi::withoutGlobalScope('active')->firstOrCreate(
                    ['vendor_id' => $vendor->id, 'nama_spesifikasi' => $spek['nama_spesifikasi']],
                    ['tipe_input' => $spek['tipe_input'], 'satuan' => $spek['satuan']]
                );
            }
            $this->command->info("   ✅ 5 spesifikasi untuk vendor: {$vendor->name}");
        }
    }

    /**
     * Create pelanggan untuk setiap vendor
     */
    protected function createPelanggansForVendors(array $vendors): void
    {
        $this->command->info('👤 Creating customers for vendors...');

        $pelangganData = [
            ['kode' => 'PLG-001', 'nama' => 'Budi Santoso', 'alamat' => 'Jl. Merdeka No. 10', 'no_telp' => '081122334455', 'email' => 'budi@test.com'],
            ['kode' => 'PLG-002', 'nama' => 'Siti Rahayu', 'alamat' => 'Jl. Pahlawan No. 20', 'no_telp' => '082233445566', 'email' => 'siti@test.com'],
            ['kode' => 'PLG-003', 'nama' => 'Ahmad Fauzi', 'alamat' => 'Jl. Sudirman No. 30', 'no_telp' => '083344556677', 'email' => 'ahmad@test.com'],
            ['kode' => 'PLG-004', 'nama' => 'Dewi Lestari', 'alamat' => 'Jl. Thamrin No. 40', 'no_telp' => '084455667788', 'email' => 'dewi@test.com'],
            ['kode' => 'PLG-005', 'nama' => 'Rizky Pratama', 'alamat' => 'Jl. Gatot Subroto No. 50', 'no_telp' => '085566778899', 'email' => 'rizky@test.com'],
        ];

        foreach ($vendors as $vendor) {
            foreach ($pelangganData as $pelanggan) {
                Pelanggan::withoutGlobalScope('active')->firstOrCreate(
                    ['vendor_id' => $vendor->id, 'kode' => $pelanggan['kode']],
                    [
                        'nama' => $pelanggan['nama'],
                        'alamat' => $pelanggan['alamat'],
                        'no_telp' => $pelanggan['no_telp'],
                        'email' => $pelanggan['email'],
                    ]
                );
            }
            $this->command->info("   ✅ 5 pelanggan untuk vendor: {$vendor->name}");
        }
    }

    /**
     * Create customers (user type)
     */
    protected function createCustomers(): array
    {
        $this->command->info('👤 Creating test customers (user type)...');

        $customerData = [
            ['name' => 'Andi Wijaya', 'email' => 'andi@test.com'],
            ['name' => 'Maya Putri', 'email' => 'maya@test.com'],
            ['name' => 'Rendra Kusuma', 'email' => 'rendra@test.com'],
            ['name' => 'Lestari Ningrum', 'email' => 'lestari@test.com'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar@test.com'],
        ];

        $customers = [];
        foreach ($customerData as $data) {
            $customer = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'usertype' => 'user',
                    'email_verified_at' => now(),
                ]
            );
            $customers[] = $customer;
            $this->command->info("   ✅ Customer: {$customer->name}");
        }

        return $customers;
    }

    /**
     * Create 10 transaksi dengan berbagai status
     */
    protected function createTransaksis(array $vendors, array $customers): void
    {
        $this->command->info('💳 Creating 10 transactions with various statuses...');

        $statuses = ['pending', 'processing', 'quality_check', 'completed', 'completed', 'completed', 'cancelled', 'pending', 'processing', 'completed'];
        $paymentMethods = ['cash', 'transfer', 'xendit', 'cash', 'transfer', 'xendit', 'cash', 'transfer', 'cash', 'xendit'];

        for ($i = 0; $i < 10; $i++) {
            $vendor = $vendors[array_rand($vendors)];
            $customer = $customers[array_rand($customers)];
            $pelanggan = Pelanggan::withoutGlobalScope('active')->where('vendor_id', $vendor->id)->first();

            if (!$pelanggan) {
                continue;
            }

            $status = $statuses[$i];
            $totalHarga = rand(50000, 500000);
            $progressPercentage = match ($status) {
                'pending' => 0,
                'processing' => rand(20, 60),
                'quality_check' => rand(70, 90),
                'completed' => 100,
                'cancelled' => 0,
                default => 0,
            };

            $transaksi = Transaksi::withoutGlobalScope('active')->firstOrCreate(
                ['vendor_id' => $vendor->id, 'kode' => 'TRX-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $customer->id,
                    'pelanggan_id' => $pelanggan->id,
                    'total_harga' => $totalHarga,
                    'status' => $status,
                    'payment_method' => $paymentMethods[$i],
                    'estimasi_selesai' => now()->addDays(rand(1, 7)),
                    'tanggal_dibuat' => now()->subDays(rand(0, 14)),
                    'progress_percentage' => $progressPercentage,
                    'catatan' => "Test transaction #" . ($i + 1),
                ]
            );

            // Buat transaksi item
            $produk = \App\Models\Vendor\Produk::withoutGlobalScope('active')->where('vendor_id', $vendor->id)->first();
            if ($produk && !$transaksi->transaksiItem()->exists()) {
                TransaksiItem::withoutGlobalScope('active')->firstOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'transaksi_id' => $transaksi->id,
                        'produk_id' => $produk->id,
                    ],
                    [
                        'kuantitas' => rand(10, 100),
                        'harga_satuan' => 5000.00,
                    ]
                );
            }

            $this->command->info("   ✅ Transaksi {$transaksi->kode} - {$status} - Rp " . number_format($totalHarga, 0, ',', '.'));
        }
    }

    /**
     * Create wallet data untuk setiap vendor
     */
    protected function createWallets(array $vendors): void
    {
        $this->command->info('🏦 Creating wallets for vendors...');

        foreach ($vendors as $vendor) {
            $balance = rand(500000, 5000000);
            $totalEarned = $balance + rand(1000000, 10000000);

            $wallet = VendorWallet::withoutGlobalScope('active')->firstOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'balance' => $balance,
                    'pending_balance' => rand(100000, 1000000),
                    'total_earned' => $totalEarned,
                    'total_withdrawn' => $totalEarned - $balance,
                    'is_active' => true,
                ]
            );

            // Buat beberapa transaksi wallet
            $transactionCategories = ['auction_payment', 'bonus', 'adjustment'];
            for ($j = 0; $j < 3; $j++) {
                $amount = rand(100000, 1000000);
                $category = $transactionCategories[$j % count($transactionCategories)];

                VendorWalletTransaction::withoutGlobalScope('active')->firstOrCreate(
                    [
                        'vendor_wallet_id' => $wallet->id,
                        'transaction_code' => 'CREDIT-' . $vendor->id . '-' . str_pad($j + 1, 3, '0', STR_PAD_LEFT),
                    ],
                    [
                        'vendor_id' => $vendor->id,
                        'type' => 'credit',
                        'category' => $category,
                        'amount' => $amount,
                        'balance_before' => $balance - $amount,
                        'balance_after' => $balance,
                        'description' => ucfirst(str_replace('_', ' ', $category)) . ' untuk vendor ' . $vendor->name,
                        'status' => 'completed',
                    ]
                );
            }

            $this->command->info("   ✅ Wallet {$vendor->name}: Rp " . number_format($balance, 0, ',', '.') . ' balance');
        }
    }

    /**
     * Create 5 auction dengan berbagai status
     */
    protected function createAuctions(array $customers, array $vendors): void
    {
        $this->command->info('🔨 Creating 5 auctions with various statuses...');

        $auctionData = [
            [
                'title' => 'Cetak Banner Promosi Ulang Tahun',
                'description' => 'Dibutuhkan cetak banner promosi ulang tahun perusahaan, ukuran 1m x 2m, full color, bahan vinyl outdoor.',
                'category' => 'Banner',
                'quantity' => 5,
                'budget' => 500000,
                'status' => 'pending',
            ],
            [
                'title' => 'Brosur Produk Baru 2026',
                'description' => 'Cetak brosur produk baru A4, 2 sisi full color, Art Paper 150gsm, minimal 1000 lembar.',
                'category' => 'Brosur',
                'quantity' => 1000,
                'budget' => 2500000,
                'status' => 'active',
            ],
            [
                'title' => 'Kartu Nama Startup Tech',
                'description' => 'Cetak kartu nama startup, desain modern minimalis, kertas premium, laminasi glossy.',
                'category' => 'Kartu Nama',
                'quantity' => 500,
                'budget' => 750000,
                'status' => 'completed',
            ],
            [
                'title' => 'Stiker Label Produk Makanan',
                'description' => 'Cetak stiker label untuk kemasan produk makanan, waterproof, food grade.',
                'category' => 'Stiker',
                'quantity' => 2000,
                'budget' => 3000000,
                'status' => 'active',
            ],
            [
                'title' => 'Poster Event Konser',
                'description' => 'Cetak poster event konser ukuran A2, full color, bahan photo paper premium.',
                'category' => 'Poster',
                'quantity' => 50,
                'budget' => 500000,
                'status' => 'closed',
            ],
        ];

        foreach ($auctionData as $index => $data) {
            $customer = $customers[array_rand($customers)];

            $auction = Auction::withoutGlobalScope('active')->firstOrCreate(
                ['user_id' => $customer->id, 'title' => $data['title']],
                [
                    'kode' => 'LGL-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'description' => $data['description'],
                    'category' => $data['category'],
                    'quantity' => $data['quantity'],
                    'budget' => $data['budget'],
                    'deadline' => now()->addDays(rand(3, 14)),
                    'status' => $data['status'],
                ]
            );

            // Buat bids dari vendor
            $vendorForBid = $vendors[array_rand($vendors)];
            $bidAmount = $data['budget'] * (rand(70, 95) / 100);

            AuctionBid::withoutGlobalScope('active')->firstOrCreate(
                [
                    'auction_id' => $auction->id,
                    'vendor_id' => $vendorForBid->id,
                ],
                [
                    'bid_amount' => $bidAmount,
                    'message' => "Kami siap mengerjakan {$data['title']} dengan kualitas terbaik.",
                    'status' => $data['status'] === 'completed' ? 'accepted' : 'pending',
                ]
            );

            // Set winner jika completed
            if ($data['status'] === 'completed') {
                $auction->update([
                    'winner_vendor_id' => $vendorForBid->id,
                    'winning_bid' => $bidAmount,
                ]);
            }

            $this->command->info("   ✅ Auction: {$data['title']} [{$data['status']}]");
        }
    }

    /**
     * Create linktree untuk vendor pertama
     */
    protected function createLinktrees(array $vendors): void
    {
        $this->command->info('🔗 Creating linktree for first vendor...');

        $vendor = $vendors[0];

        $linktree = Linktree::withoutGlobalScope('active')->firstOrCreate(
            ['vendor_id' => $vendor->id, 'custom_url' => Str::slug($vendor->name)],
            [
                'title' => $vendor->name,
                'bio' => 'Solusi percetakan berkualitas tinggi. Kami melayani banner, brosur, kartu nama, dan semua kebutuhan cetak Anda.',
                'template' => 'minimal',
                'primary_color' => '#6366f1',
                'secondary_color' => '#ec4899',
                'bg_color' => '#ffffff',
                'text_color' => '#1f2937',
                'button_style' => 'rounded',
                'is_active' => true,
                'show_qris' => false,
            ]
        );

        // Buat links
        $links = [
            ['title' => '📦 Lihat Katalog Produk', 'url' => route('vendor.dashboard', [], false) ?? '#', 'type' => 'link', 'sort_order' => 1],
            ['title' => '💬 Chat via WhatsApp', 'url' => 'https://wa.me/6281234567890', 'type' => 'whatsapp', 'sort_order' => 2],
            ['title' => '📍 Kunjungi Toko', 'url' => 'https://maps.google.com/?q=' . urlencode($vendor->address), 'type' => 'link', 'sort_order' => 3],
            ['title' => '📞 Hubungi Kami', 'url' => 'tel:' . $vendor->phone, 'type' => 'phone', 'sort_order' => 4],
        ];

        foreach ($links as $link) {
            LinktreeLink::withoutGlobalScope('active')->firstOrCreate(
                ['linktree_id' => $linktree->id, 'title' => $link['title']],
                [
                    'vendor_id' => $vendor->id,
                    'url' => $link['url'],
                    'type' => $link['type'],
                    'is_active' => true,
                    'sort_order' => $link['sort_order'],
                ]
            );
        }
        $this->command->info("   ✅ Linktree: /l/{$linktree->custom_url} (4 links)");

        // Buat social links
        $socials = [
            ['platform' => 'instagram', 'url' => 'https://instagram.com/grafikaprinting', 'sort_order' => 1],
            ['platform' => 'facebook', 'url' => 'https://facebook.com/grafikaprinting', 'sort_order' => 2],
            ['platform' => 'whatsapp', 'url' => 'https://wa.me/6281234567890', 'sort_order' => 3],
        ];

        foreach ($socials as $social) {
            LinktreeSocial::withoutGlobalScope('active')->firstOrCreate(
                ['linktree_id' => $linktree->id, 'platform' => $social['platform']],
                [
                    'vendor_id' => $vendor->id,
                    'url' => $social['url'],
                    'is_active' => true,
                    'sort_order' => $social['sort_order'],
                ]
            );
        }
        $this->command->info("   ✅ Social links: 3 platforms (Instagram, Facebook, WhatsApp)");
    }

    /**
     * Display seeding summary
     */
    protected function displaySummary(array $vendors, array $customers): void
    {
        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════╗');
        $this->command->info('║        COMPREHENSIVE TEST DATA SUMMARY                  ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->info('║ 🏭 Vendors:  3 vendor dengan user & pivot              ║');
        $this->command->info('║ 📂 Kategori: 10 kategori x 3 vendor = 30 total         ║');
        $this->command->info('║ 📦 Bahan:    10 bahan x 3 vendor = 30 total            ║');
        $this->command->info('║ 🔧 Alat:     6 alat x 3 vendor = 18 total              ║');
        $this->command->info('║ 📦 Produk:   10 produk x 3 vendor = 30 total           ║');
        $this->command->info('║ ⚙️  Spek:     5 spesifikasi x 3 vendor = 15 total      ║');
        $this->command->info('║ 👤 Pelanggan: 5 pelanggan x 3 vendor = 15 total        ║');
        $this->command->info('║ 👥 Customers: 5 customers (user type)                   ║');
        $this->command->info('║ 💳 Transaksi: 10 transaksi (berbagai status)            ║');
        $this->command->info('║ 🏦 Wallet:   3 wallet + 9 transaksi wallet              ║');
        $this->command->info('║ 🔨 Auction:  5 auction + 5 bids (berbagai status)      ║');
        $this->command->info('║ 🔗 Linktree: 1 linktree + 4 links + 3 socials          ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');

        // Tampilkan vendor credentials
        foreach ($vendors as $index => $vendor) {
            $user = User::where('email', $vendor->email)->first();
            $this->command->info("║  Vendor " . ($index + 1) . ": {$user->email}             ║");
        }
        $this->command->info('║  Semua password: password                              ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->info('║ 🧪 Ready for testing!                                   ║');
        $this->command->info('╚══════════════════════════════════════════════════════════╝');
    }
}

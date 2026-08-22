<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\AdminFeeSetting;
use App\Models\CmsSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * ProductionSeeder - Seeder aman untuk fresh install production
 *
 * Seeder ini membuat data dasar yang diperlukan untuk operasi production:
 * - Admin user (usertype=dev)
 * - Sample vendor dengan user
 * - Sample customer (user type)
 * - Admin fee settings defaults
 * - CMS settings defaults
 * - Kategori produk untuk industri percetakan
 * - Bahan/bahan baku untuk percetakan
 * - Peralatan/peralatan cetak
 *
 * Gunakan `firstOrCreate` / `findOrCreate` untuk mencegah duplicate data.
 * Aman dijalankan berulang kali (idempotent).
 *
 * Usage:
 *   php artisan db:seed --class=ProductionSeeder
 *   php artisan db:seed --class=ProductionSeeder --force  (untuk production)
 */
class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏗️  ProductionSeeder: Memulai seeding data production...');
        $this->command->newLine();

        DB::beginTransaction();

        try {
            $this->createAdminUser();
            $this->createSampleVendor();
            $this->createSampleCustomer();
            $this->createAdminFeeSettings();
            $this->createCmsSettings();
            $this->createKategoriProduk();
            $this->createBahans();
            $this->createAlats();

            DB::commit();

            $this->command->newLine();
            $this->command->info('✅ ProductionSeeder: Semua data production berhasil dibuat!');
            $this->displaySummary();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ ProductionSeeder: Gagal - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 1. Create admin user (usertype=dev)
     */
    protected function createAdminUser(): void
    {
        $this->command->info('👤 Step 1: Creating admin user...');

        $admin = User::firstOrCreate(
            ['email' => 'admin@grafika-printing.com'],
            [
                'name' => 'Admin Grafika',
                'password' => Hash::make('password'),
                'usertype' => 'dev',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('   ✅ Admin: admin@grafika-printing.com (usertype: dev)');
    }

    /**
     * 2. Create sample vendor dengan user
     */
    protected function createSampleVendor(): void
    {
        $this->command->info('🏭 Step 2: Creating sample vendor...');

        // Buat vendor user
        $vendorUser = User::firstOrCreate(
            ['email' => 'vendor@grafika-printing.com'],
            [
                'name' => 'Toko Grafika Mitra',
                'password' => Hash::make('password'),
                'usertype' => 'vendor',
                'email_verified_at' => now(),
            ]
        );

        // Buat vendor
        $vendor = Vendor::withoutGlobalScope('active')->firstOrCreate(
            ['email' => 'vendor@grafika-printing.com'],
            [
                'name' => 'Toko Grafika Mitra',
                'phone' => '081234567890',
                'address' => 'Jl. printing No. 123, Jakarta Selatan, DKI Jakarta',
                'is_active' => true,
            ]
        );

        // Pivot vendor_user
        DB::table('vendor_user')->updateOrInsert(
            [
                'vendor_id' => $vendor->id,
                'user_id' => $vendorUser->id,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('   ✅ Vendor: ' . $vendor->name . ' (ID: ' . $vendor->id . ')');
        $this->command->info('   ✅ Vendor User: vendor@grafika-printing.com (usertype: vendor)');
    }

    /**
     * 3. Create sample customer (user type)
     */
    protected function createSampleCustomer(): void
    {
        $this->command->info('👤 Step 3: Creating sample customer...');

        $customer = User::firstOrCreate(
            ['email' => 'customer@grafika-printing.com'],
            [
                'name' => 'Customer Demo',
                'password' => Hash::make('password'),
                'usertype' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('   ✅ Customer: customer@grafika-printing.com (usertype: user)');
    }

    /**
     * 4. Create admin fee settings defaults
     */
    protected function createAdminFeeSettings(): void
    {
        $this->command->info('💰 Step 4: Creating admin fee settings...');

        $adminUser = User::where('email', 'admin@grafika-printing.com')->first();
        if (!$adminUser) {
            $this->command->warn('   ⚠️  Admin user not found, skipping admin fee settings');
            return;
        }

        $feeSettings = [
            [
                'name' => 'admin_fee_auction_percentage',
                'description' => 'Biaya admin lelang sebesar persentase dari total budget',
                'type' => 'percentage',
                'value' => 5.00,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'is_active' => true,
                'category' => 'auction',
            ],
            [
                'name' => 'admin_fee_auction_minimum',
                'description' => 'Minimum biaya admin lelang',
                'type' => 'fixed',
                'value' => 5000.00,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'is_active' => true,
                'category' => 'auction',
            ],
            [
                'name' => 'payment_gateway_fee',
                'description' => 'Biaya payment gateway (Xendit)',
                'type' => 'percentage',
                'value' => 2.50,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'is_active' => true,
                'category' => 'payment',
            ],
        ];

        foreach ($feeSettings as $setting) {
            AdminFeeSetting::firstOrCreate(
                ['name' => $setting['name']],
                array_merge($setting, [
                    'created_by' => $adminUser->id,
                    'updated_by' => $adminUser->id,
                ])
            );
            $this->command->info('   ✅ Fee: ' . $setting['name'] . ' = ' . $setting['value'] . ($setting['type'] === 'percentage' ? '%' : ''));
        }
    }

    /**
     * 5. Create CMS settings defaults
     */
    protected function createCmsSettings(): void
    {
        $this->command->info('📄 Step 5: Creating CMS settings...');

        $cmsSettings = [
            // General
            ['key' => 'site_name', 'value' => 'Grafika Printing', 'type' => 'text', 'category' => 'general', 'label' => 'Nama Situs', 'sort_order' => 1],
            ['key' => 'site_tagline', 'value' => 'Solusi Percetakan Digital Indonesia', 'type' => 'text', 'category' => 'general', 'label' => 'Tagline Situs', 'sort_order' => 2],
            ['key' => 'site_description', 'value' => 'Platform multi-vendor untuk kebutuhan percetakan Indonesia', 'type' => 'text', 'category' => 'general', 'label' => 'Deskripsi Situs', 'sort_order' => 3],

            // Contact
            ['key' => 'contact_email', 'value' => 'info@grafika-printing.com', 'type' => 'email', 'category' => 'contact', 'label' => 'Email Kontak', 'sort_order' => 1],
            ['key' => 'contact_phone', 'value' => '021-12345678', 'type' => 'phone', 'category' => 'contact', 'label' => 'Telepon Kontak', 'sort_order' => 2],
            ['key' => 'contact_address', 'value' => 'Jakarta Selatan, DKI Jakarta, Indonesia', 'type' => 'text', 'category' => 'contact', 'label' => 'Alamat', 'sort_order' => 3],

            // Social Media
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/grafikaprinting', 'type' => 'social', 'category' => 'social', 'label' => 'Instagram', 'sort_order' => 1],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/grafikaprinting', 'type' => 'social', 'category' => 'social', 'label' => 'Facebook', 'sort_order' => 2],
            ['key' => 'social_whatsapp', 'value' => '6281234567890', 'type' => 'social', 'category' => 'social', 'label' => 'WhatsApp', 'sort_order' => 3],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@grafikaprinting', 'type' => 'social', 'category' => 'social', 'label' => 'YouTube', 'sort_order' => 4],

            // Footer
            ['key' => 'footer_text', 'value' => '© 2026 Grafika Printing. All rights reserved.', 'type' => 'text', 'category' => 'footer', 'label' => 'Footer Text', 'sort_order' => 1],
        ];

        foreach ($cmsSettings as $setting) {
            CmsSetting::firstOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'description' => null,
                    'is_active' => true,
                ])
            );
            $this->command->info('   ✅ CMS: ' . $setting['key']);
        }
    }

    /**
     * 6. Create basic categories (kategori produk) untuk printing
     *
     * Kategori ini bersifat global, dibuat untuk vendor sample.
     */
    protected function createKategoriProduk(): void
    {
        $this->command->info('📂 Step 6: Creating product categories...');

        $vendor = Vendor::withoutGlobalScope('active')->where('email', 'vendor@grafika-printing.com')->first();
        if (!$vendor) {
            $this->command->warn('   ⚠️  Vendor not found, skipping categories');
            return;
        }

        $kategoriList = [
            'Banner',
            'Brosur',
            'Kartu Nama',
            'Stiker',
            'Undangan',
            'Poster',
            'Amplop',
            'Kop Surat',
            'Dus/Kemasan',
            'Label',
        ];

        foreach ($kategoriList as $index => $nama) {
            \App\Models\Vendor\KategoriProduk::withoutGlobalScope('active')->firstOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'nama_kategori' => $nama,
                ],
                [
                    'slug' => Str::slug($nama),
                ]
            );
            $this->command->info('   ✅ Kategori: ' . $nama);
        }
    }

    /**
     * 7. Create basic materials (bahan) untuk percetakan
     *
     * Harga dalam Rupiah per satuan (lembar/roll/meter).
     * Stok dalam bentuk string (misal: "100 lembar", "50 meter").
     */
    protected function createBahans(): void
    {
        $this->command->info('📦 Step 7: Creating materials (bahan)...');

        $vendor = Vendor::withoutGlobalScope('active')->where('email', 'vendor@grafika-printing.com')->first();
        if (!$vendor) {
            $this->command->warn('   ⚠️  Vendor not found, skipping materials');
            return;
        }

        $bahanList = [
            // Art Paper
            ['nama_bahan' => 'Art Paper 150gsm', 'hpp' => 2500.00, 'satuan' => 'lembar', 'stok' => '500'],
            ['nama_bahan' => 'Art Paper 200gsm', 'hpp' => 3500.00, 'satuan' => 'lembar', 'stok' => '500'],
            ['nama_bahan' => 'Art Paper 230gsm', 'hpp' => 4000.00, 'satuan' => 'lembar', 'stok' => '500'],
            ['nama_bahan' => 'Art Paper 260gsm', 'hpp' => 4500.00, 'satuan' => 'lembar', 'stok' => '500'],
            ['nama_bahan' => 'Art Paper 300gsm', 'hpp' => 5500.00, 'satuan' => 'lembar', 'stok' => '500'],

            // Ivory
            ['nama_bahan' => 'Ivory 250gsm', 'hpp' => 6000.00, 'satuan' => 'lembar', 'stok' => '300'],
            ['nama_bahan' => 'Ivory 270gsm', 'hpp' => 6500.00, 'satuan' => 'lembar', 'stok' => '300'],
            ['nama_bahan' => 'Ivory 300gsm', 'hpp' => 7500.00, 'satuan' => 'lembar', 'stok' => '300'],

            // HVS
            ['nama_bahan' => 'HVS 60gsm', 'hpp' => 800.00, 'satuan' => 'lembar', 'stok' => '2000'],
            ['nama_bahan' => 'HVS 70gsm', 'hpp' => 1000.00, 'satuan' => 'lembar', 'stok' => '2000'],
            ['nama_bahan' => 'HVS 80gsm', 'hpp' => 1200.00, 'satuan' => 'lembar', 'stok' => '2000'],

            // Premium
            ['nama_bahan' => 'Linen', 'hpp' => 8000.00, 'satuan' => 'lembar', 'stok' => '200'],
            ['nama_bahan' => 'Jasmine', 'hpp' => 7000.00, 'satuan' => 'lembar', 'stok' => '200'],
            ['nama_bahan' => 'Fancy Paper', 'hpp' => 9000.00, 'satuan' => 'lembar', 'stok' => '200'],

            // Special Media
            ['nama_bahan' => 'Vinyl', 'hpp' => 15000.00, 'satuan' => 'meter', 'stok' => '100'],
            ['nama_bahan' => 'Magnet', 'hpp' => 25000.00, 'satuan' => 'meter', 'stok' => '50'],
            ['nama_bahan' => 'Canvas', 'hpp' => 20000.00, 'satuan' => 'meter', 'stok' => '50'],
            ['nama_bahan' => 'Photo Paper', 'hpp' => 5000.00, 'satuan' => 'lembar', 'stok' => '300'],
        ];

        foreach ($bahanList as $bahan) {
            \App\Models\Vendor\Bahan::withoutGlobalScope('active')->firstOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'nama_bahan' => $bahan['nama_bahan'],
                ],
                [
                    'hpp' => $bahan['hpp'],
                    'satuan' => $bahan['satuan'],
                    'stok' => $bahan['stok'],
                ]
            );
            $this->command->info('   ✅ Bahan: ' . $bahan['nama_bahan'] . ' (Rp ' . number_format($bahan['hpp'], 0, ',', '.') . '/' . $bahan['satuan'] . ')');
        }
    }

    /**
     * 8. Create basic tools (alat) untuk percetakan
     */
    protected function createAlats(): void
    {
        $this->command->info('🔧 Step 8: Creating tools (alat)...');

        $vendor = Vendor::withoutGlobalScope('active')->where('email', 'vendor@grafika-printing.com')->first();
        if (!$vendor) {
            $this->command->warn('   ⚠️  Vendor not found, skipping tools');
            return;
        }

        $alatList = [
            [
                'nama_alat' => 'Mesin Cetak Offset',
                'merek' => 'Heidelberg',
                'model' => 'Speedmaster 52',
                'spesifikasi_alat' => 'Cetak offset 4 warna (CMYK), ukuran kertas max A3, kecepatan 10000 lembar/jam',
                'status' => 'aktif',
                'kapasitas_cetak_per_jam' => 10000,
            ],
            [
                'nama_alat' => 'Mesin Cetak Digital',
                'merek' => 'Canon',
                'model' => 'imageRUNNER C3530',
                'spesifikasi_alat' => 'Cetak digital warna, resolusi 2400x600 dpi, ukuran kertas max A3, kecepatan 30 lembar/menit',
                'status' => 'aktif',
                'kapasitas_cetak_per_jam' => 1800,
            ],
            [
                'nama_alat' => 'Mesin Potong',
                'merek' => 'Guillotine',
                'model' => 'EP 76',
                'spesifikasi_alat' => 'Mesin potong kertas, kapasitas potong max 76 lembar, presisi 0.5mm',
                'status' => 'aktif',
                'kapasitas_cetak_per_jam' => 500,
            ],
            [
                'nama_alat' => 'Mesin Laminasi',
                'merek' => 'Fellowes',
                'model' => 'Lunar 35',
                'spesifikasi_alat' => 'Laminasi panas/dingin, ukuran max A3, kecepatan 30cm/menit',
                'status' => 'aktif',
                'kapasitas_cetak_per_jam' => 200,
            ],
            [
                'nama_alat' => 'Mesin Pond',
                'merek' => 'Fiamo',
                'model' => 'ECO 80',
                'spesifikasi_alat' => 'Mesin pond/kiss cut, area kerja 80x50cm, tekanan 20 ton',
                'status' => 'aktif',
                'kapasitas_cetak_per_jam' => 150,
            ],
            [
                'nama_alat' => 'Mesin Bubut',
                'merek' => 'CNC',
                'model' => 'Mini Lathe 7x14',
                'spesifikasi_alat' => 'Mesin bubut mini untuk pembuatan parts dan dies, diameter max 180mm',
                'status' => 'aktif',
                'kapasitas_cetak_per_jam' => 50,
            ],
        ];

        foreach ($alatList as $alat) {
            \App\Models\Vendor\Alat::withoutGlobalScope('active')->firstOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'nama_alat' => $alat['nama_alat'],
                ],
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
            $this->command->info('   ✅ Alat: ' . $alat['nama_alat'] . ' (' . $alat['merek'] . ' ' . $alat['model'] . ')');
        }
    }

    /**
     * Display seeding summary
     */
    protected function displaySummary(): void
    {
        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════╗');
        $this->command->info('║           PRODUCTION SEEDING SUMMARY                    ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->info('║ 👤 Admin:      admin@grafika-printing.com               ║');
        $this->command->info('║                (password: password, usertype: dev)      ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 🏭 Vendor:     vendor@grafika-printing.com              ║');
        $this->command->info('║                (password: password, usertype: vendor)   ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 👤 Customer:   customer@grafika-printing.com            ║');
        $this->command->info('║                (password: password, usertype: user)     ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 💰 Admin Fees: Auction 5% + Min Rp 5,000                ║');
        $this->command->info('║                Payment Gateway 2.5%                     ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 📄 CMS:       11 settings (general, contact, social)    ║');
        $this->command->info('║                                                         ║');
        $this->command->info('║ 📂 Kategori:  10 kategori produk printing               ║');
        $this->command->info('║ 📦 Bahan:     18 jenis bahan baku                       ║');
        $this->command->info('║ 🔧 Alat:      6 peralatan cetak                         ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->info('║ ⚠️  GANTI PASSWORD SETELAH LOGIN PERTAMA!              ║');
        $this->command->info('╚══════════════════════════════════════════════════════════╝');
    }
}

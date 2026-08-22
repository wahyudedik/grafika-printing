<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * PosCompleteSeeder - Seeder lengkap dan realistis untuk bisnis percetakan Indonesia
 *
 * Seeder ini membuat data POS LENGKAP untuk 1 vendor dengan data realistis:
 * - 10 kategori produk percetakan
 * - 15+ bahan percetakan dengan HPP realistis (per m²)
 * - 10 spesifikasi produk
 * - 10 produk percetakan populer
 * - Spesifikasi produk (hubungan produk-spesifikasi)
 * - Bahan spesifikasi produk (pivot table)
 * - 6 alat percetakan
 * - Estimasi produksi
 * - Wholesale price (harga grosir bahan)
 * - 5 pelanggan test
 * - Printer setting
 *
 * Menggunakan DB::table() langsung untuk menghindari dependency TenantModel.
 * Semua data dalam Rupiah (IDR).
 *
 * Usage:
 *   php artisan db:seed --class=PosCompleteSeeder
 *   php artisan db:seed --class=PosCompleteSeeder --force
 */
class PosCompleteSeeder extends Seeder
{
    protected int $vendorId;
    protected array $kategoriMap = [];
    protected array $bahanMap = [];
    protected array $spesifikasiMap = [];
    protected array $produkMap = [];
    protected array $alatMap = [];
    protected array $spesifikasiProdukMap = [];

    public function run(): void
    {
        $this->command->info('🏪 PosCompleteSeeder: Memulai seeding data POS lengkap...');
        $this->command->newLine();

        // Cari vendor yang sudah ada dari ProductionSeeder
        $vendor = DB::table('vendors')->where('email', 'vendor@grafika-printing.com')->first();

        if (!$vendor) {
            $this->command->error('❌ Vendor tidak ditemukan! Jalankan ProductionSeeder terlebih dahulu.');
            $this->command->info('   php artisan db:seed --class=ProductionSeeder');
            return;
        }

        $this->vendorId = $vendor->id;
        $this->command->info("🏭 Vendor: {$vendor->name} (ID: {$this->vendorId})");
        $this->command->newLine();

        DB::beginTransaction();

        try {
            $this->seedKategoriProduk();
            $this->seedBahan();
            $this->seedSpesifikasi();
            $this->seedAlat();
            $this->seedProduk();
            $this->seedSpesifikasiProduk();
            $this->seedBahanSpesifikasiProduk();
            $this->seedEstimasiProduk();
            $this->seedWholesalePrice();
            $this->seedPelanggan();
            $this->seedPrinterSetting();
            $this->seedCoupons();

            DB::commit();

            $this->command->newLine();
            $this->displaySummary();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ PosCompleteSeeder: Gagal - ' . $e->getMessage());
            Log::error('PosCompleteSeeder failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // 1. KATEGORI PRODUK (10 kategori percetakan)
    // ═══════════════════════════════════════════════════════════════
    protected function seedKategoriProduk(): void
    {
        $this->command->info('📂 Step 1: Membuat kategori produk...');

        $data = [
            ['nama_kategori' => 'Banner',      'slug' => 'banner'],
            ['nama_kategori' => 'Kartu Nama',   'slug' => 'kartu-nama'],
            ['nama_kategori' => 'Stiker',       'slug' => 'stiker'],
            ['nama_kategori' => 'Brosur',       'slug' => 'brosur'],
            ['nama_kategori' => 'Poster',       'slug' => 'poster'],
            ['nama_kategori' => 'Undangan',     'slug' => 'undangan'],
            ['nama_kategori' => 'Label',        'slug' => 'label'],
            ['nama_kategori' => 'Packaging',    'slug' => 'packaging'],
            ['nama_kategori' => 'Mapping',      'slug' => 'mapping'],
            ['nama_kategori' => 'Sablon',       'slug' => 'sablon'],
        ];

        foreach ($data as $row) {
            DB::table('kategori_produks')->updateOrInsert(
                ['slug' => $row['slug'], 'vendor_id' => $this->vendorId],
                [
                    'nama_kategori' => $row['nama_kategori'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $kategori = DB::table('kategori_produks')
                ->where('slug', $row['slug'])
                ->where('vendor_id', $this->vendorId)
                ->first();
            $this->kategoriMap[$row['slug']] = $kategori->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' kategori produk dibuat');
    }

    // ═══════════════════════════════════════════════════════════════
    // 2. BAHAN (15+ bahan percetakan dengan HPP realistis per m²)
    // ═══════════════════════════════════════════════════════════════
    protected function seedBahan(): void
    {
        $this->command->info('📦 Step 2: Membuat bahan percetakan...');

        // HPP = Harga Pokok Per m² (Rupiah) — realistis untuk industri percetakan Indonesia
        $data = [
            // Indoor Papers
            ['nama_bahan' => 'Art Paper 150gsm',    'hpp' => 3500,  'satuan' => 'm²',   'stok' => '500', 'minimum_stok' => 50],
            ['nama_bahan' => 'Art Paper 200gsm',    'hpp' => 4500,  'satuan' => 'm²',   'stok' => '500', 'minimum_stok' => 50],
            ['nama_bahan' => 'Art Paper 230gsm',    'hpp' => 5500,  'satuan' => 'm²',   'stok' => '500', 'minimum_stok' => 50],
            ['nama_bahan' => 'Art Paper 260gsm',    'hpp' => 6500,  'satuan' => 'm²',   'stok' => '500', 'minimum_stok' => 50],

            // Carton
            ['nama_bahan' => 'Art Carton 250gsm',   'hpp' => 7000,  'satuan' => 'm²',   'stok' => '300', 'minimum_stok' => 30],
            ['nama_bahan' => 'Art Carton 300gsm',   'hpp' => 8500,  'satuan' => 'm²',   'stok' => '300', 'minimum_stok' => 30],

            // Premium
            ['nama_bahan' => 'Ivory 250gsm',        'hpp' => 8000,  'satuan' => 'm²',   'stok' => '200', 'minimum_stok' => 20],

            // HVS (Houtvrij Schrijfpapier)
            ['nama_bahan' => 'HVS 60gsm',           'hpp' => 2000,  'satuan' => 'm²',   'stok' => '1000', 'minimum_stok' => 100],
            ['nama_bahan' => 'HVS 70gsm',           'hpp' => 2500,  'satuan' => 'm²',   'stok' => '1000', 'minimum_stok' => 100],
            ['nama_bahan' => 'HVS 80gsm',           'hpp' => 3000,  'satuan' => 'm²',   'stok' => '1000', 'minimum_stok' => 100],

            // Outdoor
            ['nama_bahan' => 'Vinyl Outdoor',       'hpp' => 15000, 'satuan' => 'm²',   'stok' => '200', 'minimum_stok' => 20],
            ['nama_bahan' => 'Vinyl Banner',        'hpp' => 12000, 'satuan' => 'm²',   'stok' => '200', 'minimum_stok' => 20],

            // Special
            ['nama_bahan' => 'Stiker Hologram',     'hpp' => 25000, 'satuan' => 'm²',   'stok' => '100', 'minimum_stok' => 10],

            // Photo & Special
            ['nama_bahan' => 'Photo Paper',         'hpp' => 8000,  'satuan' => 'm²',   'stok' => '150', 'minimum_stok' => 15],
            ['nama_bahan' => 'Kalkir',              'hpp' => 5000,  'satuan' => 'm²',   'stok' => '100', 'minimum_stok' => 10],
        ];

        foreach ($data as $row) {
            DB::table('bahans')->updateOrInsert(
                ['nama_bahan' => $row['nama_bahan'], 'vendor_id' => $this->vendorId],
                [
                    'hpp'          => $row['hpp'],
                    'satuan'       => $row['satuan'],
                    'stok'         => $row['stok'],
                    'minimum_stok' => $row['minimum_stok'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
            $bahan = DB::table('bahans')
                ->where('nama_bahan', $row['nama_bahan'])
                ->where('vendor_id', $this->vendorId)
                ->first();
            $this->bahanMap[$row['nama_bahan']] = $bahan->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' bahan percetakan dibuat');
    }

    // ═══════════════════════════════════════════════════════════════
    // 3. SPESIFIKASI (10 spesifikasi untuk produk percetakan)
    // ═══════════════════════════════════════════════════════════════
    protected function seedSpesifikasi(): void
    {
        $this->command->info('🔧 Step 3: Membuat spesifikasi...');

        $data = [
            ['nama_spesifikasi' => 'Bahan',                'tipe_input' => 'select', 'satuan' => '-'],
            ['nama_spesifikasi' => 'Ukuran',               'tipe_input' => 'select', 'satuan' => 'cm'],
            ['nama_spesifikasi' => 'Jumlah Lembar',         'tipe_input' => 'number', 'satuan' => 'lembar'],
            ['nama_spesifikasi' => 'Warna Cetak',           'tipe_input' => 'select', 'satuan' => '-'],
            ['nama_spesifikasi' => 'Finishing',              'tipe_input' => 'select', 'satuan' => '-'],
            ['nama_spesifikasi' => 'Resolusi Cetak',        'tipe_input' => 'select', 'satuan' => 'dpi'],
            ['nama_spesifikasi' => 'Desain',                 'tipe_input' => 'select', 'satuan' => '-'],
            ['nama_spesifikasi' => 'Durasi Pengerjaan',     'tipe_input' => 'select', 'satuan' => 'hari'],
            ['nama_spesifikasi' => 'Jumlah Cetak',           'tipe_input' => 'number', 'satuan' => 'pcs'],
            ['nama_spesifikasi' => 'Area Cetak',             'tipe_input' => 'number', 'satuan' => 'm²'],
        ];

        foreach ($data as $row) {
            DB::table('spesifikasis')->updateOrInsert(
                ['nama_spesifikasi' => $row['nama_spesifikasi'], 'vendor_id' => $this->vendorId],
                [
                    'tipe_input' => $row['tipe_input'],
                    'satuan'     => $row['satuan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $spesifikasi = DB::table('spesifikasis')
                ->where('nama_spesifikasi', $row['nama_spesifikasi'])
                ->where('vendor_id', $this->vendorId)
                ->first();
            $this->spesifikasiMap[$row['nama_spesifikasi']] = $spesifikasi->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' spesifikasi dibuat');
    }

    // ═══════════════════════════════════════════════════════════════
    // 4. ALAT (6 alat percetakan)
    // ═══════════════════════════════════════════════════════════════
    protected function seedAlat(): void
    {
        $this->command->info('🖨️ Step 4: Membuat alat percetakan...');

        $data = [
            [
                'nama_alat' => 'Mesin Cetak Digital Large Format',
                'merek' => 'Epson', 'model' => 'SureColor T5470M',
                'spesifikasi_alat' => 'Mesin cetak digital large format, lebar cetak max 36 inch, resolusi 2400x1200 dpi, tinta UltraChrome XD2.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 50, 'tersedia' => true,
            ],
            [
                'nama_alat' => 'Mesin Cetak Offset',
                'merek' => 'Heidelberg', 'model' => 'GTO 52',
                'spesifikasi_alat' => 'Mesin cetak offset 2 warna, ukuran kertas max B2 (50x70cm), kecepatan 5000 lembar/jam.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 5000, 'tersedia' => true,
            ],
            [
                'nama_alat' => 'Mesin Potong Gunting',
                'merek' => 'Guillotine', 'model' => 'EP 76',
                'spesifikasi_alat' => 'Mesin potong kertas hidrolik, kapasitas potong max 76 lembar sekaligus, presisi 0.5mm.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 200, 'tersedia' => true,
            ],
            [
                'nama_alat' => 'Mesin Laminasi',
                'merek' => 'Fellowes', 'model' => 'Lunar A3',
                'spesifikasi_alat' => 'Mesin laminasi panas/dingin, ukuran max A3, kecepatan 30cm/menit, suhu 80-160°C.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 100, 'tersedia' => true,
            ],
            [
                'nama_alat' => 'Mesin Pond',
                'merek' => 'Fiamo', 'model' => 'ECO 80',
                'spesifikasi_alat' => 'Mesin pond/kiss cut, area kerja 80x50cm, tekanan 20 ton, cocok untuk stiker dan kemasan.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 300, 'tersedia' => true,
            ],
            [
                'nama_alat' => 'Printer Desktop',
                'merek' => 'Canon', 'model' => 'imagePROGRAF PRO-300',
                'spesifikasi_alat' => 'Printer desktop A3+, resolusi 4800x2400 dpi, 10 warna tinta LUCIA PRO, cocok untuk foto dan proof.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 50, 'tersedia' => true,
            ],
        ];

        foreach ($data as $row) {
            DB::table('alats')->updateOrInsert(
                ['nama_alat' => $row['nama_alat'], 'vendor_id' => $this->vendorId],
                [
                    'merek'                    => $row['merek'],
                    'model'                    => $row['model'],
                    'spesifikasi_alat'         => $row['spesifikasi_alat'],
                    'status'                   => $row['status'],
                    'tanggal_pembelian'        => now()->subMonths(rand(6, 24))->toDateString(),
                    'kapasitas_cetak_per_jam'  => $row['kapasitas_cetak_per_jam'],
                    'tersedia'                 => $row['tersedia'],
                    'created_at'               => now(),
                    'updated_at'               => now(),
                ]
            );
            $alat = DB::table('alats')
                ->where('nama_alat', $row['nama_alat'])
                ->where('vendor_id', $this->vendorId)
                ->first();
            $this->alatMap[$row['nama_alat']] = $alat->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' alat percetakan dibuat');
    }

    // ═══════════════════════════════════════════════════════════════
    // 5. PRODUK (10 produk percetakan populer)
    // ═══════════════════════════════════════════════════════════════
    protected function seedProduk(): void
    {
        $this->command->info('🛍️ Step 5: Membuat produk...');

        $data = [
            [
                'nama_produk' => 'Kartu Nama',
                'deskripsi'   => 'Kartu nama bisnis ukuran 9x5.5cm, dicetak full color dua sisi. Tersedia berbagai pilihan bahan dari Art Carton hingga Ivory. Finishing laminasi doff/glossy tersedia.',
                'kategori'    => 'kartu-nama',
                'harga_jual'  => 35000,
            ],
            [
                'nama_produk' => 'Banner 1x1m',
                'deskripsi'   => 'Banner indoor/outdoor ukuran 1x1 meter. Bahan vinyl tahan cuaca, cetak full color resolusi tinggi. Cocok untuk promosi toko, booth pameran, dan event.',
                'kategori'    => 'banner',
                'harga_jual'  => 25000,
            ],
            [
                'nama_produk' => 'Banner 2x1m',
                'deskripsi'   => 'Banner indoor/outdoor ukuran 2x1 meter. Bahan vinyl tahan cuaca, cetak full color resolusi tinggi. Ukuran lebih besar untuk visibilitas maksimal.',
                'kategori'    => 'banner',
                'harga_jual'  => 45000,
            ],
            [
                'nama_produk' => 'Stiker Cutting',
                'deskripsi'   => 'Stiker vinyl custom potong sesuai desain. Waterproof, tahan UV, cocok untuk branding produk, labeling, dan dekorasi. Tersedia stiker hologram.',
                'kategori'    => 'stiker',
                'harga_jual'  => 5000,
            ],
            [
                'nama_produk' => 'Brosur A4',
                'deskripsi'   => 'Brosur ukuran A4, cetak full color dua sisi. Bahan Art Paper 150-200gsm. Cocok untuk promosi produk, menu restoran, dan profile perusahaan.',
                'kategori'    => 'brosur',
                'harga_jual'  => 500,
            ],
            [
                'nama_produk' => 'Poster A3',
                'deskripsi'   => 'Poster ukuran A3 (29.7x42cm), cetak full color. Bahan Art Paper atau Photo Paper. Cocok untuk dekorasi, display produk, dan presentasi.',
                'kategori'    => 'poster',
                'harga_jual'  => 15000,
            ],
            [
                'nama_produk' => 'Undangan Softcover',
                'deskripsi'   => 'Undangan pernikahan/event softcover. Bahan Art Carton/Ivory, cetak full color, tersedia berbagai finishing. Desain elegan dan custom.',
                'kategori'    => 'undangan',
                'harga_jual'  => 3000,
            ],
            [
                'nama_produk' => 'Label Produk',
                'deskripsi'   => 'Label produk sticker untuk branding dan informasi. Potong custom, tahan air, cocok untuk botol, kemasan, dan produk retail.',
                'kategori'    => 'label',
                'harga_jual'  => 2000,
            ],
            [
                'nama_produk' => 'Dus Kemasan',
                'deskripsi'   => 'Dus/box kemasan custom dari Art Carton. Cetak full color, potong & lipat sesuai desain. Cocok untuk produk makanan, kosmetik, dan retail.',
                'kategori'    => 'packaging',
                'harga_jual'  => 15000,
            ],
            [
                'nama_produk' => 'Mapping Per Meter',
                'deskripsi'   => 'Cetak mapping/printing large format per meter. Bahan vinyl banner, cocok untuk spanduk, backdrop, dan signage ukuran besar.',
                'kategori'    => 'mapping',
                'harga_jual'  => 15000,
            ],
        ];

        foreach ($data as $row) {
            DB::table('produks')->updateOrInsert(
                ['nama_produk' => $row['nama_produk'], 'vendor_id' => $this->vendorId],
                [
                    'deskripsi'   => $row['deskripsi'],
                    'kategori_id' => $this->kategoriMap[$row['kategori']],
                    'gambar'      => null,
                    'harga_jual'  => $row['harga_jual'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
            $produk = DB::table('produks')
                ->where('nama_produk', $row['nama_produk'])
                ->where('vendor_id', $this->vendorId)
                ->first();
            $slug = Str::slug($row['nama_produk']);
            $this->produkMap[$slug] = $produk->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' produk dibuat');
    }

    // ═══════════════════════════════════════════════════════════════
    // 6. SPESIFIKASI PRODUK (hubungkan produk dengan spesifikasi)
    // ═══════════════════════════════════════════════════════════════
    protected function seedSpesifikasiProduk(): void
    {
        $this->command->info('🔗 Step 6: Menghubungkan produk dengan spesifikasi...');

        // Mapping: produk_slug => [nama_spesifikasi => ['pilihan' => [...], 'wajib' => bool]]
        $links = [
            'kartu-nama' => [
                'Bahan'              => ['pilihan' => ['Art Carton 250gsm', 'Art Carton 300gsm', 'Ivory 250gsm'], 'wajib' => true],
                'Jumlah Lembar'       => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)', 'Hitam Putih', '2 Warna'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Laminating Doff', 'Laminating Glossy', 'Emboss', 'Foil'], 'wajib' => false],
                'Resolusi Cetak'       => ['pilihan' => ['300 DPI', '600 DPI', '1200 DPI'], 'wajib' => false],
                'Desain'               => ['pilihan' => ['Kirim File Sendiri', 'Desain dari Kami', 'Revisi Desain'], 'wajib' => false],
                'Durasi Pengerjaan'    => ['pilihan' => ['1 Hari (Express)', '2-3 Hari (Normal)', '4-7 Hari (Santai)'], 'wajib' => false],
            ],
            'banner-1x1m' => [
                'Bahan'              => ['pilihan' => ['Vinyl Outdoor', 'Vinyl Banner'], 'wajib' => true],
                'Ukuran'              => ['pilihan' => ['100x100 cm'], 'wajib' => true],
                'Jumlah Cetak'         => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Laminating Doff', 'Laminating Glossy', 'Potong'], 'wajib' => false],
                'Durasi Pengerjaan'    => ['pilihan' => ['1 Hari (Express)', '2-3 Hari (Normal)'], 'wajib' => false],
            ],
            'banner-2x1m' => [
                'Bahan'              => ['pilihan' => ['Vinyl Outdoor', 'Vinyl Banner'], 'wajib' => true],
                'Ukuran'              => ['pilihan' => ['200x100 cm'], 'wajib' => true],
                'Jumlah Cetak'         => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Laminating Doff', 'Laminating Glossy', 'Potong'], 'wajib' => false],
                'Durasi Pengerjaan'    => ['pilihan' => ['1 Hari (Express)', '2-3 Hari (Normal)'], 'wajib' => false],
            ],
            'stiker-cutting' => [
                'Bahan'              => ['pilihan' => ['Stiker Hologram', 'Vinyl Outdoor'], 'wajib' => true],
                'Ukuran'              => ['pilihan' => ['A4 (21x29.7)', 'A3 (29.7x42)', 'Custom'], 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)', 'Hitam Putih'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Laminating Doff', 'Laminating Glossy', 'Potong', 'Pond'], 'wajib' => false],
                'Jumlah Cetak'         => ['pilihan' => null, 'wajib' => true],
            ],
            'brosur-a4' => [
                'Bahan'              => ['pilihan' => ['Art Paper 150gsm', 'Art Paper 200gsm', 'Art Paper 230gsm'], 'wajib' => true],
                'Ukuran'              => ['pilihan' => ['A4 (21x29.7)'], 'wajib' => true],
                'Jumlah Lembar'       => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)', 'Hitam Putih', '2 Warna'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Laminating Doff', 'Laminating Glossy', 'Potong'], 'wajib' => false],
                'Desain'               => ['pilihan' => ['Kirim File Sendiri', 'Desain dari Kami', 'Revisi Desain'], 'wajib' => false],
            ],
            'poster-a3' => [
                'Bahan'              => ['pilihan' => ['Art Paper 200gsm', 'Art Paper 230gsm', 'Photo Paper'], 'wajib' => true],
                'Ukuran'              => ['pilihan' => ['A3 (29.7x42)'], 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Laminating Doff', 'Laminating Glossy'], 'wajib' => false],
            ],
            'undangan-softcover' => [
                'Bahan'              => ['pilihan' => ['Art Carton 250gsm', 'Art Carton 300gsm', 'Ivory 250gsm'], 'wajib' => true],
                'Ukuran'              => ['pilihan' => ['A4 (21x29.7)', 'A3 (29.7x42)', 'Custom'], 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)', '2 Warna'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Laminating Doff', 'Laminating Glossy', 'Emboss', 'Foil'], 'wajib' => false],
                'Desain'               => ['pilihan' => ['Kirim File Sendiri', 'Desain dari Kami', 'Revisi Desain'], 'wajib' => false],
                'Durasi Pengerjaan'    => ['pilihan' => ['2-3 Hari (Normal)', '4-7 Hari (Santai)'], 'wajib' => false],
            ],
            'label-produk' => [
                'Bahan'              => ['pilihan' => ['Stiker Hologram', 'Vinyl Outdoor'], 'wajib' => true],
                'Ukuran'              => ['pilihan' => ['Custom'], 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)', 'Hitam Putih'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Laminating Doff', 'Laminating Glossy', 'Potong'], 'wajib' => false],
                'Jumlah Cetak'         => ['pilihan' => null, 'wajib' => true],
            ],
            'dus-kemasan' => [
                'Bahan'              => ['pilihan' => ['Art Carton 250gsm', 'Art Carton 300gsm'], 'wajib' => true],
                'Ukuran'              => ['pilihan' => ['Custom'], 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Potong', 'Pond'], 'wajib' => true],
                'Jumlah Cetak'         => ['pilihan' => null, 'wajib' => true],
            ],
            'mapping-per-meter' => [
                'Bahan'              => ['pilihan' => ['Vinyl Banner', 'Vinyl Outdoor'], 'wajib' => true],
                'Ukuran'              => ['pilihan' => ['Custom'], 'wajib' => true],
                'Warna Cetak'         => ['pilihan' => ['Full Color (CMYK)'], 'wajib' => true],
                'Finishing'            => ['pilihan' => ['Tanpa Finishing', 'Laminating Doff', 'Laminating Glossy', 'Potong'], 'wajib' => false],
                'Area Cetak'           => ['pilihan' => null, 'wajib' => true],
            ],
        ];

        $count = 0;
        foreach ($links as $produkSlug => $specs) {
            $produkId = $this->produkMap[$produkSlug] ?? null;
            if (!$produkId) {
                $this->command->warn("    ⚠️  Produk '{$produkSlug}' tidak ditemukan, skip.");
                continue;
            }

            foreach ($specs as $namaSpec => $config) {
                $spesifikasiId = $this->spesifikasiMap[$namaSpec] ?? null;
                if (!$spesifikasiId) {
                    $this->command->warn("    ⚠️  Spesifikasi '{$namaSpec}' tidak ditemukan, skip.");
                    continue;
                }

                DB::table('spesifikasi_produks')->updateOrInsert(
                    [
                        'produk_id'      => $produkId,
                        'spesifikasi_id' => $spesifikasiId,
                        'vendor_id'      => $this->vendorId,
                    ],
                    [
                        'wajib_diisi' => $config['wajib'] ? '1' : '0',
                        'pilihan'     => $config['pilihan'] ? json_encode($config['pilihan']) : null,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]
                );

                $spesProd = DB::table('spesifikasi_produks')
                    ->where('produk_id', $produkId)
                    ->where('spesifikasi_id', $spesifikasiId)
                    ->first();

                $key = "{$produkSlug}:{$namaSpec}";
                $this->spesifikasiProdukMap[$key] = $spesProd->id;
                $count++;
            }
        }

        $this->command->info("    ✅ {$count} hubungan produk-spesifikasi dibuat");
    }

    // ═══════════════════════════════════════════════════════════════
    // 7. BAHAN SPESIFIKASI PRODUK (pivot table)
    // ═══════════════════════════════════════════════════════════════
    protected function seedBahanSpesifikasiProduk(): void
    {
        $this->command->info('🧵 Step 7: Menghubungkan bahan dengan spesifikasi produk...');

        // Pivot: spesifikasiProdukKey => [bahan nama]
        // Hanya untuk spesifikasi tipe 'select' yang berkaitan dengan bahan
        $links = [
            // Kartu Nama — Bahan
            'kartu-nama:Bahan'                 => ['Art Carton 250gsm', 'Art Carton 300gsm', 'Ivory 250gsm'],
            // Kartu Nama — Finishing (tidak perlu bahan, tapi untuk Completeness)

            // Banner 1x1m — Bahan
            'banner-1x1m:Bahan'               => ['Vinyl Outdoor', 'Vinyl Banner'],
            // Banner 2x1m — Bahan
            'banner-2x1m:Bahan'               => ['Vinyl Outdoor', 'Vinyl Banner'],

            // Stiker — Bahan
            'stiker-cutting:Bahan'            => ['Stiker Hologram', 'Vinyl Outdoor'],

            // Brosur — Bahan
            'brosur-a4:Bahan'                 => ['Art Paper 150gsm', 'Art Paper 200gsm', 'Art Paper 230gsm'],

            // Poster — Bahan
            'poster-a3:Bahan'                 => ['Art Paper 200gsm', 'Art Paper 230gsm', 'Photo Paper'],

            // Undangan — Bahan
            'undangan-softcover:Bahan'        => ['Art Carton 250gsm', 'Art Carton 300gsm', 'Ivory 250gsm'],

            // Label — Bahan
            'label-produk:Bahan'              => ['Stiker Hologram', 'Vinyl Outdoor'],

            // Dus Kemasan — Bahan
            'dus-kemasan:Bahan'               => ['Art Carton 250gsm', 'Art Carton 300gsm'],

            // Mapping — Bahan
            'mapping-per-meter:Bahan'         => ['Vinyl Banner', 'Vinyl Outdoor'],
        ];

        $count = 0;
        foreach ($links as $spesProdKey => $bahanNames) {
            $spesProdId = $this->spesifikasiProdukMap[$spesProdKey] ?? null;
            if (!$spesProdId) {
                $this->command->warn("    ⚠️  SpesifikasiProduk '{$spesProdKey}' tidak ditemukan, skip.");
                continue;
            }

            foreach ($bahanNames as $bahanName) {
                $bahanId = $this->bahanMap[$bahanName] ?? null;
                if (!$bahanId) {
                    $this->command->warn("    ⚠️  Bahan '{$bahanName}' tidak ditemukan, skip.");
                    continue;
                }

                DB::table('bahan_spesifikasi_produk')->updateOrInsert(
                    ['bahan_id' => $bahanId, 'spesifikasi_produk_id' => $spesProdId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
                $count++;
            }
        }

        $this->command->info("    ✅ {$count} hubungan bahan-spesifikasi produk dibuat");
    }

    // ═══════════════════════════════════════════════════════════════
    // 8. ESTIMASI PRODUKSI (estimasi waktu per produk + alat)
    // ═══════════════════════════════════════════════════════════════
    protected function seedEstimasiProduk(): void
    {
        $this->command->info('⏱️ Step 8: Membuat estimasi produksi...');

        // produk_slug => [alat_nama => [waktu_persiapan(menit), waktu_produksi_per_unit(menit)]]
        $links = [
            'kartu-nama' => [
                'Mesin Cetak Digital Large Format'  => [10, 0.5],
                'Mesin Potong Gunting'              => [5, 0.3],
                'Mesin Laminasi'                    => [5, 0.2],
            ],
            'banner-1x1m' => [
                'Mesin Cetak Digital Large Format'  => [15, 5.0],
                'Mesin Potong Gunting'              => [5, 1.0],
            ],
            'banner-2x1m' => [
                'Mesin Cetak Digital Large Format'  => [15, 8.0],
                'Mesin Potong Gunting'              => [5, 1.5],
            ],
            'stiker-cutting' => [
                'Mesin Cetak Digital Large Format'  => [10, 2.0],
                'Mesin Pond'                        => [8, 1.0],
            ],
            'brosur-a4' => [
                'Mesin Cetak Offset'                => [20, 0.3],
                'Mesin Potong Gunting'              => [5, 0.2],
            ],
            'poster-a3' => [
                'Mesin Cetak Digital Large Format'  => [10, 3.0],
                'Printer Desktop'                   => [5, 2.0],
            ],
            'undangan-softcover' => [
                'Mesin Cetak Digital Large Format'  => [15, 1.5],
                'Mesin Potong Gunting'              => [10, 0.5],
                'Mesin Laminasi'                    => [5, 0.3],
            ],
            'label-produk' => [
                'Mesin Cetak Digital Large Format'  => [10, 1.0],
                'Mesin Pond'                        => [8, 0.5],
            ],
            'dus-kemasan' => [
                'Mesin Cetak Digital Large Format'  => [15, 3.0],
                'Mesin Pond'                        => [10, 1.5],
                'Mesin Potong Gunting'              => [5, 0.5],
            ],
            'mapping-per-meter' => [
                'Mesin Cetak Digital Large Format'  => [20, 10.0],
                'Mesin Potong Gunting'              => [5, 2.0],
            ],
        ];

        $count = 0;
        foreach ($links as $produkSlug => $tools) {
            $produkId = $this->produkMap[$produkSlug] ?? null;
            if (!$produkId) continue;

            foreach ($tools as $alatNama => $times) {
                $alatId = $this->alatMap[$alatNama] ?? null;
                if (!$alatId) continue;

                DB::table('estimasi_produks')->updateOrInsert(
                    [
                        'produk_id' => $produkId,
                        'alat_id'   => $alatId,
                        'vendor_id' => $this->vendorId,
                    ],
                    [
                        'waktu_persiapan'        => $times[0],
                        'waktu_produksi_per_unit' => $times[1],
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ]
                );
                $count++;
            }
        }

        $this->command->info("    ✅ {$count} estimasi produksi dibuat");
    }

    // ═══════════════════════════════════════════════════════════════
    // 9. WHOLESALE PRICE (harga grosir bahan)
    // ═══════════════════════════════════════════════════════════════
    protected function seedWholesalePrice(): void
    {
        $this->command->info('💲 Step 9: Membuat harga grosir bahan...');

        // [bahan_nama, min_quantity, max_quantity, harga_per_m²]
        // Harga grosir sedikit lebih murah dari HPP eceran (dalam konteks ini, grosir bahan)
        $data = [
            // Art Paper 150gsm — HPP: 3500/m²
            ['Art Paper 150gsm',    10,   50,   3300],
            ['Art Paper 150gsm',   51,  200,   3100],
            ['Art Paper 150gsm',  201,  500,   2900],

            // Art Paper 200gsm — HPP: 4500/m²
            ['Art Paper 200gsm',   10,   50,   4200],
            ['Art Paper 200gsm',   51,  200,   3900],
            ['Art Paper 200gsm',  201,  500,   3700],

            // Art Paper 230gsm — HPP: 5500/m²
            ['Art Paper 230gsm',   10,   50,   5200],
            ['Art Paper 230gsm',   51,  200,   4800],

            // Art Paper 260gsm — HPP: 6500/m²
            ['Art Paper 260gsm',   10,   50,   6100],
            ['Art Paper 260gsm',   51,  200,   5700],

            // Art Carton 250gsm — HPP: 7000/m²
            ['Art Carton 250gsm',  10,   50,   6600],
            ['Art Carton 250gsm',  51,  200,   6200],

            // Art Carton 300gsm — HPP: 8500/m²
            ['Art Carton 300gsm',  10,   50,   8000],
            ['Art Carton 300gsm',  51,  200,   7500],

            // HVS 80gsm — HPP: 3000/m²
            ['HVS 80gsm',         10,   50,   2800],
            ['HVS 80gsm',         51,  200,   2600],
            ['HVS 80gsm',        201,  500,   2400],

            // Vinyl Outdoor — HPP: 15000/m²
            ['Vinyl Outdoor',      10,   50,  14000],
            ['Vinyl Outdoor',      51,  200,  13000],

            // Vinyl Banner — HPP: 12000/m²
            ['Vinyl Banner',       10,   50,  11200],
            ['Vinyl Banner',       51,  200,  10500],
        ];

        $count = 0;
        foreach ($data as [$bahanName, $min, $max, $harga]) {
            $bahanId = $this->bahanMap[$bahanName] ?? null;
            if (!$bahanId) continue;

            DB::table('harga_grosir')->updateOrInsert(
                ['bahan_id' => $bahanId, 'min_quantity' => $min, 'vendor_id' => $this->vendorId],
                [
                    'max_quantity' => $max,
                    'harga'        => $harga,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
            $count++;
        }

        $this->command->info("    ✅ {$count} harga grosir dibuat");
    }

    // ═══════════════════════════════════════════════════════════════
    // 10. PELANGGAN (5 pelanggan test)
    // ═══════════════════════════════════════════════════════════════
    protected function seedPelanggan(): void
    {
        $this->command->info('👥 Step 10: Membuat data pelanggan...');

        $data = [
            [
                'kode'    => 'PLG-001',
                'nama'    => 'Toko Fashion Muslim',
                'alamat'  => 'Jl. Malioboro No. 10, Yogyakarta',
                'no_telp' => '081234567890',
                'email'   => 'fashion.muslim@gmail.com',
            ],
            [
                'kode'    => 'PLG-002',
                'nama'    => 'Kedai Kopi Premium',
                'alamat'  => 'Jl. Braga No. 25, Bandung',
                'no_telp' => '082345678901',
                'email'   => 'kopipremium@gmail.com',
            ],
            [
                'kode'    => 'PLG-003',
                'nama'    => 'Restoran Padang Sederhana',
                'alamat'  => 'Jl. Pemuda No. 5, Surabaya',
                'no_telp' => '083456789012',
                'email'   => 'padang.sederhana@gmail.com',
            ],
            [
                'kode'    => 'PLG-004',
                'nama'    => 'Toko ATK Sejahtera',
                'alamat'  => 'Jl. Asia Afrika No. 15, Bandung',
                'no_telp' => '084567890123',
                'email'   => 'atk.sejahtera@gmail.com',
            ],
            [
                'kode'    => 'PLG-005',
                'nama'    => 'Event Organizer Ceria',
                'alamat'  => 'Jl. Sudirman No. 20, Jakarta',
                'no_telp' => '085678901234',
                'email'   => 'eo.ceria@gmail.com',
            ],
        ];

        $count = 0;
        foreach ($data as $row) {
            DB::table('pelanggans')->updateOrInsert(
                ['kode' => $row['kode'], 'vendor_id' => $this->vendorId],
                [
                    'nama'                => $row['nama'],
                    'alamat'              => $row['alamat'],
                    'no_telp'             => $row['no_telp'],
                    'email'               => $row['email'],
                    'transaksi_terakhir'  => now()->subDays(rand(1, 30)),
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]
            );
            $count++;
        }

        $this->command->info("    ✅ {$count} pelanggan dibuat");
    }

    // ═══════════════════════════════════════════════════════════════
    // 11. PRINTER SETTING
    // ═══════════════════════════════════════════════════════════════
    protected function seedPrinterSetting(): void
    {
        $this->command->info('🖨️ Step 11: Membuat pengaturan printer...');

        DB::table('printer_settings')->updateOrInsert(
            ['vendor_id' => $this->vendorId],
            [
                'paper_width'        => '80mm',
                'font_size'          => 12,
                'margin'             => '0mm',
                'auto_print'         => false,
                'auto_cut'           => true,
                'auto_close_window'  => false,
                'print_delay'        => 500,
                'printer_name'       => null,
                'is_active'          => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]
        );

        $this->command->info('    ✅ Pengaturan printer dibuat (80mm, auto cut)');
    }

    // ═══════════════════════════════════════════════════════════════
    // 12. COUPONS (Sample kupon diskon)
    // ═══════════════════════════════════════════════════════════════
    protected function seedCoupons(): void
    {
        $this->command->info('🎫 Step 12: Membuat kupon diskon...');

        $data = [
            [
                'code' => 'DISKON10',
                'name' => 'Diskon 10% Semua Produk',
                'description' => 'Diskon 10% untuk semua produk percetakan, minimum pembelian Rp100.000',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_order' => 100000.00,
                'maximum_discount' => null,
                'usage_limit' => 100,
                'usage_limit_per_user' => 3,
                'starts_at' => now()->subDays(7),
                'expires_at' => now()->addDays(30),
            ],
            [
                'code' => 'HEMAT20',
                'name' => 'Hemat Rp20.000',
                'description' => 'Diskon tetap Rp20.000 untuk pembelian minimal Rp150.000',
                'type' => 'fixed',
                'value' => 20000.00,
                'minimum_order' => 150000.00,
                'maximum_discount' => null,
                'usage_limit' => 50,
                'usage_limit_per_user' => 1,
                'starts_at' => now()->subDays(3),
                'expires_at' => now()->addDays(60),
            ],
        ];

        foreach ($data as $row) {
            DB::table('coupons')->updateOrInsert(
                ['code' => $row['code'], 'vendor_id' => $this->vendorId],
                [
                    'name'                 => $row['name'],
                    'description'          => $row['description'],
                    'type'                 => $row['type'],
                    'value'                => $row['value'],
                    'minimum_order'        => $row['minimum_order'],
                    'maximum_discount'     => $row['maximum_discount'],
                    'usage_limit'          => $row['usage_limit'],
                    'usage_limit_per_user' => $row['usage_limit_per_user'],
                    'used_count'           => 0,
                    'starts_at'            => $row['starts_at'],
                    'expires_at'           => $row['expires_at'],
                    'is_active'            => true,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]
            );
        }

        $this->command->info('    ✅ ' . count($data) . ' kupon diskon dibuat');
    }

    // ═══════════════════════════════════════════════════════════════
    // SUMMARY
    // ═══════════════════════════════════════════════════════════════
    protected function displaySummary(): void
    {
        // Hitung jumlah data
        $kategoriCount   = DB::table('kategori_produks')->where('vendor_id', $this->vendorId)->count();
        $bahanCount      = DB::table('bahans')->where('vendor_id', $this->vendorId)->count();
        $spesifikasiCount = DB::table('spesifikasis')->where('vendor_id', $this->vendorId)->count();
        $alatCount       = DB::table('alats')->where('vendor_id', $this->vendorId)->count();
        $produkCount     = DB::table('produks')->where('vendor_id', $this->vendorId)->count();
        $spesProdCount   = DB::table('spesifikasi_produks')->where('vendor_id', $this->vendorId)->count();
        $bahanSpesCount  = DB::table('bahan_spesifikasi_produk')
            ->whereIn('spesifikasi_produk_id', function ($query) {
                $query->select('id')->from('spesifikasi_produks')->where('vendor_id', $this->vendorId);
            })->count();
        $estimasiCount   = DB::table('estimasi_produks')->where('vendor_id', $this->vendorId)->count();
        $wholesaleCount  = DB::table('harga_grosir')->where('vendor_id', $this->vendorId)->count();
        $pelangganCount  = DB::table('pelanggans')->where('vendor_id', $this->vendorId)->count();
        $couponCount     = DB::table('coupons')->where('vendor_id', $this->vendorId)->count();

        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║         POS COMPLETE SEEDER — RINGKASAN                     ║');
        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info("║  📂 Kategori Produk:      {$kategoriCount} kategori                    ║");
        $this->command->info("║  📦 Bahan Percetakan:     {$bahanCount} bahan (HPP per m²)            ║");
        $this->command->info("║  🔧 Spesifikasi:          {$spesifikasiCount} spesifikasi                  ║");
        $this->command->info("║  🖨️  Alat Percetakan:      {$alatCount} alat                           ║");
        $this->command->info("║  🛍️  Produk:               {$produkCount} produk                        ║");
        $this->command->info("║  🔗 Spesifikasi Produk:   {$spesProdCount} hubungan                    ║");
        $this->command->info("║  🧵 Bahan-Spesifikasi:    {$bahanSpesCount} pivot entries                ║");
        $this->command->info("║  ⏱️  Estimasi Produksi:    {$estimasiCount} estimasi                      ║");
        $this->command->info("║  💲 Harga Grosir:         {$wholesaleCount} tier harga                    ║");
        $this->command->info("║  👥 Pelanggan:            {$pelangganCount} pelanggan                     ║");
        $this->command->info("║  🎫 Kupon Diskon:         {$couponCount} kupon                          ║");
        $this->command->info("║  🖨️  Printer Setting:      1 setting (80mm, auto cut)              ║");
        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info('║  ✅ SEMUA DATA POS BERHASIL DIBUAT!                          ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  Vendor: vendor@grafika-printing.com                         ║');
        $this->command->info("║  Vendor ID: {$this->vendorId}                                        ║");
        $this->command->info('║  Data realistis untuk bisnis percetakan Indonesia.            ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
    }
}

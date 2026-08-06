<?php

namespace Database\Seeders;

use App\Facades\Tenant;
use App\Models\Vendor;
use App\Models\Vendor\Alat;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\TransaksiItemSpecifications;
use App\Models\Vendor\WholesalePrice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosSeeder extends Seeder
{
    protected ?Vendor $vendor = null;

    public function run(): void
    {
        $this->vendor = Vendor::where('email', 'vendor@example.com')->first();

        if (!$this->vendor) {
            $this->command->error('❌ Vendor not found! Run SimpleTestSeeder first.');
            return;
        }

        // Set tenant context agar TenantModel bisa mengisi vendor_id otomatis
        Tenant::setVendorId($this->vendor->id);

        $this->command->info('🏪 Starting POS data seeding...');

        $kategoriMap     = $this->seedKategoriProduk();
        $spesifikasiMap  = $this->seedSpesifikasi();
        $bahanMap        = $this->seedBahan();
        $alatMap         = $this->seedAlat();
        $produkMap       = $this->seedProduk($kategoriMap);
        $spesifikasiProdukMap = $this->seedSpesifikasiProduk($produkMap, $spesifikasiMap);
        $this->seedBahanSpesifikasiProduk($spesifikasiProdukMap, $bahanMap);
        $this->seedEstimasiProduk($produkMap, $alatMap);
        $this->seedPelanggan();
        $this->seedWholesalePrice($bahanMap);
        $this->seedTransaksi($produkMap, $spesifikasiProdukMap, $bahanMap);

        $this->command->newLine();
        $this->command->info('🎉 POS data seeded successfully!');
    }

    // ─── KategoriProduk ───────────────────────────────────────────
    protected function seedKategoriProduk(): array
    {
        $this->command->info('  📂 Creating product categories...');

        $data = [
            ['nama_kategori' => 'Kartu Nama & Undangan',   'slug' => 'kartu-nama-undangan'],
            ['nama_kategori' => 'Banner, Spanduk & X-Banner', 'slug' => 'banner-spanduk-xbanner'],
            ['nama_kategori' => 'Stiker & Label',           'slug' => 'stiker-label'],
            ['nama_kategori' => 'Dokumen & Brosur',         'slug' => 'dokumen-brosur'],
            ['nama_kategori' => 'Packaging & Kemasan',      'slug' => 'packaging-kemasan'],
            ['nama_kategori' => 'Merchandise & Souvenir',   'slug' => 'merchandise-souvenir'],
        ];

        $map = [];
        foreach ($data as $row) {
            $kategori = KategoriProduk::updateOrCreate(
                ['slug' => $row['slug']],
                ['nama_kategori' => $row['nama_kategori']]
            );
            $map[$row['slug']] = $kategori->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' categories created');
        return $map;
    }

    // ─── Spesifikasi ──────────────────────────────────────────────
    protected function seedSpesifikasi(): array
    {
        $this->command->info('  🔧 Creating specifications...');

        $data = [
            ['nama_spesifikasi' => 'Ukuran',          'tipe_input' => 'select', 'satuan' => '-',
             'pilihan' => ['A4', 'A3', 'A5', 'F4', 'Custom']],
            ['nama_spesifikasi' => 'Jenis Kertas',    'tipe_input' => 'select', 'satuan' => '-',
             'pilihan' => ['HVS 80gsm', 'Art Paper 150gsm', 'Art Carton 260gsm', 'Buffalo 250gsm', 'Stiker Vinyl']],
            ['nama_spesifikasi' => 'Finishing',        'tipe_input' => 'select', 'satuan' => '-',
             'pilihan' => ['Tanpa Finishing', 'Laminasi Doff', 'Laminasi Glossy', 'Potong', 'Folding']],
            ['nama_spesifikasi' => 'Jumlah Halaman',   'tipe_input' => 'number', 'satuan' => 'halaman'],
            ['nama_spesifikasi' => 'Warna Cetak',     'tipe_input' => 'select', 'satuan' => '-',
             'pilihan' => ['Hitam Putih', 'Full Color', 'Spot Color']],
            ['nama_spesifikasi' => 'Jumlah Item',      'tipe_input' => 'number', 'satuan' => 'pcs'],
            ['nama_spesifikasi' => 'Orientasi',        'tipe_input' => 'select', 'satuan' => '-',
             'pilihan' => ['Portrait', 'Landscape']],
            ['nama_spesifikasi' => 'Keterangan Tambahan', 'tipe_input' => 'text', 'satuan' => '-'],
        ];

        $map = [];
        foreach ($data as $row) {
            unset($row['pilihan']); // pilihan disimpan di spesifikasi_produks, bukan di spesifikasis

            $spesifikasi = Spesifikasi::updateOrCreate(
                ['nama_spesifikasi' => $row['nama_spesifikasi']],
                $row
            );

            $map[$row['nama_spesifikasi']] = $spesifikasi->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' specifications created');
        return $map;
    }

    // ─── Bahan ────────────────────────────────────────────────────
    protected function seedBahan(): array
    {
        $this->command->info('  📦 Creating materials...');

        $data = [
            ['nama_bahan' => 'Kertas HVS A4 80gsm',      'hpp' => 85000,   'satuan' => 'rim',     'stok' => 200],
            ['nama_bahan' => 'Kertas HVS F4 70gsm',      'hpp' => 75000,   'satuan' => 'rim',     'stok' => 150],
            ['nama_bahan' => 'Kertas Art Paper 150gsm',  'hpp' => 120000,  'satuan' => 'lembar',  'stok' => 500],
            ['nama_bahan' => 'Kertas Art Carton 260gsm', 'hpp' => 180000,  'satuan' => 'lembar',  'stok' => 300],
            ['nama_bahan' => 'Kertas Stiker Vinyl',       'hpp' => 95000,   'satuan' => 'meter',   'stok' => 100],
            ['nama_bahan' => 'Kertas Buffalo 250gsm',     'hpp' => 65000,   'satuan' => 'lembar',  'stok' => 250],
            ['nama_bahan' => 'Tinta Black',                'hpp' => 85000,   'satuan' => 'botol',   'stok' => 30],
            ['nama_bahan' => 'Tinta Color CMYK',           'hpp' => 320000,  'satuan' => 'set',     'stok' => 15],
            ['nama_bahan' => 'Laminasi Doff Film',         'hpp' => 45000,   'satuan' => 'meter',   'stok' => 200],
            ['nama_bahan' => 'Laminasi Glossy Film',       'hpp' => 45000,   'satuan' => 'meter',   'stok' => 200],
            ['nama_bahan' => 'Lem Panas',                  'hpp' => 25000,   'satuan' => 'batang',  'stok' => 50],
            ['nama_bahan' => 'Tali Kur',                   'hpp' => 15000,   'satuan' => 'roll',    'stok' => 40],
        ];

        $map = [];
        foreach ($data as $row) {
            $bahan = Bahan::updateOrCreate(
                ['nama_bahan' => $row['nama_bahan']],
                $row
            );
            $map[$row['nama_bahan']] = $bahan->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' materials created');
        return $map;
    }

    // ─── Alat ─────────────────────────────────────────────────────
    protected function seedAlat(): array
    {
        $this->command->info('  🖨️ Creating equipment...');

        $data = [
            [
                'nama_alat' => 'Mesin Cetak Digital Canon IR',
                'merek' => 'Canon', 'model' => 'IR 2525i',
                'spesifikasi_alat' => 'Mesin cetak digital multifungsi, cetak/warnet/fotokopi. Resolusi 1200x1200 dpi.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 25, 'tersedia' => true,
            ],
            [
                'nama_alat' => 'Mesin Potong Cutting Plotter',
                'merek' => 'Graphtec', 'model' => 'CE7000-130',
                'spesifikasi_alat' => 'Mesin potong otomatis untuk stiker dan vinyl. Lebar potong maks 130cm.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 50, 'tersedia' => true,
            ],
            [
                'nama_alat' => 'Mesin Laminasi',
                'merek' => 'Fellowes', 'model' => 'LUNA A4',
                'spesifikasi_alat' => 'Mesin laminasi panas untuk dokumen. Ukuran A4, suhu 80-160°C.',
                'status' => 'maintenance', 'kapasitas_cetak_per_jam' => 30, 'tersedia' => false,
            ],
            [
                'nama_alat' => 'Mesin Cetak Offset',
                'merek' => 'Heidelberg', 'model' => 'GTO 52',
                'spesifikasi_alat' => 'Mesin cetak offset untuk volume tinggi. Lebar cetak 35x52cm.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 5000, 'tersedia' => true,
            ],
            [
                'nama_alat' => 'Mesin Potong Manual',
                'merek' => 'Fellowes', 'model' => 'Saturn 3i A4',
                'spesifikasi_alat' => 'Mesin potong manual untuk dokumen. Kapasitas 12 lembar sekaligus.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 100, 'tersedia' => true,
            ],
            [
                'nama_alat' => 'Komputer Desain',
                'merek' => 'Dell', 'model' => 'Precision 3660',
                'spesifikasi_alat' => 'Workstation desain grafis. Intel i7, 32GB RAM, NVIDIA RTX 4060.',
                'status' => 'aktif', 'kapasitas_cetak_per_jam' => 0, 'tersedia' => true,
            ],
        ];

        $map = [];
        foreach ($data as $row) {
            $alat = Alat::updateOrCreate(
                ['nama_alat' => $row['nama_alat']],
                array_merge($row, ['tanggal_pembelian' => now()->subMonths(rand(6, 24))])
            );
            $map[$row['nama_alat']] = $alat->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' equipment created');
        return $map;
    }

    // ─── Produk ───────────────────────────────────────────────────
    protected function seedProduk(array $kategoriMap): array
    {
        $this->command->info('  🛍️ Creating products...');

        $data = [
            [
                'nama_produk' => 'Kartu Nama Standar',
                'deskripsi'   => 'Kartu nama standar ukuran 9x5.5cm, dicet full color dua sisi, bahan Art Carton 260gsm.',
                'kategori'    => 'kartu-nama-undangan',
            ],
            [
                'nama_produk' => 'Kartu Nama Premium',
                'deskripsi'   => 'Kartu nama premium dengan finishing laminasi glossy, emboss logo, ukuran 9x5.5cm.',
                'kategori'    => 'kartu-nama-undangan',
            ],
            [
                'nama_produk' => 'Undangan Pernikahan',
                'deskripsi'   => 'Undangan pernikahan custom dengan desain elegan, bahan Art Paper 270gsm, ukuran A5.',
                'kategori'    => 'kartu-nama-undangan',
            ],
            [
                'nama_produk' => 'Banner Indoor',
                'deskripsi'   => 'Banner indoor bahan flexi 280gsm, cetak full color resolusi tinggi, ukuran custom.',
                'kategori'    => 'banner-spanduk-xbanner',
            ],
            [
                'nama_produk' => 'Banner Outdoor',
                'deskripsi'   => 'Banner outdoor tahan cuaca bahan flexi 440gsm, laminasi, ukuran custom.',
                'kategori'    => 'banner-spanduk-xbanner',
            ],
            [
                'nama_produk' => 'Stiker Vinyl',
                'deskripsi'   => 'Stiker vinyl waterproof, potong custom sesuai desain, cocok untuk branding.',
                'kategori'    => 'stiker-label',
            ],
            [
                'nama_produk' => 'Brosur A4',
                'deskripsi'   => 'Brosur ukuran A4, cetak full color dua sisi, bahan Art Paper 150gsm.',
                'kategori'    => 'dokumen-brosur',
            ],
            [
                'nama_produk' => 'Nota / Invoice',
                'deskripsi'   => 'Nota atau invoice karbon set 3 rangkap, ukuran F4, cetak 2 warna.',
                'kategori'    => 'dokumen-brosur',
            ],
            [
                'nama_produk' => 'Box Kemasan',
                'deskripsi'   => 'Box kemasan custom dari Art Carton 350gsm, cetak full color, potong & lipat otomatis.',
                'kategori'    => 'packaging-kemasan',
            ],
            [
                'nama_produk' => 'Tumbler Custom',
                'deskripsi'   => 'Tumbler custom printing, ukuran 350ml, bahan stainless steel, tahan panas & dingin.',
                'kategori'    => 'merchandise-souvenir',
            ],
        ];

        $map = [];
        foreach ($data as $row) {
            $slug = Str::slug($row['nama_produk']);
            $produk = Produk::updateOrCreate(
                ['nama_produk' => $row['nama_produk']],
                [
                    'deskripsi'   => $row['deskripsi'],
                    'kategori_id' => $kategoriMap[$row['kategori']],
                    'gambar'      => null,
                ]
            );
            $map[$slug] = $produk->id;
        }

        $this->command->info('    ✅ ' . count($data) . ' products created');
        return $map;
    }

    // ─── SpesifikasiProduk ────────────────────────────────────────
    protected function seedSpesifikasiProduk(array $produkMap, array $spesifikasiMap): array
    {
        $this->command->info('  🔗 Linking products to specifications...');

        // Mapping: produk slug => [spesifikasi nama => pilihan atau null (number/text)]
        $links = [
            'kartu-nama-standar' => [
                'Ukuran'   => ['pilihan' => '9x5.5 cm', 'wajib' => true],
                'Jumlah Item' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Full Color', 'wajib' => false],
                'Finishing'   => ['pilihan' => 'Tanpa Finishing', 'wajib' => false],
            ],
            'kartu-nama-premium' => [
                'Ukuran'   => ['pilihan' => '9x5.5 cm', 'wajib' => true],
                'Jumlah Item' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Full Color', 'wajib' => true],
                'Finishing'   => ['pilihan' => 'Laminasi Glossy', 'wajib' => true],
            ],
            'undangan-pernikahan' => [
                'Ukuran'   => ['pilihan' => 'A5', 'wajib' => true],
                'Jenis Kertas' => ['pilihan' => 'Art Paper 150gsm', 'wajib' => true],
                'Jumlah Halaman' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Full Color', 'wajib' => true],
                'Finishing'   => ['pilihan' => 'Laminasi Doff', 'wajib' => false],
            ],
            'banner-indoor' => [
                'Ukuran'   => ['pilihan' => 'Custom', 'wajib' => true],
                'Jumlah Item' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Full Color', 'wajib' => true],
                'Keterangan Tambahan' => ['pilihan' => null, 'wajib' => false],
            ],
            'banner-outdoor' => [
                'Ukuran'   => ['pilihan' => 'Custom', 'wajib' => true],
                'Jumlah Item' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Full Color', 'wajib' => true],
                'Keterangan Tambahan' => ['pilihan' => null, 'wajib' => false],
            ],
            'stiker-vinyl' => [
                'Ukuran'   => ['pilihan' => 'Custom', 'wajib' => true],
                'Jumlah Item' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Full Color', 'wajib' => true],
                'Finishing'   => ['pilihan' => 'Tanpa Finishing', 'wajib' => false],
            ],
            'brosur-a4' => [
                'Ukuran'   => ['pilihan' => 'A4', 'wajib' => true],
                'Jenis Kertas' => ['pilihan' => 'Art Paper 150gsm', 'wajib' => true],
                'Jumlah Halaman' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Full Color', 'wajib' => true],
                'Orientasi' => ['pilihan' => 'Portrait', 'wajib' => false],
            ],
            'nota-invoice' => [
                'Ukuran'   => ['pilihan' => 'F4', 'wajib' => true],
                'Jenis Kertas' => ['pilihan' => 'HVS 80gsm', 'wajib' => true],
                'Jumlah Halaman' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Spot Color', 'wajib' => true],
            ],
            'box-kemasan' => [
                'Ukuran'   => ['pilihan' => 'Custom', 'wajib' => true],
                'Jenis Kertas' => ['pilihan' => 'Art Carton 260gsm', 'wajib' => true],
                'Jumlah Item' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Full Color', 'wajib' => true],
                'Finishing'   => ['pilihan' => 'Potong', 'wajib' => true],
            ],
            'tumbler-custom' => [
                'Jumlah Item' => ['pilihan' => null, 'wajib' => true],
                'Warna Cetak' => ['pilihan' => 'Full Color', 'wajib' => true],
                'Keterangan Tambahan' => ['pilihan' => null, 'wajib' => false],
            ],
        ];

        $map = []; // key: "produk_slug:spesifikasi_nama" => SpesifikasiProduk id

        foreach ($links as $produkSlug => $specs) {
            $produkId = $produkMap[$produkSlug] ?? null;
            if (!$produkId) continue;

            foreach ($specs as $namaSpec => $config) {
                $spesifikasiId = $spesifikasiMap[$namaSpec] ?? null;
                if (!$spesifikasiId) continue;

                // Tentukan pilihan berdasarkan tipe spesifikasi
                $pilihanValue = $config['pilihan'] ?? null;

                $spesProd = SpesifikasiProduk::updateOrCreate(
                    ['produk_id' => $produkId, 'spesifikasi_id' => $spesifikasiId],
                    [
                        'wajib_diisi' => ($config['wajib'] ?? false) ? '1' : '0',
                        'pilihan'     => $pilihanValue ? [$pilihanValue] : null,
                    ]
                );

                $map["$produkSlug:$namaSpec"] = $spesProd->id;
            }
        }

        $this->command->info('    ✅ ' . count($map) . ' product-specification links created');
        return $map;
    }

    // ─── BahanSpesifikasiProduk (Pivot) ───────────────────────────
    protected function seedBahanSpesifikasiProduk(array $spesifikasiProdukMap, array $bahanMap): void
    {
        $this->command->info('  🧵 Linking materials to product specifications...');

        // Pivot: spesifikasiProduk key => [bahan nama]
        // Termasuk Jenis Kertas, Finishing, dan Warna Cetak
        $links = [
            // ── Jenis Kertas ──
            'kartu-nama-standar:Jenis Kertas'       => ['Kertas Art Carton 260gsm'],
            'kartu-nama-premium:Jenis Kertas'       => ['Kertas Art Carton 260gsm'],
            'undangan-pernikahan:Jenis Kertas'       => ['Kertas Art Paper 150gsm', 'Kertas Art Carton 260gsm'],
            'brosur-a4:Jenis Kertas'                => ['Kertas HVS A4 80gsm', 'Kertas Art Paper 150gsm'],
            'nota-invoice:Jenis Kertas'             => ['Kertas HVS F4 70gsm'],
            'box-kemasan:Jenis Kertas'              => ['Kertas Art Carton 260gsm', 'Kertas Buffalo 250gsm'],

            // ── Finishing ──
            'kartu-nama-premium:Finishing'           => ['Laminasi Glossy Film'],
            'undangan-pernikahan:Finishing'           => ['Laminasi Doff Film', 'Laminasi Glossy Film'],
            'stiker-vinyl:Finishing'                  => ['Laminasi Glossy Film'],
            'box-kemasan:Finishing'                   => ['Lem Panas'],

            // ── Warna Cetak ──
            'kartu-nama-standar:Warna Cetak'         => ['Tinta Color CMYK'],
            'kartu-nama-premium:Warna Cetak'         => ['Tinta Color CMYK'],
            'undangan-pernikahan:Warna Cetak'        => ['Tinta Color CMYK'],
            'banner-indoor:Warna Cetak'              => ['Tinta Color CMYK'],
            'banner-outdoor:Warna Cetak'             => ['Tinta Color CMYK'],
            'brosur-a4:Warna Cetak'                  => ['Tinta Color CMYK'],
            'nota-invoice:Warna Cetak'               => ['Tinta Black', 'Tinta Color CMYK'],
        ];

        $count = 0;
        foreach ($links as $spesProdKey => $bahanNames) {
            $spesProdId = $spesifikasiProdukMap[$spesProdKey] ?? null;
            if (!$spesProdId) continue;

            foreach ($bahanNames as $bahanName) {
                $bahanId = $bahanMap[$bahanName] ?? null;
                if (!$bahanId) continue;

                DB::table('bahan_spesifikasi_produk')->updateOrInsert(
                    ['bahan_id' => $bahanId, 'spesifikasi_produk_id' => $spesProdId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
                $count++;
            }
        }

        $this->command->info("    ✅ {$count} material-specification links created");
    }

    // ─── EstimasiProduk ──────────────────────────────────────────
    protected function seedEstimasiProduk(array $produkMap, array $alatMap): void
    {
        $this->command->info('  ⏱️ Creating production estimates...');

        //produk slug => [alat nama => [persiapan(menit), per_unit(menit)]]
        $links = [
            'kartu-nama-standar' => [
                'Mesin Cetak Digital Canon IR' => [5, 0.5],
                'Mesin Potong Manual'          => [2, 0.2],
            ],
            'kartu-nama-premium' => [
                'Mesin Cetak Digital Canon IR' => [5, 0.5],
                'Mesin Potong Manual'          => [2, 0.3],
            ],
            'undangan-pernikahan' => [
                'Mesin Cetak Digital Canon IR' => [10, 1.0],
                'Mesin Potong Manual'          => [5, 0.5],
            ],
            'banner-indoor' => [
                'Mesin Cetak Digital Canon IR' => [15, 5.0],
            ],
            'banner-outdoor' => [
                'Mesin Cetak Digital Canon IR' => [15, 8.0],
                'Mesin Potong Cutting Plotter' => [5, 2.0],
            ],
            'stiker-vinyl' => [
                'Mesin Cetak Digital Canon IR' => [10, 2.0],
                'Mesin Potong Cutting Plotter' => [5, 1.0],
            ],
            'brosur-a4' => [
                'Mesin Cetak Digital Canon IR' => [8, 0.8],
                'Mesin Potong Manual'          => [3, 0.3],
            ],
            'nota-invoice' => [
                'Mesin Cetak Offset'          => [20, 0.3],
                'Mesin Potong Manual'          => [3, 0.2],
            ],
            'box-kemasan' => [
                'Mesin Cetak Digital Canon IR' => [10, 3.0],
                'Mesin Potong Cutting Plotter' => [5, 1.5],
            ],
            'tumbler-custom' => [
                'Komputer Desain'             => [30, 5.0],
            ],
        ];

        $count = 0;
        foreach ($links as $produkSlug => $tools) {
            $produkId = $produkMap[$produkSlug] ?? null;
            if (!$produkId) continue;

            foreach ($tools as $alatNama => $times) {
                $alatId = $alatMap[$alatNama] ?? null;
                if (!$alatId) continue;

                EstimasiProduk::updateOrCreate(
                    ['produk_id' => $produkId, 'alat_id' => $alatId],
                    [
                        'waktu_persiapan'          => $times[0],
                        'waktu_produksi_per_unit'   => $times[1],
                    ]
                );
                $count++;
            }
        }

        $this->command->info("    ✅ {$count} production estimates created");
    }

    // ─── Pelanggan ────────────────────────────────────────────────
    protected function seedPelanggan(): void
    {
        $this->command->info('  👥 Creating customers...');

        $data = [
            ['kode' => 'PLG-001', 'nama' => 'Budi Santoso',       'alamat' => 'Jl. Merdeka No. 10, Jakarta Pusat',  'no_telp' => '081234567890', 'email' => 'budi.santoso@gmail.com'],
            ['kode' => 'PLG-002', 'nama' => 'Siti Rahayu',         'alamat' => 'Jl. Sudirman Kav. 52, Jakarta Selatan', 'no_telp' => '082345678901', 'email' => 'siti.rahayu@gmail.com'],
            ['kode' => 'PLG-003', 'nama' => 'Ahmad Hidayat',       'alamat' => 'Jl. Gatot Subroto No. 30, Jakarta Timur', 'no_telp' => '083456789012', 'email' => 'ahmad.hidayat@yahoo.com'],
            ['kode' => 'PLG-004', 'nama' => 'Dewi Lestari',        'alamat' => 'Jl. Thamrin No. 88, Jakarta Pusat',   'no_telp' => '084567890123', 'email' => 'dewi.lestari@outlook.com'],
            ['kode' => 'PLG-005', 'nama' => 'Rizky Pratama',       'alamat' => 'Jl. Kuningan No. 15, Jakarta Selatan', 'no_telp' => '085678901234', 'email' => 'rizky.pratama@gmail.com'],
            ['kode' => 'PLG-006', 'nama' => 'Maya Putri',          'alamat' => 'Jl. Kemang No. 7, Jakarta Selatan',   'no_telp' => '086789012345', 'email' => 'maya.putri@gmail.com'],
            ['kode' => 'PLG-007', 'nama' => 'Hendra Wijaya',       'alamat' => 'Jl. Pejaten No. 22, Jakarta Selatan',  'no_telp' => '087890123456', 'email' => 'hendra.wijaya@gmail.com'],
            ['kode' => 'PLG-008', 'nama' => 'Lestari Budiman (Toko Bunga)', 'alamat' => 'Jl. Cipete Raya No. 5, Jakarta Selatan', 'no_telp' => '088901234567', 'email' => 'tokobunga.lestari@gmail.com'],
        ];

        $count = 0;
        foreach ($data as $row) {
            Pelanggan::updateOrCreate(
                ['kode' => $row['kode']],
                [
                    'nama'     => $row['nama'],
                    'alamat'   => $row['alamat'],
                    'no_telp'  => $row['no_telp'],
                    'email'    => $row['email'],
                ]
            );
            $count++;
        }

        $this->command->info("    ✅ {$count} customers created");
    }

    // ─── WholesalePrice ───────────────────────────────────────────
    protected function seedWholesalePrice(array $bahanMap): void
    {
        $this->command->info('  💲 Creating wholesale prices...');

        // [bahan nama, min_qty, max_qty, harga grosir]
        $data = [
            // ── Kertas HVS A4 ──
            ['Kertas HVS A4 80gsm',      10,   50,  82000],
            ['Kertas HVS A4 80gsm',      51,  200,  78000],
            ['Kertas HVS A4 80gsm',     201,  500,  74000],

            // ── Kertas Art Paper ──
            ['Kertas Art Paper 150gsm',   10,   50,  115000],
            ['Kertas Art Paper 150gsm',  51,  200,  108000],

            // ── Kertas Art Carton ──
            ['Kertas Art Carton 260gsm',  10,   50,  172000],
            ['Kertas Art Carton 260gsm', 51,  200,  163000],
            ['Kertas Art Carton 260gsm',201,  500,  155000],

            // ── Tinta ──
            ['Tinta Color CMYK',           3,   10,  300000],
            ['Tinta Color CMYK',          11,   30,  285000],
            ['Tinta Black',                5,   15,  80000],

            // ── Laminasi ──
            ['Laminasi Doff Film',        20,  100,  42000],
            ['Laminasi Glossy Film',      20,  100,  42000],
        ];

        $count = 0;
        foreach ($data as [$bahanName, $min, $max, $harga]) {
            $bahanId = $bahanMap[$bahanName] ?? null;
            if (!$bahanId) continue;

            WholesalePrice::updateOrCreate(
                ['bahan_id' => $bahanId, 'min_quantity' => $min],
                [
                    'max_quantity' => $max,
                    'harga'        => $harga,
                ]
            );
            $count++;
        }

        $this->command->info("    ✅ {$count} wholesale prices created");
    }

    // ─── Transaksi + Items + ItemSpecifications ──────────────────
    protected function seedTransaksi(array $produkMap, array $spesifikasiProdukMap, array $bahanMap): void
    {
        $this->command->info('  🧾 Creating sample transactions...');

        // Get pelanggan IDs
        $pelanggans = Pelanggan::where('vendor_id', $this->vendor->id)->pluck('id', 'kode');
        if ($pelanggans->isEmpty()) {
            $this->command->warn('    ⚠️ No pelanggan found, skipping transaksi seeding.');
            return;
        }

        // Status options with corresponding payment and progress
        $statuses = [
            ['status' => 'completed',     'payment_status' => 'paid',     'progress' => 100, 'tracking' => 'selesai'],
            ['status' => 'completed',     'payment_status' => 'paid',     'progress' => 100, 'tracking' => 'selesai'],
            ['status' => 'processing',    'payment_status' => 'paid',     'progress' => 25,  'tracking' => 'diproses'],
            ['status' => 'pending',       'payment_status' => 'pending',  'progress' => 0,   'tracking' => 'menunggu'],
            ['status' => 'completed',     'payment_status' => 'paid',     'progress' => 100, 'tracking' => 'selesai'],
            ['status' => 'quality_check', 'payment_status' => 'paid',     'progress' => 80,  'tracking' => 'dicetak'],
            ['status' => 'processing',    'payment_status' => 'paid',     'progress' => 25,  'tracking' => 'diproses'],
            ['status' => 'cancelled',     'payment_status' => 'cancelled','progress' => 0,   'tracking' => 'menunggu'],
        ];

        // Transaction definitions: [kode, pelanggan_kode, produk_slug, qty, harga_satuan, specs]
        $transaksiDefs = [
            [
                'kode' => 'TRX-2025-001',
                'pelanggan' => 'PLG-001',
                'items' => [
                    ['produk' => 'kartu-nama-standar', 'qty' => 200, 'harga' => 15000, 'specs' => [
                        'Ukuran'      => ['value' => '9x5.5 cm', 'input_type' => 'select', 'price' => 0],
                        'Jumlah Item'  => ['value' => '200', 'input_type' => 'number', 'price' => 0],
                        'Warna Cetak'  => ['value' => 'Full Color', 'input_type' => 'select', 'price' => 5000],
                        'Finishing'    => ['value' => 'Tanpa Finishing', 'input_type' => 'select', 'price' => 0],
                    ]],
                ],
                'catatan' => 'Kartu nama untuk acara seminar, desain sudah dikirim via email.',
                'payment_method' => 'cash',
            ],
            [
                'kode' => 'TRX-2025-002',
                'pelanggan' => 'PLG-002',
                'items' => [
                    ['produk' => 'undangan-pernikahan', 'qty' => 300, 'harga' => 8500, 'specs' => [
                        'Ukuran'         => ['value' => 'A5', 'input_type' => 'select', 'price' => 0],
                        'Jenis Kertas'   => ['value' => 'Art Paper 150gsm', 'input_type' => 'select', 'price' => 15000],
                        'Jumlah Halaman'  => ['value' => '2', 'input_type' => 'number', 'price' => 0],
                        'Warna Cetak'     => ['value' => 'Full Color', 'input_type' => 'select', 'price' => 3000],
                        'Finishing'       => ['value' => 'Laminasi Doff', 'input_type' => 'select', 'price' => 2000],
                    ]],
                ],
                'catatan' => 'Undangan pernikahan, tema rustic. Foto pengantin sudah dilampirkan.',
                'payment_method' => 'xendit',
            ],
            [
                'kode' => 'TRX-2025-003',
                'pelanggan' => 'PLG-003',
                'items' => [
                    ['produk' => 'banner-indoor', 'qty' => 5, 'harga' => 150000, 'specs' => [
                        'Ukuran'               => ['value' => '120x200 cm', 'input_type' => 'select', 'price' => 50000],
                        'Jumlah Item'           => ['value' => '5', 'input_type' => 'number', 'price' => 0],
                        'Warna Cetak'           => ['value' => 'Full Color', 'input_type' => 'select', 'price' => 25000],
                        'Keterangan Tambahan'   => ['value' => 'Mata ayam di atas dan bawah', 'input_type' => 'text', 'price' => 0],
                    ]],
                ],
                'catatan' => 'Banner promosi grand opening cabang baru.',
                'payment_method' => 'cash',
            ],
            [
                'kode' => 'TRX-2025-004',
                'pelanggan' => 'PLG-004',
                'items' => [
                    ['produk' => 'brosur-a4', 'qty' => 500, 'harga' => 2500, 'specs' => [
                        'Ukuran'          => ['value' => 'A4', 'input_type' => 'select', 'price' => 0],
                        'Jenis Kertas'    => ['value' => 'Art Paper 150gsm', 'input_type' => 'select', 'price' => 5000],
                        'Jumlah Halaman'   => ['value' => '4', 'input_type' => 'number', 'price' => 0],
                        'Warna Cetak'      => ['value' => 'Full Color', 'input_type' => 'select', 'price' => 3000],
                        'Orientasi'        => ['value' => 'Portrait', 'input_type' => 'select', 'price' => 0],
                    ]],
                ],
                'catatan' => 'Brosur promosi produk baru, file PDF sudah dikirim.',
                'payment_method' => 'xendit',
            ],
            [
                'kode' => 'TRX-2025-005',
                'pelanggan' => 'PLG-005',
                'items' => [
                    ['produk' => 'stiker-vinyl', 'qty' => 100, 'harga' => 5000, 'specs' => [
                        'Ukuran'       => ['value' => '5x5 cm', 'input_type' => 'select', 'price' => 0],
                        'Jumlah Item'   => ['value' => '100', 'input_type' => 'number', 'price' => 0],
                        'Warna Cetak'   => ['value' => 'Full Color', 'input_type' => 'select', 'price' => 2000],
                        'Finishing'     => ['value' => 'Tanpa Finishing', 'input_type' => 'select', 'price' => 0],
                    ]],
                    ['produk' => 'box-kemasan', 'qty' => 50, 'harga' => 25000, 'specs' => [
                        'Ukuran'         => ['value' => '15x10x5 cm', 'input_type' => 'select', 'price' => 0],
                        'Jenis Kertas'   => ['value' => 'Art Carton 260gsm', 'input_type' => 'select', 'price' => 10000],
                        'Jumlah Item'     => ['value' => '50', 'input_type' => 'number', 'price' => 0],
                        'Warna Cetak'     => ['value' => 'Full Color', 'input_type' => 'select', 'price' => 5000],
                        'Finishing'       => ['value' => 'Potong', 'input_type' => 'select', 'price' => 3000],
                    ]],
                ],
                'catatan' => 'Stiker branding + box kemasan untuk produk kosmetik.',
                'payment_method' => 'cash',
            ],
            [
                'kode' => 'TRX-2025-006',
                'pelanggan' => 'PLG-006',
                'items' => [
                    ['produk' => 'nota-invoice', 'qty' => 10, 'harga' => 120000, 'specs' => [
                        'Ukuran'          => ['value' => 'F4', 'input_type' => 'select', 'price' => 0],
                        'Jenis Kertas'    => ['value' => 'HVS 80gsm', 'input_type' => 'select', 'price' => 0],
                        'Jumlah Halaman'   => ['value' => '3', 'input_type' => 'number', 'price' => 0],
                        'Warna Cetak'      => ['value' => 'Spot Color', 'input_type' => 'select', 'price' => 15000],
                    ]],
                ],
                'catatan' => 'Nota karbon 3 rangkap untuk toko elektronik.',
                'payment_method' => 'cash',
            ],
            [
                'kode' => 'TRX-2025-007',
                'pelanggan' => 'PLG-007',
                'items' => [
                    ['produk' => 'kartu-nama-premium', 'qty' => 100, 'harga' => 25000, 'specs' => [
                        'Ukuran'       => ['value' => '9x5.5 cm', 'input_type' => 'select', 'price' => 0],
                        'Jumlah Item'   => ['value' => '100', 'input_type' => 'number', 'price' => 0],
                        'Warna Cetak'   => ['value' => 'Full Color', 'input_type' => 'select', 'price' => 5000],
                        'Finishing'     => ['value' => 'Laminasi Glossy', 'input_type' => 'select', 'price' => 3000],
                    ]],
                ],
                'catatan' => 'Kartu nama premium untuk arsitek, desain minimalis.',
                'payment_method' => 'xendit',
            ],
            [
                'kode' => 'TRX-2025-008',
                'pelanggan' => 'PLG-008',
                'items' => [
                    ['produk' => 'banner-outdoor', 'qty' => 2, 'harga' => 250000, 'specs' => [
                        'Ukuran'               => ['value' => '100x300 cm', 'input_type' => 'select', 'price' => 100000],
                        'Jumlah Item'           => ['value' => '2', 'input_type' => 'number', 'price' => 0],
                        'Warna Cetak'           => ['value' => 'Full Color', 'input_type' => 'select', 'price' => 40000],
                        'Keterangan Tambahan'   => ['value' => 'Tahan hujan, laminasi 2 sisi', 'input_type' => 'text', 'price' => 20000],
                    ]],
                ],
                'catatan' => 'Banner outdoor toko bunga, ukuran besar untuk depan toko.',
                'payment_method' => 'cash',
            ],
        ];

        $transaksiCount = 0;
        $itemCount = 0;
        $specCount = 0;

        foreach ($transaksiDefs as $idx => $def) {
            $statusInfo = $statuses[$idx];

            // Hitung total harga dari semua items
            $totalHarga = 0;
            foreach ($def['items'] as $item) {
                $itemTotal = $item['qty'] * $item['harga'];
                // Tambah harga specs
                foreach ($item['specs'] as $spec) {
                    $itemTotal += $spec['price'] * $item['qty'];
                }
                $totalHarga += $itemTotal;
            }

            // Buat transaksi
            $transaksi = Transaksi::updateOrCreate(
                ['kode' => $def['kode']],
                [
                    'pelanggan_id'        => $pelanggans[$def['pelanggan']],
                    'total_harga'         => $totalHarga,
                    'status'              => $statusInfo['status'],
                    'payment_method'      => $def['payment_method'],
                    'payment_status'      => $statusInfo['payment_status'],
                    'progress_percentage' => $statusInfo['progress'],
                    'tracking_status'     => $statusInfo['tracking'],
                    'catatan'             => $def['catatan'],
                    'tanggal_dibuat'      => now()->subDays(rand(0, 30))->subHours(rand(0, 12)),
                    'estimasi_selesai'    => now()->addDays(rand(3, 14)),
                    'payment_amount'      => $statusInfo['payment_status'] === 'paid' ? $totalHarga : null,
                    'paid_at'             => $statusInfo['payment_status'] === 'paid' ? now()->subDays(rand(0, 28)) : null,
                ]
            );
            $transaksiCount++;

            // Buat items
            foreach ($def['items'] as $itemDef) {
                $produkId = $produkMap[$itemDef['produk']] ?? null;
                if (!$produkId) continue;

                $transaksiItem = TransaksiItem::updateOrCreate(
                    ['transaksi_id' => $transaksi->id, 'produk_id' => $produkId],
                    [
                        'kuantitas'    => $itemDef['qty'],
                        'harga_satuan' => $itemDef['harga'],
                    ]
                );
                $itemCount++;

                // Buat item specifications
                foreach ($itemDef['specs'] as $namaSpec => $specData) {
                    // Cari spesifikasi_produk_id
                    $spesProdKey = $itemDef['produk'] . ':' . $namaSpec;
                    $spesProdId = $spesifikasiProdukMap[$spesProdKey] ?? null;
                    if (!$spesProdId) continue;

                    // Cari bahan_id dari spesifikasi produk (ambil bahan pertama yang terkait)
                    $bahanId = DB::table('bahan_spesifikasi_produk')
                        ->where('spesifikasi_produk_id', $spesProdId)
                        ->value('bahan_id');

                    TransaksiItemSpecifications::create([
                        'transaksi_item_id'     => $transaksiItem->id,
                        'spesifikasi_produk_id' => $spesProdId,
                        'bahan_id'              => $bahanId,
                        'value'                 => $specData['value'],
                        'input_type'            => $specData['input_type'],
                        'price'                 => $specData['price'],
                    ]);
                    $specCount++;
                }
            }
        }

        $this->command->info("    ✅ {$transaksiCount} transactions created");
        $this->command->info("    ✅ {$itemCount} transaction items created");
        $this->command->info("    ✅ {$specCount} item specifications created");
    }
}

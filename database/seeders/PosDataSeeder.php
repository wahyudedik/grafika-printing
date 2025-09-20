<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Alat;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\WholesalePrice;
use App\Models\Vendor\EstimasiProduk;
use Illuminate\Support\Facades\DB;

class PosDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            $this->createCategories();
            $this->createMaterials();
            $this->createTools();
            $this->createProducts();
            $this->createSpecifications();
            $this->createWholesalePrices();
            $this->createProductionEstimates();
            
            DB::commit();
            $this->command->info('✅ POS dummy data created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating POS dummy data: ' . $e->getMessage());
        }
    }

    private function createCategories()
    {
        $this->command->info('Creating product categories...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $categories = [
                [
                    'nama' => 'Digital Printing',
                    'deskripsi' => 'Layanan digital printing untuk berbagai kebutuhan',
                    'is_active' => true
                ],
                [
                    'nama' => 'Offset Printing',
                    'deskripsi' => 'Layanan offset printing untuk cetak massal',
                    'is_active' => true
                ],
                [
                    'nama' => 'Screen Printing',
                    'deskripsi' => 'Layanan screen printing untuk kaos dan merchandise',
                    'is_active' => true
                ],
                [
                    'nama' => 'Large Format',
                    'deskripsi' => 'Layanan large format untuk banner dan billboard',
                    'is_active' => true
                ],
                [
                    'nama' => 'Finishing',
                    'deskripsi' => 'Layanan finishing untuk produk cetak',
                    'is_active' => true
                ]
            ];

            foreach ($categories as $categoryData) {
                KategoriProduk::updateOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'nama' => $categoryData['nama']
                    ],
                    array_merge($categoryData, ['vendor_id' => $vendor->id])
                );
            }
        }
    }

    private function createMaterials()
    {
        $this->command->info('Creating materials...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $materials = [
                [
                    'nama' => 'Kertas HVS 80gsm',
                    'deskripsi' => 'Kertas HVS putih 80gsm untuk printing umum',
                    'satuan' => 'rim',
                    'harga_satuan' => 45000,
                    'stok' => rand(50, 200),
                    'stok_minimum' => 20,
                    'supplier' => 'PT Kertas Indonesia',
                    'kategori' => 'Kertas',
                    'is_active' => true
                ],
                [
                    'nama' => 'Kertas Art Paper 150gsm',
                    'deskripsi' => 'Kertas art paper glossy 150gsm untuk brosur',
                    'satuan' => 'rim',
                    'harga_satuan' => 85000,
                    'stok' => rand(30, 100),
                    'stok_minimum' => 15,
                    'supplier' => 'PT Kertas Premium',
                    'kategori' => 'Kertas',
                    'is_active' => true
                ],
                [
                    'nama' => 'Tinta Cyan',
                    'deskripsi' => 'Tinta cyan untuk offset printing',
                    'satuan' => 'kg',
                    'harga_satuan' => 125000,
                    'stok' => rand(20, 80),
                    'stok_minimum' => 10,
                    'supplier' => 'PT Tinta Printing',
                    'kategori' => 'Tinta',
                    'is_active' => true
                ],
                [
                    'nama' => 'Tinta Magenta',
                    'deskripsi' => 'Tinta magenta untuk offset printing',
                    'satuan' => 'kg',
                    'harga_satuan' => 125000,
                    'stok' => rand(20, 80),
                    'stok_minimum' => 10,
                    'supplier' => 'PT Tinta Printing',
                    'kategori' => 'Tinta',
                    'is_active' => true
                ],
                [
                    'nama' => 'Tinta Yellow',
                    'deskripsi' => 'Tinta yellow untuk offset printing',
                    'satuan' => 'kg',
                    'harga_satuan' => 125000,
                    'stok' => rand(20, 80),
                    'stok_minimum' => 10,
                    'supplier' => 'PT Tinta Printing',
                    'kategori' => 'Tinta',
                    'is_active' => true
                ],
                [
                    'nama' => 'Tinta Black',
                    'deskripsi' => 'Tinta black untuk offset printing',
                    'satuan' => 'kg',
                    'harga_satuan' => 125000,
                    'stok' => rand(20, 80),
                    'stok_minimum' => 10,
                    'supplier' => 'PT Tinta Printing',
                    'kategori' => 'Tinta',
                    'is_active' => true
                ],
                [
                    'nama' => 'Vinyl Banner',
                    'deskripsi' => 'Vinyl banner untuk outdoor advertising',
                    'satuan' => 'meter',
                    'harga_satuan' => 25000,
                    'stok' => rand(100, 500),
                    'stok_minimum' => 50,
                    'supplier' => 'PT Vinyl Indonesia',
                    'kategori' => 'Banner',
                    'is_active' => true
                ],
                [
                    'nama' => 'Flexi Banner',
                    'deskripsi' => 'Flexi banner untuk indoor advertising',
                    'satuan' => 'meter',
                    'harga_satuan' => 15000,
                    'stok' => rand(100, 500),
                    'stok_minimum' => 50,
                    'supplier' => 'PT Flexi Indonesia',
                    'kategori' => 'Banner',
                    'is_active' => true
                ]
            ];

            foreach ($materials as $materialData) {
                Bahan::updateOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'nama' => $materialData['nama']
                    ],
                    array_merge($materialData, ['vendor_id' => $vendor->id])
                );
            }
        }
    }

    private function createTools()
    {
        $this->command->info('Creating tools...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $tools = [
                [
                    'nama' => 'Printer Digital Canon',
                    'deskripsi' => 'Printer digital Canon untuk printing umum',
                    'tipe' => 'Digital Printer',
                    'kapasitas_harian' => 1000,
                    'status' => 'aktif',
                    'tersedia' => true,
                    'lokasi' => 'Workshop A',
                    'tanggal_maintenance' => now()->addDays(rand(30, 90)),
                    'biaya_operasional' => 50000
                ],
                [
                    'nama' => 'Mesin Offset Heidelberg',
                    'deskripsi' => 'Mesin offset Heidelberg untuk cetak massal',
                    'tipe' => 'Offset Machine',
                    'kapasitas_harian' => 5000,
                    'status' => 'aktif',
                    'tersedia' => true,
                    'lokasi' => 'Workshop B',
                    'tanggal_maintenance' => now()->addDays(rand(30, 90)),
                    'biaya_operasional' => 100000
                ],
                [
                    'nama' => 'Mesin Screen Printing',
                    'deskripsi' => 'Mesin screen printing untuk kaos',
                    'tipe' => 'Screen Printing',
                    'kapasitas_harian' => 2000,
                    'status' => 'aktif',
                    'tersedia' => true,
                    'lokasi' => 'Workshop C',
                    'tanggal_maintenance' => now()->addDays(rand(30, 90)),
                    'biaya_operasional' => 75000
                ],
                [
                    'nama' => 'Large Format Printer',
                    'deskripsi' => 'Printer large format untuk banner',
                    'tipe' => 'Large Format',
                    'kapasitas_harian' => 500,
                    'status' => 'aktif',
                    'tersedia' => true,
                    'lokasi' => 'Workshop D',
                    'tanggal_maintenance' => now()->addDays(rand(30, 90)),
                    'biaya_operasional' => 125000
                ],
                [
                    'nama' => 'Mesin Laminating',
                    'deskripsi' => 'Mesin laminating untuk finishing',
                    'tipe' => 'Finishing',
                    'kapasitas_harian' => 3000,
                    'status' => 'aktif',
                    'tersedia' => true,
                    'lokasi' => 'Workshop E',
                    'tanggal_maintenance' => now()->addDays(rand(30, 90)),
                    'biaya_operasional' => 30000
                ]
            ];

            foreach ($tools as $toolData) {
                Alat::updateOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'nama' => $toolData['nama']
                    ],
                    array_merge($toolData, ['vendor_id' => $vendor->id])
                );
            }
        }
    }

    private function createProducts()
    {
        $this->command->info('Creating products...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $categories = $vendor->kategori;
            
            $products = [
                [
                    'nama' => 'Cetak Brosur A4',
                    'deskripsi' => 'Cetak brosur ukuran A4 dengan kualitas tinggi',
                    'kategori_id' => $categories->where('nama', 'Digital Printing')->first()->id,
                    'harga_dasar' => 500,
                    'satuan' => 'lembar',
                    'is_active' => true
                ],
                [
                    'nama' => 'Cetak Kartu Nama',
                    'deskripsi' => 'Cetak kartu nama dengan berbagai ukuran',
                    'kategori_id' => $categories->where('nama', 'Digital Printing')->first()->id,
                    'harga_dasar' => 1000,
                    'satuan' => 'lembar',
                    'is_active' => true
                ],
                [
                    'nama' => 'Cetak Banner Vinyl',
                    'deskripsi' => 'Cetak banner vinyl untuk outdoor',
                    'kategori_id' => $categories->where('nama', 'Large Format')->first()->id,
                    'harga_dasar' => 15000,
                    'satuan' => 'meter',
                    'is_active' => true
                ],
                [
                    'nama' => 'Cetak Kaos Screen Printing',
                    'deskripsi' => 'Cetak kaos dengan teknik screen printing',
                    'kategori_id' => $categories->where('nama', 'Screen Printing')->first()->id,
                    'harga_dasar' => 25000,
                    'satuan' => 'pcs',
                    'is_active' => true
                ],
                [
                    'nama' => 'Cetak Buku Offset',
                    'deskripsi' => 'Cetak buku dengan teknik offset',
                    'kategori_id' => $categories->where('nama', 'Offset Printing')->first()->id,
                    'harga_dasar' => 2000,
                    'satuan' => 'lembar',
                    'is_active' => true
                ]
            ];

            foreach ($products as $productData) {
                Produk::updateOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'nama' => $productData['nama']
                    ],
                    array_merge($productData, ['vendor_id' => $vendor->id])
                );
            }
        }
    }

    private function createSpecifications()
    {
        $this->command->info('Creating specifications...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $specifications = [
                [
                    'nama' => 'Ukuran',
                    'deskripsi' => 'Ukuran produk',
                    'tipe_input' => 'select',
                    'opsi' => json_encode(['A4', 'A3', 'A5', 'Custom']),
                    'is_required' => true,
                    'is_active' => true
                ],
                [
                    'nama' => 'Jumlah',
                    'deskripsi' => 'Jumlah produk',
                    'tipe_input' => 'number',
                    'opsi' => json_encode(['min' => 1, 'max' => 10000]),
                    'is_required' => true,
                    'is_active' => true
                ],
                [
                    'nama' => 'Warna',
                    'deskripsi' => 'Jumlah warna',
                    'tipe_input' => 'select',
                    'opsi' => json_encode(['1 Warna', '2 Warna', '4 Warna', 'Full Color']),
                    'is_required' => true,
                    'is_active' => true
                ],
                [
                    'nama' => 'Kertas',
                    'deskripsi' => 'Jenis kertas',
                    'tipe_input' => 'select',
                    'opsi' => json_encode(['HVS 80gsm', 'Art Paper 150gsm', 'Art Paper 200gsm', 'Art Paper 300gsm']),
                    'is_required' => true,
                    'is_active' => true
                ],
                [
                    'nama' => 'Finishing',
                    'deskripsi' => 'Jenis finishing',
                    'tipe_input' => 'select',
                    'opsi' => json_encode(['Tanpa Finishing', 'Laminating Glossy', 'Laminating Doff', 'UV Varnish']),
                    'is_required' => false,
                    'is_active' => true
                ]
            ];

            foreach ($specifications as $specData) {
                Spesifikasi::updateOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'nama' => $specData['nama']
                    ],
                    array_merge($specData, ['vendor_id' => $vendor->id])
                );
            }
        }
    }

    private function createWholesalePrices()
    {
        $this->command->info('Creating wholesale prices...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $materials = $vendor->bahan;
            
            foreach ($materials as $material) {
                $wholesalePrices = [
                    [
                        'min_quantity' => 1,
                        'max_quantity' => 10,
                        'discount_percentage' => 0,
                        'price_per_unit' => $material->harga_satuan
                    ],
                    [
                        'min_quantity' => 11,
                        'max_quantity' => 50,
                        'discount_percentage' => 5,
                        'price_per_unit' => $material->harga_satuan * 0.95
                    ],
                    [
                        'min_quantity' => 51,
                        'max_quantity' => 100,
                        'discount_percentage' => 10,
                        'price_per_unit' => $material->harga_satuan * 0.90
                    ],
                    [
                        'min_quantity' => 101,
                        'max_quantity' => 500,
                        'discount_percentage' => 15,
                        'price_per_unit' => $material->harga_satuan * 0.85
                    ],
                    [
                        'min_quantity' => 501,
                        'max_quantity' => 1000,
                        'discount_percentage' => 20,
                        'price_per_unit' => $material->harga_satuan * 0.80
                    ]
                ];

                foreach ($wholesalePrices as $priceData) {
                    WholesalePrice::updateOrCreate(
                        [
                            'vendor_id' => $vendor->id,
                            'bahan_id' => $material->id,
                            'min_quantity' => $priceData['min_quantity']
                        ],
                        array_merge($priceData, [
                            'vendor_id' => $vendor->id,
                            'bahan_id' => $material->id
                        ])
                    );
                }
            }
        }
    }

    private function createProductionEstimates()
    {
        $this->command->info('Creating production estimates...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $products = $vendor->produk;
            $tools = $vendor->alat;
            
            foreach ($products as $product) {
                foreach ($tools as $tool) {
                    EstimasiProduk::updateOrCreate(
                        [
                            'vendor_id' => $vendor->id,
                            'produk_id' => $product->id,
                            'alat_id' => $tool->id
                        ],
                        [
                            'vendor_id' => $vendor->id,
                            'produk_id' => $product->id,
                            'alat_id' => $tool->id,
                            'waktu_setup' => rand(30, 120), // menit
                            'waktu_produksi_per_unit' => rand(5, 30), // menit
                            'biaya_setup' => rand(50000, 200000),
                            'biaya_operasional_per_unit' => rand(1000, 10000),
                            'kapasitas_maksimal' => $tool->kapasitas_harian,
                            'efficiency_percentage' => rand(80, 95)
                        ]
                    );
                }
            }
        }
    }
}

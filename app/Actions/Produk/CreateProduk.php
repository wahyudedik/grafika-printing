<?php

namespace App\Actions\Produk;

use App\Actions\BaseAction;
use App\Models\Vendor\Produk;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\EstimasiProduk;
use Illuminate\Support\Str;

class CreateProduk extends BaseAction
{
    /**
     * Create a new product with specifications and estimates.
     *
     * Expected data:
     * - vendor_id (int)
     * - nama_produk (string)
     * - deskripsi (string, nullable)
     * - kategori_id (int|string) — int for existing, 'new' for new category
     * - new_kategori (string, optional) — name for new category
     * - harga_jual (float, nullable)
     * - gambar (array, optional) — array of Illuminate\Http\UploadedFile
     * - spesifikasi (array, optional)
     *   - spesifikasi_id (int)
     *   - wajib_diisi (bool, optional)
     *   - pilihan (array, optional)
     *   - bahan_ids (array, optional)
     * - estimasi (array, optional)
     *   - alat_id (int)
     *   - waktu_persiapan (float)
     *   - waktu_produksi_per_unit (float)
     */
    public function handle(array $data): Produk
    {
        $vendorId = $data['vendor_id'];

        // Handle category - create new if needed
        $kategoriId = $data['kategori_id'];
        if ($data['kategori_id'] === 'new' && !empty($data['new_kategori'])) {
            $kategori = KategoriProduk::create([
                'nama_kategori' => $data['new_kategori'],
                'slug' => Str::slug($data['new_kategori']),
            ]);
            $kategoriId = $kategori->id;
        }

        // Handle image uploads
        $gambars = [];
        if (!empty($data['gambar']) && is_array($data['gambar'])) {
            foreach ($data['gambar'] as $file) {
                $gambarName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('produk_gambar'), $gambarName);
                $gambars[] = 'produk_gambar/' . $gambarName;
            }
        }

        // Create the product
        $produk = Produk::create([
            'vendor_id' => $vendorId,
            'nama_produk' => $data['nama_produk'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'kategori_id' => $kategoriId,
            'gambar' => $gambars,
            'harga_jual' => $data['harga_jual'] ?? null,
        ]);

        // Handle specifications
        if (!empty($data['spesifikasi']) && is_array($data['spesifikasi'])) {
            foreach ($data['spesifikasi'] as $spec) {
                $specModel = SpesifikasiProduk::create([
                    'produk_id' => $produk->id,
                    'spesifikasi_id' => $spec['spesifikasi_id'],
                    'wajib_diisi' => $spec['wajib_diisi'] ?? false,
                    'pilihan' => $spec['pilihan'] ?? [],
                ]);

                // Handle bahan (materials) for this specification
                if (isset($spec['bahan_ids']) && is_array($spec['bahan_ids'])) {
                    $specModel->bahanSpesifikasiProduk()->attach($spec['bahan_ids']);
                }
            }
        }

        // Handle production estimates
        if (!empty($data['estimasi']) && is_array($data['estimasi'])) {
            foreach ($data['estimasi'] as $estimasi) {
                EstimasiProduk::create([
                    'produk_id' => $produk->id,
                    'alat_id' => $estimasi['alat_id'],
                    'waktu_persiapan' => $estimasi['waktu_persiapan'],
                    'waktu_produksi_per_unit' => $estimasi['waktu_produksi_per_unit'],
                ]);
            }
        }

        return $produk;
    }
}

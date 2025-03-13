<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\WholesalePrice;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\TransaksiItemSpecifications;

class SpesifikasiProduk extends TenantModel
{
    protected $table = 'spesifikasi_produks';

    protected $fillable = [
        'vendor_id',
        'produk_id',
        'spesifikasi_id',
        'wajib_diisi',
        'pilihan'
    ];

    protected $casts = [
        'pilihan' => 'array'
    ];
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function spesifikasi()
    {
        return $this->belongsTo(Spesifikasi::class, 'spesifikasi_id');
    }

    public function transaksiItemSpecifications()
    {
        return $this->hasMany(TransaksiItemSpecifications::class, 'spesifikasi_produk_id');
    }

    // public function bahans()
    // {
    //     $vendorId = auth()->user()->vendor->id;

    //     // Relasi many-to-many antara SpesifikasiProduk dan Bahan
    //     // Menggunakan tabel pivot 'bahan_spesifikasi_produk'
    //     // Hanya mengambil bahan yang dimiliki oleh vendor yang sedang login
    //     return $this->belongsToMany(Bahan::class, 'bahan_spesifikasi_produk', 'spesifikasi_produk_id', 'bahan_id')
    //         ->when($vendorId, function ($query) use ($vendorId) {
    //             $query->where('bahans.vendor_id', $vendorId);
    //         });
    // }

    public function calculatePrice($value, $bahanId, $quantity)
    {
        $bahan = Bahan::find($bahanId);
        if (!$bahan) return 0;

        $basePrice = $bahan->harga_per_satuan * $value;

        // Apply wholesale pricing if applicable
        $wholesalePrice = new WholesalePrice();
        return $wholesalePrice->calculateFinalPrice($basePrice, $quantity, $bahanId);
    }

    public function validateSpecificationValue($value)
    {
        switch ($this->spesifikasi->tipe_input) {
            case 'number':
                return is_numeric($value) && $value >= 0;
            case 'select':
                return $this->bahans->pluck('id')->contains($value);
            case 'text':
                return is_string($value) && !empty($value);
            default:
                return false;
        }
    }
}

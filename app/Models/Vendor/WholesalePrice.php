<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\Bahan;
use Illuminate\Database\Eloquent\Model;

class WholesalePrice extends TenantModel
{
    protected $table = 'harga_grosir';

    protected $fillable = [
        'vendor_id',
        'bahan_id',
        'min_quantity',
        'max_quantity',
        'harga'
    ];

    protected $casts = [
        'vendor_id' => 'integer',
        'bahan_id' => 'integer',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'harga' => 'decimal:2'
    ];

    /**
     * Relationships
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    /**
     * Get max quantity display text
     */
    public function getMaxQuantityDisplayAttribute(): string
    {
        return $this->max_quantity ?? 'Unlimited';
    }
}

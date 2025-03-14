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

    /**
     * Calculate the final price based on quantity and wholesale pricing tiers
     *
     * @param float $basePrice The base price (hpp)
     * @param int $quantity The quantity
     * @param int $bahanId The bahan ID to check for wholesale pricing
     * @return float The calculated final price per unit
     */
    public function calculateFinalPrice($basePrice, $quantity, $bahanId)
    {
        // Find applicable wholesale price tier
        $wholesalePricing = self::where('bahan_id', $bahanId)
            ->where('min_quantity', '<=', $quantity)
            ->where('max_quantity', '>=', $quantity)
            ->first();

        // If a wholesale price tier is found, use that price
        if ($wholesalePricing) {
            return $wholesalePricing->harga;
        }

        // Otherwise, return the base price
        return $basePrice;
    }
}

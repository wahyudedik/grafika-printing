<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use App\Models\Vendor\TenantModel;
use Illuminate\Support\Facades\Log;
use App\Models\Vendor\WholesalePrice;
use Illuminate\Database\Eloquent\Builder;

class Bahan extends TenantModel
{
    protected $table = 'bahans';

    protected $fillable = [
        'vendor_id',
        'nama_bahan',
        'hpp',
        'satuan',
        'stok',
        'minimum_stok',
        'maksimum_stok',
    ];

    protected $casts = [
        'hpp' => 'decimal:2',
        'stok' => 'integer',
        'minimum_stok' => 'integer',
        'maksimum_stok' => 'integer',
    ];

    /**
     * Relationships
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function wholesalePrices()
    {
        return $this->hasMany(WholesalePrice::class, 'bahan_id');
    }

    public function wholesalePrice()
    {
        return $this->wholesalePrices();
    }

    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class, 'bahan_id');
    }

    /**
     * Scopes
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('nama_bahan', 'like', "%{$search}%")
                    ->orWhere('satuan', 'like', "%{$search}%");
            });
        });
    }

    public function scopeStockFilter(Builder $query, ?string $stockStatus): Builder
    {
        return $query->when($stockStatus, function ($query, $stockStatus) {
            return match ($stockStatus) {
                'low' => $query->where('stok', '<', 10)->where('stok', '>', 0),
                'out' => $query->where('stok', '=', 0),
                'available' => $query->where('stok', '>', 0),
                default => $query
            };
        });
    }

    public function scopeWholesaleFilter(Builder $query, ?string $hasWholesale): Builder
    {
        return $query->when($hasWholesale, function ($query, $hasWholesale) {
            return match ($hasWholesale) {
                'yes' => $query->whereHas('wholesalePrices'),
                'no' => $query->whereDoesntHave('wholesalePrices'),
                default => $query
            };
        });
    }

    /**
     * Helper methods
     */

    /**
     * Get price for a given quantity, considering wholesale pricing tiers.
     *
     * @deprecated Gunakan PriceCalculationService::getPriceForQuantity() sebagai gantinya
     */
    public function getPriceForQuantity(int $quantity): float
    {
        $priceCalcService = app(\App\Services\PriceCalculationService::class);
        return $priceCalcService->getPriceForQuantity($this, $quantity);
    }

    public function getStockStatusAttribute(): string
    {
        $minimumStock = $this->minimum_stok ?? 5;

        if ($this->stok == 0) {
            return 'out';
        } elseif ($this->stok <= $minimumStock) {
            return 'low';
        } else {
            return 'available';
        }
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'out' => '<span class="badge bg-danger text-white">Habis</span>',
            'low' => '<span class="badge bg-warning text-white">Rendah (' . $this->stok . ')</span>',
            default => '<span class="badge bg-success text-white">' . $this->stok . '</span>',
        };
    }

    /**
     * Check if the stock level is low and update status accordingly.
     * Uses the configurable minimum_stok field from the database.
     *
     * @return void
     */
    public function checkStockLevel()
    {
        $minimumStock = $this->minimum_stok ?? 5;

        if ($this->stok <= $minimumStock) {
            Log::warning("Low stock alert: {$this->nama_bahan} (ID: {$this->id}) - Current stock: {$this->stok}, Minimum: {$minimumStock}");
        }
    }
}

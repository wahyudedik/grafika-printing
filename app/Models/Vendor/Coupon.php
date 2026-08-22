<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Builder;

class Coupon extends TenantModel
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_order',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_order' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class, 'vendor_id');
    }

    public function transaksiDiscounts()
    {
        return $this->hasMany(TransaksiDiscount::class, 'coupon_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        $now = now();
        return $query->active()
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            });
    }

    public function scopeExpired(Builder $query): Builder
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->where('expires_at', '<', $now)
              ->orWhere('is_active', false);
        });
    }

    // ─── Methods ─────────────────────────────────────────────────

    /**
     * Cek apakah kupon masih valid (active + within date range)
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah kupon masih bisa dipakai (valid + usage limit)
     */
    public function canBeUsed(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Hitung nominal diskon berdasarkan subtotal
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->minimum_order) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);
            if ($this->maximum_discount !== null) {
                $discount = min($discount, $this->maximum_discount);
            }
            return $discount;
        }

        // fixed
        return min($this->value, $subtotal);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}

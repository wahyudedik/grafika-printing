<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AdminFeeSetting extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_amount',
        'is_active',
        'category',
        'conditions',
        'effective_from',
        'effective_until',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'conditions' => 'array',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get active fee settings for a category
     */
    public static function getActiveSettings($category = 'auction')
    {
        $now = now();

        return static::where('category', $category)
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $now);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Calculate admin fee for given amount
     */
    public function calculateFee($amount)
    {
        if (!$this->is_active) {
            return 0;
        }

        // Check if amount is within minimum/maximum range
        if ($amount < $this->minimum_amount) {
            return 0;
        }

        if ($this->maximum_amount && $amount > $this->maximum_amount) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return $this->value;
        } elseif ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        }

        return 0;
    }

    /**
     * Check if setting is currently effective
     */
    public function isEffective()
    {
        $now = now();

        if (!$this->is_active) {
            return false;
        }

        if ($this->effective_from && $now < $this->effective_from) {
            return false;
        }

        if ($this->effective_until && $now > $this->effective_until) {
            return false;
        }

        return true;
    }

    /**
     * Get fee breakdown for given amount
     */
    public function getFeeBreakdown($amount)
    {
        $fee = $this->calculateFee($amount);

        return [
            'setting_id' => $this->id,
            'setting_name' => $this->name,
            'type' => $this->type,
            'value' => $this->value,
            'base_amount' => $amount,
            'fee_amount' => $fee,
            'percentage' => $this->type === 'percentage' ? $this->value : null,
            'is_effective' => $this->isEffective()
        ];
    }

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for effective settings
     */
    public function scopeEffective($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $now);
            });
    }

    /**
     * Scope for category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}

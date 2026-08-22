<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends TenantModel
{
    protected $table = 'stock_alerts';

    protected $fillable = [
        'bahan_id',
        'type',
        'previous_stock',
        'current_stock',
        'threshold',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'previous_stock' => 'integer',
        'current_stock' => 'integer',
        'threshold' => 'integer',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    /**
     * Scopes
     */

    /**
     * Filter unresolved (unread) alerts
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Filter resolved (read) alerts
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    /**
     * Filter by alert type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Mark this alert as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get type label in Indonesian
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'low_stock' => 'Stok Rendah',
            'out_of_stock' => 'Stok Habis',
            'restocked' => 'Stok Diisi Ulang',
            default => $this->type,
        };
    }

    /**
     * Get type color for Tailwind badge
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'low_stock' => 'warning',
            'out_of_stock' => 'danger',
            'restocked' => 'success',
            default => 'secondary',
        };
    }
}

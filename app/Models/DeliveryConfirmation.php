<?php

namespace App\Models;

use App\Models\User\UserTenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryConfirmation extends UserTenantModel
{
    protected $fillable = [
        'auction_id',
        'user_id',
        'vendor_id',
        'delivery_status',
        'delivery_date',
        'delivery_notes',
        'user_rating',
        'user_feedback',
        'photos',
        'confirmed_at',
        'dispute_reason',
        'dispute_resolved_at'
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'dispute_resolved_at' => 'datetime',
        'photos' => 'array'
    ];

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function isDelivered(): bool
    {
        return $this->delivery_status === 'delivered';
    }

    public function isConfirmed(): bool
    {
        return $this->delivery_status === 'confirmed';
    }

    public function hasDispute(): bool
    {
        return $this->delivery_status === 'disputed';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->delivery_status) {
            'pending' => 'Menunggu Konfirmasi',
            'delivered' => 'Barang Diterima',
            'confirmed' => 'Dikonfirmasi',
            'disputed' => 'Ada Masalah',
            'resolved' => 'Selesai',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->delivery_status) {
            'pending' => 'warning',
            'delivered' => 'info',
            'confirmed' => 'success',
            'disputed' => 'danger',
            'resolved' => 'primary',
            default => 'secondary'
        };
    }
}

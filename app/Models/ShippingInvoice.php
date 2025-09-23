<?php

namespace App\Models;

use App\Models\User\UserTenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingInvoice extends UserTenantModel
{
    protected $fillable = [
        'kode',
        'auction_id',
        'user_id',
        'vendor_id',
        'courier',
        'service',
        'waybill_number',
        'weight',
        'shipping_cost',
        'origin_city',
        'destination_city',
        'origin_address',
        'destination_address',
        'payment_status',
        'shipping_status',
        'shipped_at',
        'delivered_at',
        'tracking_data',
        'notes'
    ];

    protected $casts = [
        'tracking_data' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'weight' => 'decimal:2',
        'shipping_cost' => 'decimal:2'
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

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isShipped(): bool
    {
        return $this->shipping_status === 'shipped';
    }

    public function isDelivered(): bool
    {
        return $this->shipping_status === 'delivered';
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->shipping_cost, 0, ',', '.');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->shipping_status) {
            'pending' => 'bg-secondary',
            'processing' => 'bg-info',
            'shipped' => 'bg-primary',
            'delivered' => 'bg-success',
            'failed' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'bg-warning',
            'paid' => 'bg-success',
            'failed' => 'bg-danger',
            'expired' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }
}

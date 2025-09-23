<?php

namespace App\Models;

use App\Models\User\UserTenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderTracking extends UserTenantModel
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'vendor_id',
        'user_id',
        'status',
        'status_description',
        'tracking_number',
        'estimated_delivery',
        'actual_delivery',
        'notes',
        'admin_notes',
        'is_mediation_requested',
        'mediation_reason',
        'mediation_status',
        'mediation_resolution',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'estimated_delivery' => 'datetime',
        'actual_delivery' => 'datetime',
        'is_mediation_requested' => 'boolean'
    ];

    // Order Status Constants
    const STATUS_PAYMENT_RECEIVED = 'payment_received';
    const STATUS_ORDER_ACCEPTED = 'order_accepted';
    const STATUS_PRODUCTION_STARTED = 'production_started';
    const STATUS_PRODUCTION_COMPLETED = 'production_completed';
    const STATUS_QUALITY_CHECK = 'quality_check';
    const STATUS_PACKAGING = 'packaging';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_MEDIATION = 'mediation';

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_PAYMENT_RECEIVED => 'Payment Received',
            self::STATUS_ORDER_ACCEPTED => 'Order Accepted',
            self::STATUS_PRODUCTION_STARTED => 'Production Started',
            self::STATUS_PRODUCTION_COMPLETED => 'Production Completed',
            self::STATUS_QUALITY_CHECK => 'Quality Check',
            self::STATUS_PACKAGING => 'Packaging',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_MEDIATION => 'Mediation',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PAYMENT_RECEIVED => 'blue',
            self::STATUS_ORDER_ACCEPTED => 'green',
            self::STATUS_PRODUCTION_STARTED => 'yellow',
            self::STATUS_PRODUCTION_COMPLETED => 'orange',
            self::STATUS_QUALITY_CHECK => 'purple',
            self::STATUS_PACKAGING => 'indigo',
            self::STATUS_SHIPPED => 'teal',
            self::STATUS_DELIVERED => 'emerald',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_MEDIATION => 'red',
            default => 'gray'
        };
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isInMediation()
    {
        return $this->status === self::STATUS_MEDIATION;
    }

    public function canRequestMediation()
    {
        return in_array($this->status, [
            self::STATUS_PRODUCTION_STARTED,
            self::STATUS_PRODUCTION_COMPLETED,
            self::STATUS_QUALITY_CHECK,
            self::STATUS_PACKAGING
        ]);
    }
}

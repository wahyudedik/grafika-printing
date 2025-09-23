<?php

namespace App\Models;

use App\Models\User\UserTenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EscrowPayment extends UserTenantModel
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'vendor_id',
        'user_id',
        'amount',
        'admin_fee',
        'vendor_amount',
        'status',
        'released_at',
        'release_reason',
        'admin_notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'vendor_amount' => 'decimal:2',
        'released_at' => 'datetime'
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_RELEASED = 'released';
    const STATUS_WITHDRAWN = 'withdrawn';
    const STATUS_DISPUTED = 'disputed';
    const STATUS_REFUNDED = 'refunded';

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
            self::STATUS_PENDING => 'Pending Release',
            self::STATUS_RELEASED => 'Released to Vendor',
            self::STATUS_WITHDRAWN => 'Withdrawn by Vendor',
            self::STATUS_DISPUTED => 'Under Dispute',
            self::STATUS_REFUNDED => 'Refunded to User',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_RELEASED => 'green',
            self::STATUS_WITHDRAWN => 'blue',
            self::STATUS_DISPUTED => 'red',
            self::STATUS_REFUNDED => 'gray',
            default => 'gray'
        };
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isReleased()
    {
        return $this->status === self::STATUS_RELEASED;
    }

    public function isDisputed()
    {
        return $this->status === self::STATUS_DISPUTED;
    }

    public function release($reason = 'Delivery Confirmed')
    {
        $this->update([
            'status' => self::STATUS_RELEASED,
            'released_at' => now(),
            'release_reason' => $reason
        ]);

        // Add to vendor wallet
        $vendor = $this->vendor;
        $wallet = $vendor->getOrCreateWallet();
        $wallet->addCredit(
            $this->vendor_amount,
            'auction_payment',
            "Payment for auction: {$this->auction->title}",
            $this->auction->id,
            'auction'
        );
    }

    public function dispute($reason)
    {
        $this->update([
            'status' => self::STATUS_DISPUTED,
            'admin_notes' => $reason
        ]);
    }

    public function refund($reason)
    {
        $this->update([
            'status' => self::STATUS_REFUNDED,
            'admin_notes' => $reason
        ]);
    }
}

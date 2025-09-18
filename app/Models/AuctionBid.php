<?php

namespace App\Models;

use App\Models\Vendor\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionBid extends TenantModel
{
    protected $fillable = [
        'auction_id',
        'vendor_id',
        'bid_amount',
        'message',
        'status'
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2'
    ];

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}

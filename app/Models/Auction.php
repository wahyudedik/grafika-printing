<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'quantity',
        'budget',
        'deadline',
        'file_path',
        'status',
        'winner_vendor_id',
        'winning_bid',
        'specifications'
    ];

    protected $casts = [
        'deadline' => 'date',
        'budget' => 'decimal:2',
        'winning_bid' => 'decimal:2',
        'quantity' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function winnerVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'winner_vendor_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->deadline > now();
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed' || $this->deadline <= now();
    }

    public function getLowestBid()
    {
        return $this->bids()->where('status', 'pending')->min('bid_amount');
    }

    public function getBidCount(): int
    {
        return $this->bids()->where('status', 'pending')->count();
    }
}

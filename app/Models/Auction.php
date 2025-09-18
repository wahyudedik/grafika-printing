<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    protected $fillable = [
        'user_id',
        'kode',
        'title',
        'description',
        'category',
        'quantity',
        'budget',
        'admin_fee_amount',
        'payment_gateway_fee',
        'total_amount_with_fees',
        'vendor_receives',
        'admin_receives',
        'fee_breakdown',
        'fees_calculated',
        'deadline',
        'file_path',
        'status',
        'winner_vendor_id',
        'winning_bid',
        'specifications',
        'alamat_pengiriman',
        'no_telp',
        'email_pengiriman',
        'catatan_khusus',
        'metode_pembayaran',
        'estimasi_selesai',
        'progress_percentage',
        'catatan_vendor',
        'transaksi_id',
        'pos_integrated'
    ];

    protected $casts = [
        'deadline' => 'date',
        'budget' => 'decimal:2',
        'admin_fee_amount' => 'decimal:2',
        'payment_gateway_fee' => 'decimal:2',
        'total_amount_with_fees' => 'decimal:2',
        'vendor_receives' => 'decimal:2',
        'admin_receives' => 'decimal:2',
        'fee_breakdown' => 'array',
        'fees_calculated' => 'boolean',
        'winning_bid' => 'decimal:2',
        'quantity' => 'integer',
        'estimasi_selesai' => 'datetime',
        'progress_percentage' => 'integer',
        'pos_integrated' => 'boolean'
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

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Vendor\Transaksi::class, 'transaksi_id');
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

    /**
     * Relasi dengan XenditPayment
     */
    public function xenditPayments()
    {
        return $this->hasMany(XenditPayment::class);
    }

    /**
     * Get the latest payment for this auction
     */
    public function latestPayment()
    {
        return $this->hasOne(XenditPayment::class)->latest();
    }
}

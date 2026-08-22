<?php

namespace App\Models;

use App\Models\User\UserTenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class XenditPayment extends UserTenantModel
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'xendit_id',
        'amount',
        'description',
        'customer_name',
        'customer_email',
        'status',
        'checkout_url',
        'expires_at',
        'paid_at',
        'payment_method',
        'failure_reason',
        'auction_id',
        'transaksi_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800',
            'paid' => 'bg-emerald-100 text-emerald-800',
            'expired' => 'bg-red-100 text-red-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the status text
     */
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Berhasil Dibayar',
            'expired' => 'Kadaluarsa',
            'failed' => 'Gagal',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Check if payment is expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if payment is successful
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    /**
     * Get the auction that owns the payment
     */
    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    /**
     * Get the transaction that owns the payment
     */
    public function transaksi()
    {
        return $this->belongsTo(\App\Models\Vendor\Transaksi::class, 'transaksi_id');
    }
}

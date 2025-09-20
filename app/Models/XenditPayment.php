<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XenditPayment extends Model
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
        'failure_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'pending' => 'badge-warning',
            'paid' => 'badge-success',
            'expired' => 'badge-danger',
            'failed' => 'badge-danger',
            default => 'badge-secondary'
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class XenditPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'xendit_id',
        'type',
        'amount',
        'currency',
        'description',
        'status',
        'payment_method',
        'customer',
        'items',
        'fees',
        'checkout_url',
        'success_redirect_url',
        'failure_redirect_url',
        'expires_at',
        'paid_at',
        'webhook_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'customer' => 'array',
        'items' => 'array',
        'fees' => 'array',
        'webhook_data' => 'array',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Scope untuk payment yang pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk payment yang paid
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope untuk payment yang expired
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Check apakah payment sudah expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check apakah payment sudah paid
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check apakah payment masih pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}

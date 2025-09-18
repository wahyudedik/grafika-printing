<?php

namespace App\Models;

use App\Models\Vendor\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AdminFeeTransaction extends TenantModel
{
    protected $fillable = [
        'auction_id',
        'vendor_id',
        'user_id',
        'transaction_code',
        'auction_amount',
        'admin_fee_amount',
        'payment_gateway_fee',
        'total_amount',
        'vendor_receives',
        'admin_receives',
        'status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'fee_breakdown',
        'notes'
    ];

    protected $casts = [
        'auction_amount' => 'decimal:2',
        'admin_fee_amount' => 'decimal:2',
        'payment_gateway_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'vendor_receives' => 'decimal:2',
        'admin_receives' => 'decimal:2',
        'fee_breakdown' => 'array',
        'paid_at' => 'datetime'
    ];

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create admin fee transaction
     */
    public static function createTransaction($auctionId, $vendorId, $userId, $auctionAmount, $adminFeeAmount, $paymentGatewayFee = 0, $feeBreakdown = [])
    {
        $totalAmount = $auctionAmount + $adminFeeAmount + $paymentGatewayFee;
        $vendorReceives = $auctionAmount;
        $adminReceives = $adminFeeAmount + $paymentGatewayFee;

        return static::create([
            'auction_id' => $auctionId,
            'vendor_id' => $vendorId,
            'user_id' => $userId,
            'transaction_code' => 'AFT-' . Str::upper(Str::random(10)),
            'auction_amount' => $auctionAmount,
            'admin_fee_amount' => $adminFeeAmount,
            'payment_gateway_fee' => $paymentGatewayFee,
            'total_amount' => $totalAmount,
            'vendor_receives' => $vendorReceives,
            'admin_receives' => $adminReceives,
            'status' => 'pending',
            'fee_breakdown' => $feeBreakdown
        ]);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid($paymentMethod = null, $paymentReference = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
            'paid_at' => now()
        ]);

        return $this;
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($notes = null)
    {
        $this->update([
            'status' => 'failed',
            'notes' => $notes
        ]);

        return $this;
    }

    /**
     * Mark as refunded
     */
    public function markAsRefunded($notes = null)
    {
        $this->update([
            'status' => 'refunded',
            'notes' => $notes
        ]);

        return $this;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Dibayar',
            'failed' => 'Gagal',
            'refunded' => 'Dikembalikan'
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get fee breakdown summary
     */
    public function getFeeSummary()
    {
        return [
            'auction_amount' => $this->auction_amount,
            'admin_fee' => $this->admin_fee_amount,
            'payment_gateway_fee' => $this->payment_gateway_fee,
            'total_paid' => $this->total_amount,
            'vendor_receives' => $this->vendor_receives,
            'admin_receives' => $this->admin_receives,
            'fee_percentage' => $this->auction_amount > 0 ?
                round(($this->admin_fee_amount / $this->auction_amount) * 100, 2) : 0
        ];
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid transactions
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for refunded transactions
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Scope for vendor
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope for user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

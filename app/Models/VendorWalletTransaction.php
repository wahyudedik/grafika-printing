<?php

namespace App\Models;

use App\Models\Vendor\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorWalletTransaction extends TenantModel
{
    protected $fillable = [
        'vendor_wallet_id',
        'vendor_id',
        'transaction_code',
        'type',
        'category',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_id',
        'reference_type',
        'status',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array'
    ];

    public function vendorWallet(): BelongsTo
    {
        return $this->belongsTo(VendorWallet::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get formatted amount with sign
     */
    public function getFormattedAmountAttribute()
    {
        $sign = $this->type === 'credit' ? '+' : '-';
        return $sign . 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        $labels = [
            'auction_payment' => 'Pembayaran Lelang',
            'withdrawal' => 'Penarikan Dana',
            'refund' => 'Pengembalian Dana',
            'bonus' => 'Bonus',
            'adjustment' => 'Penyesuaian Saldo'
        ];

        return $labels[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu',
            'completed' => 'Selesai',
            'failed' => 'Gagal',
            'cancelled' => 'Dibatalkan'
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status color class
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}

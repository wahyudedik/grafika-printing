<?php

namespace App\Models\Vendor;

use App\Models\User;

class TransaksiDiscount extends TenantModel
{
    protected $fillable = [
        'transaksi_id',
        'coupon_id',
        'discount_code',
        'discount_type',
        'discount_amount',
        'description',
        'applied_by_user_id',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function appliedByUser()
    {
        return $this->belongsTo(User::class, 'applied_by_user_id');
    }
}

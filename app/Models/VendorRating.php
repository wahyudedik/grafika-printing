<?php

namespace App\Models;

use App\Models\User\UserTenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorRating extends UserTenantModel
{
    protected $fillable = [
        'vendor_id',
        'user_id',
        'auction_id',
        'transaksi_id',
        'rating',
        'comment',
        'rating_details',
        'is_verified'
    ];

    protected $casts = [
        'rating_details' => 'array',
        'is_verified' => 'boolean'
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Vendor\Transaksi::class);
    }

    /**
     * Get average rating for a vendor
     */
    public static function getAverageRating($vendorId)
    {
        return self::where('vendor_id', $vendorId)
            ->where('is_verified', true)
            ->avg('rating');
    }

    /**
     * Get total rating count for a vendor
     */
    public static function getRatingCount($vendorId)
    {
        return self::where('vendor_id', $vendorId)
            ->where('is_verified', true)
            ->count();
    }

    /**
     * Get rating distribution for a vendor
     */
    public static function getRatingDistribution($vendorId)
    {
        return self::where('vendor_id', $vendorId)
            ->where('is_verified', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();
    }
}

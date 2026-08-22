<?php

namespace App\Models\Vendor;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Transaksi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class TransactionReview extends TenantModel
{
    use HasFactory;

    protected $table = 'transaction_reviews';

    protected $fillable = [
        'vendor_id',
        'user_id',
        'transaksi_id',
        'rating',
        'comment',
        'quality_rating',
        'speed_rating',
        'service_rating',
    ];

    protected $casts = [
        'rating' => 'integer',
        'quality_rating' => 'integer',
        'speed_rating' => 'integer',
        'service_rating' => 'integer',
    ];

    /**
     * Get the vendor that owns the review.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the user who wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the transaction being reviewed.
     */
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    /**
     * Get the average rating for a vendor.
     *
     * @param int $vendorId
     * @return float|null
     */
    public static function getAverageRating($vendorId): ?float
    {
        return static::where('vendor_id', $vendorId)->avg('rating');
    }

    /**
     * Get the total number of reviews for a vendor.
     *
     * @param int $vendorId
     * @return int
     */
    public static function getRatingCount($vendorId): int
    {
        return (int) static::where('vendor_id', $vendorId)->count();
    }

    /**
     * Get the most recent reviews for a vendor.
     *
     * @param int $vendorId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentReviews($vendorId, $limit = 10)
    {
        return static::where('vendor_id', $vendorId)
            ->with(['user', 'transaksi'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Scope: filter reviews by transaction status.
     *
     * @param Builder $query
     * @param string $status
     * @return Builder
     */
    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->whereHas('transaksi', function (Builder $q) use ($status) {
            $q->where('status', $status);
        });
    }
}

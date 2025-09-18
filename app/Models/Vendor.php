<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vendor\Alat;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\WholesalePrice;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\SpesifikasiProduk;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Vendor\TransaksiItemSpecifications;
use App\Models\AuctionBid;
use App\Models\Auction;
use App\Models\VendorRating;
use App\Models\VendorWallet;
use App\Models\VendorWithdrawal;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'logo',
        'website',
        'is_active',
        'auto_withdrawal_enabled',
        'auto_withdrawal_date',
        'auto_withdrawal_amount',
        'auto_withdrawal_method',
        'auto_withdrawal_account_number',
        'auto_withdrawal_account_name',
        'auto_withdrawal_bank_name'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_withdrawal_enabled' => 'boolean',
        'auto_withdrawal_date' => 'integer',
        'auto_withdrawal_amount' => 'decimal:2'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Global scope to only show active vendors by default
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    /**
     * Scope a query to include inactive vendors.
     */
    public function scopeWithInactive(Builder $query): Builder
    {
        return $query->withoutGlobalScope('active');
    }

    /**
     * Scope a query to only include vendors associated with a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('vendorUser', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    /**
     * Scope a query to filter vendors by name.
     */
    public function scopeSearchByName(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Define relationship with vendors
     */
    public function vendorUser()
    {
        return $this->belongsToMany(User::class, 'vendor_user');
    }

    public function bahan()
    {
        return $this->hasMany(Bahan::class, 'vendor_id');
    }

    public function alat()
    {
        return $this->hasMany(Alat::class, 'vendor_id');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'vendor_id');
    }

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class, 'vendor_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'vendor_id');
    }

    public function wholesalePrice()
    {
        return $this->hasMany(WholesalePrice::class, 'vendor_id');
    }

    public function spesifikasiProduk()
    {
        return $this->hasMany(SpesifikasiProduk::class, 'vendor_id');
    }

    public function transaksiItem()
    {
        return $this->hasMany(TransaksiItem::class, 'vendor_id');
    }

    public function kategori()
    {
        return $this->hasMany(KategoriProduk::class, 'vendor_id');
    }

    public function spesifikasi()
    {
        return $this->hasMany(Spesifikasi::class, 'vendor_id');
    }

    public function estimasiProduk()
    {
        return $this->hasMany(EstimasiProduk::class, 'vendor_id');
    }

    public function transaksiItemSpecifications()
    {
        return $this->hasMany(TransaksiItemSpecifications::class, 'vendor_id');
    }

    /**
     * Get all auction bids for this vendor
     */
    public function auctionBids()
    {
        return $this->hasMany(AuctionBid::class, 'vendor_id');
    }

    /**
     * Get auctions where this vendor is the winner
     */
    public function wonAuctions()
    {
        return $this->hasMany(Auction::class, 'winner_vendor_id');
    }

    /**
     * Get all auctions where this vendor has participated (through bids)
     */
    public function auctions()
    {
        return $this->hasManyThrough(
            Auction::class,
            AuctionBid::class,
            'vendor_id', // Foreign key on auction_bids table
            'id', // Foreign key on auctions table
            'id', // Local key on vendors table
            'auction_id' // Local key on auction_bids table
        );
    }

    /**
     * Get completed auctions for this vendor
     */
    public function completedAuctions()
    {
        return $this->auctions()->where('auctions.status', 'completed');
    }

    /**
     * Get ratings for this vendor
     */
    public function ratings()
    {
        return $this->hasMany(VendorRating::class, 'vendor_id');
    }

    /**
     * Get average rating for this vendor
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->where('is_verified', true)->avg('rating') ?? 0;
    }

    /**
     * Get total rating count for this vendor
     */
    public function getRatingCountAttribute()
    {
        return $this->ratings()->where('is_verified', true)->count();
    }

    /**
     * Get verified ratings for this vendor
     */
    public function verifiedRatings()
    {
        return $this->ratings()->where('is_verified', true);
    }

    /**
     * Get vendor wallet
     */
    public function wallet()
    {
        return $this->hasOne(VendorWallet::class, 'vendor_id');
    }

    /**
     * Get or create wallet for this vendor
     */
    public function getOrCreateWallet()
    {
        return VendorWallet::getOrCreate($this->id);
    }

    /**
     * Get vendor withdrawals
     */
    public function withdrawals()
    {
        return $this->hasMany(VendorWithdrawal::class, 'vendor_id');
    }
}

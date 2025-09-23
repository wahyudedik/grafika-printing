<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasUuid;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'usertype', //dev, vendor, user
        'last_login_at',
        'uuid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define relationship with vendors
     */
    public function vendorUser()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_user');
    }

    public function auctions()
    {
        return $this->hasMany(Auction::class);
    }

    public function xenditPayments()
    {
        return $this->hasMany(XenditPayment::class);
    }

    public function deliveryConfirmations()
    {
        return $this->hasMany(DeliveryConfirmation::class);
    }

    public function shippingInvoices()
    {
        return $this->hasMany(ShippingInvoice::class);
    }

    public function vendorRatings()
    {
        return $this->hasMany(VendorRating::class);
    }

    /**
     * Scope a query to only include users of a specific type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('usertype', $type);
    }

    /**
     * Scope a query to only include users associated with a specific vendor.
     */
    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->whereHas('vendorUser', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        });
    }
}

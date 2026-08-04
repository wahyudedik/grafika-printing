<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LelangUserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'phone_number',
        'address',
        'city',
        'province',
        'postal_code',
        'status',
        'notes',
        'is_verified',
        'verified_at',
        'total_auctions',
        'total_won',
        'total_spent',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'total_spent' => 'decimal:2',
        'total_auctions' => 'integer',
        'total_won' => 'integer',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user's auctions.
     */
    public function auctions()
    {
        return $this->hasMany(Auction::class, 'user_id', 'user_id');
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'suspended' => 'Ditangguhkan',
            'pending' => 'Menunggu',
            default => 'Unknown',
        };
    }

    /**
     * Get status color for Tabler badge.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'suspended' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Check if profile is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if profile is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Verify the profile.
     */
    public function verify(int $adminId): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Suspend the profile.
     */
    public function suspend(string $reason = null): void
    {
        $this->update([
            'status' => 'suspended',
            'notes' => $reason,
        ]);
    }

    /**
     * Reactivate the profile.
     */
    public function reactivate(): void
    {
        $this->update([
            'status' => 'active',
        ]);
    }

    /**
     * Get win rate percentage.
     */
    public function getWinRateAttribute(): float
    {
        if ($this->total_auctions === 0) {
            return 0;
        }

        return round(($this->total_won / $this->total_auctions) * 100, 1);
    }

    /**
     * Get or create profile for a user.
     */
    public static function getOrCreate(int $userId): static
    {
        return static::firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Scope: only active profiles.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: only verified profiles.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: search by name or email.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        })->orWhere('company_name', 'like', "%{$search}%");
    }
}

<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Linktree extends TenantModel
{
    protected $fillable = [
        'title',
        'custom_url',
        'bio',
        'avatar',
        'banner',
        'template',
        'primary_color',
        'secondary_color',
        'bg_color',
        'text_color',
        'button_style',
        'is_active',
        'show_qris',
        'qris_image',
        'meta_title',
        'meta_description',
        'views_count',
        'clicks_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_qris' => 'boolean',
        'views_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    /**
     * Get the vendor that owns the linktree.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Vendor::class);
    }

    /**
     * Get the links for the linktree.
     */
    public function links(): HasMany
    {
        return $this->hasMany(LinktreeLink::class)->orderBy('sort_order');
    }

    /**
     * Get the active links for the linktree.
     */
    public function activeLinks(): HasMany
    {
        return $this->links()->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get the social media links for the linktree.
     */
    public function socials(): HasMany
    {
        return $this->hasMany(LinktreeSocial::class)->orderBy('sort_order');
    }

    /**
     * Get the active social media links.
     */
    public function activeSocials(): HasMany
    {
        return $this->socials()->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Increment views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment clicks count.
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
    }

    /**
     * Find linktree by custom URL.
     */
    public static function findByCustomUrl(string $customUrl): ?static
    {
        return static::where('custom_url', $customUrl)
            ->where('is_active', true)
            ->with(['activeLinks', 'activeSocials'])
            ->first();
    }

    /**
     * Get template CSS classes based on template type.
     */
    public function getTemplateClasses(): array
    {
        return match ($this->template) {
            'colorful' => [
                'bg' => 'bg-gradient-to-br from-purple-500 to-pink-500',
                'card' => 'bg-white/90 backdrop-blur',
                'button' => 'bg-gradient-to-r from-purple-500 to-pink-500 text-white',
            ],
            'dark' => [
                'bg' => 'bg-gray-900',
                'card' => 'bg-gray-800',
                'button' => 'bg-gray-700 text-white hover:bg-gray-600',
            ],
            'professional' => [
                'bg' => 'bg-slate-100',
                'card' => 'bg-white shadow-lg',
                'button' => 'bg-slate-800 text-white hover:bg-slate-700',
            ],
            default => [
                'bg' => 'bg-white',
                'card' => 'bg-white shadow-sm',
                'button' => 'border-2 border-gray-200 text-gray-700 hover:border-gray-400',
            ],
        };
    }
}

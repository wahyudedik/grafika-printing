<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CmsSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'label',
        'description',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get setting by key
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("cms_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->where('is_active', true)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value
     */
    public static function set($key, $value, $type = 'text', $category = 'general', $label = null, $description = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'category' => $category,
                'label' => $label ?: ucfirst(str_replace('_', ' ', $key)),
                'description' => $description,
                'is_active' => true
            ]
        );

        // Clear cache
        Cache::forget("cms_setting_{$key}");

        return $setting;
    }

    /**
     * Get settings by category
     */
    public static function getByCategory($category)
    {
        return static::where('category', $category)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get all settings grouped by category
     */
    public static function getAllGrouped()
    {
        return static::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');
    }

    /**
     * Clear all cache
     */
    public static function clearCache()
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("cms_setting_{$key}");
        }
    }

    /**
     * Get social media links
     */
    public static function getSocialMedia()
    {
        return static::where('category', 'social')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get contact information
     */
    public static function getContactInfo()
    {
        return static::where('category', 'contact')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get hero section settings
     */
    public static function getHeroSettings()
    {
        return static::where('category', 'hero')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get footer settings
     */
    public static function getFooterSettings()
    {
        return static::where('category', 'footer')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}

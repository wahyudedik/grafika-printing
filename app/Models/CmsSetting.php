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

    /**
     * Get all settings as key-value pairs
     */
    public static function getAllAsArray()
    {
        return static::where('is_active', true)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Get settings by multiple categories
     */
    public static function getByCategories(array $categories)
    {
        return static::whereIn('category', $categories)
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');
    }

    /**
     * Search settings by label or key
     */
    public static function search($query)
    {
        return static::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('label', 'like', "%{$query}%")
                    ->orWhere('key', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get settings statistics
     */
    public static function getStatistics()
    {
        return [
            'total' => static::count(),
            'active' => static::where('is_active', true)->count(),
            'inactive' => static::where('is_active', false)->count(),
            'by_category' => static::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category'),
            'by_type' => static::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
        ];
    }

    /**
     * Bulk update settings
     */
    public static function bulkUpdate(array $settings)
    {
        $updated = 0;

        foreach ($settings as $key => $value) {
            $setting = static::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
                $updated++;
            }
        }

        // Clear cache
        static::clearCache();

        return $updated;
    }

    /**
     * Get settings for specific page/section
     */
    public static function getForPage($page)
    {
        $pageSettings = [
            'home' => ['hero', 'general'],
            'about' => ['contact', 'general'],
            'contact' => ['contact', 'social'],
            'footer' => ['footer', 'social', 'contact']
        ];

        $categories = $pageSettings[$page] ?? ['general'];

        return static::getByCategories($categories);
    }

    /**
     * Validate setting value based on type
     */
    public function validateValue($value)
    {
        switch ($this->type) {
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            case 'phone':
                return preg_match('/^[\+]?[0-9\s\-\(\)]+$/', $value);
            case 'image':
                return file_exists(storage_path('app/public/' . $value));
            default:
                return true;
        }
    }

    /**
     * Get formatted value for display
     */
    public function getFormattedValue()
    {
        switch ($this->type) {
            case 'image':
                return $this->value ? asset('storage/' . $this->value) : null;
            case 'url':
                return $this->value ?: '#';
            case 'email':
                return $this->value ? 'mailto:' . $this->value : null;
            case 'phone':
                return $this->value ? 'tel:' . $this->value : null;
            default:
                return $this->value;
        }
    }
}

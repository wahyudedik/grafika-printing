<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class ServiceConfig extends Model
{
    protected $fillable = [
        'service_name',
        'key',
        'value',
        'label',
        'description',
        'is_active',
        'is_encrypted',
        'is_masked',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_encrypted' => 'boolean',
        'is_masked' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get config value by service and key
     */
    public static function getValue(string $serviceName, string $key, $default = null)
    {
        $cacheKey = "service_config_{$serviceName}_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($serviceName, $key, $default) {
            $config = static::where('service_name', $serviceName)
                ->where('key', $key)
                ->where('is_active', true)
                ->first();

            if (!$config) {
                return $default;
            }

            return $config->getDecryptedValue();
        });
    }

    /**
     * Set config value
     */
    public static function setValue(string $serviceName, string $key, $value, array $meta = []): static
    {
        $config = static::updateOrCreate(
            ['service_name' => $serviceName, 'key' => $key],
            array_merge([
                'value' => $value,
                'label' => $meta['label'] ?? ucfirst(str_replace('_', ' ', $key)),
                'description' => $meta['description'] ?? null,
                'is_active' => $meta['is_active'] ?? true,
                'is_encrypted' => $meta['is_encrypted'] ?? false,
                'is_masked' => $meta['is_masked'] ?? true,
                'sort_order' => $meta['sort_order'] ?? 0,
            ])
        );

        // Clear cache
        Cache::forget("service_config_{$serviceName}_{$key}");

        return $config;
    }

    /**
     * Get all configs grouped by service
     */
    public static function getGroupedByService()
    {
        return static::orderBy('service_name')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('service_name');
    }

    /**
     * Get all configs for a specific service
     */
    public static function forService(string $serviceName)
    {
        return static::where('service_name', $serviceName)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get decrypted value
     */
    public function getDecryptedValue()
    {
        if (empty($this->value)) {
            return null;
        }

        if ($this->is_encrypted) {
            try {
                return Crypt::decrypt($this->value);
            } catch (\Exception $e) {
                // If decryption fails, return raw value
                return $this->value;
            }
        }

        return $this->value;
    }

    /**
     * Get masked value for display
     */
    public function getMaskedValue(): string
    {
        $value = $this->getDecryptedValue();

        if (!$value) {
            return '(kosong)';
        }

        if ($this->is_masked && strlen($value) > 8) {
            return substr($value, 0, 4) . '****' . substr($value, -4);
        }

        return $value;
    }

    /**
     * Set value with optional encryption
     */
    public function setSecureValue($value): void
    {
        if ($this->is_encrypted && !empty($value)) {
            $this->value = Crypt::encrypt($value);
        } else {
            $this->value = $value;
        }

        $this->save();

        // Clear cache
        Cache::forget("service_config_{$this->service_name}_{$this->key}");
    }

    /**
     * Toggle active status
     */
    public function toggleActive(): bool
    {
        $this->update(['is_active' => !$this->is_active]);

        // Clear cache
        Cache::forget("service_config_{$this->service_name}_{$this->key}");

        return $this->is_active;
    }

    /**
     * Clear all cache for a service
     */
    public static function clearServiceCache(string $serviceName): void
    {
        $configs = static::where('service_name', $serviceName)->get();

        foreach ($configs as $config) {
            Cache::forget("service_config_{$config->service_name}_{$config->key}");
        }
    }

    /**
     * Clear all cache
     */
    public static function clearAllCache(): void
    {
        $keys = static::pluck('key');
        $services = static::pluck('service_name')->unique();

        foreach ($services as $service) {
            static::clearServiceCache($service);
        }
    }

    /**
     * Get available services
     */
    public static function getAvailableServices(): array
    {
        return [
            'xendit' => [
                'name' => 'Xendit',
                'description' => 'Payment Gateway - QRIS, Virtual Account, E-Wallet',
                'icon' => 'credit-card',
            ],
            'rajaongkir' => [
                'name' => 'RajaOngkir',
                'description' => 'Layanan Cek Ongkos Kirim & Tracking',
                'icon' => 'truck',
            ],
        ];
    }

    /**
     * Seed default configs from .env values
     */
    public static function seedDefaults(): int
    {
        $defaults = [
            'xendit' => [
                [
                    'key' => 'api_key',
                    'value' => env('XENDIT_API_KEY'),
                    'label' => 'API Key',
                    'description' => 'Xendit API Key untuk autentikasi',
                    'is_encrypted' => true,
                    'is_masked' => true,
                    'sort_order' => 1,
                ],
                [
                    'key' => 'public_key',
                    'value' => env('XENDIT_PUBLIC_KEY'),
                    'label' => 'Public Key',
                    'description' => 'Xendit Public Key untuk frontend',
                    'is_encrypted' => false,
                    'is_masked' => true,
                    'sort_order' => 2,
                ],
                [
                    'key' => 'webhook_token',
                    'value' => env('XENDIT_WEBHOOK_TOKEN'),
                    'label' => 'Webhook Token',
                    'description' => 'Token untuk validasi webhook dari Xendit',
                    'is_encrypted' => true,
                    'is_masked' => true,
                    'sort_order' => 3,
                ],
                [
                    'key' => 'base_url',
                    'value' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),
                    'label' => 'Base URL',
                    'description' => 'URL base API Xendit (production: https://api.xendit.co)',
                    'is_encrypted' => false,
                    'is_masked' => false,
                    'sort_order' => 4,
                ],
                [
                    'key' => 'redirect_url',
                    'value' => env('APP_URL'),
                    'label' => 'Redirect URL',
                    'description' => 'URL redirect setelah pembayaran',
                    'is_encrypted' => false,
                    'is_masked' => false,
                    'sort_order' => 5,
                ],
            ],
            'rajaongkir' => [
                [
                    'key' => 'api_key',
                    'value' => env('RAJAONGKIR_API_KEY'),
                    'label' => 'API Key',
                    'description' => 'RajaOngkir API Key untuk cek ongkir',
                    'is_encrypted' => true,
                    'is_masked' => true,
                    'sort_order' => 1,
                ],
                [
                    'key' => 'base_url',
                    'value' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
                    'label' => 'Base URL',
                    'description' => 'URL base API RajaOngkir',
                    'is_encrypted' => false,
                    'is_masked' => false,
                    'sort_order' => 2,
                ],
            ],
        ];

        $count = 0;

        foreach ($defaults as $service => $items) {
            foreach ($items as $index => $item) {
                // Skip if already exists
                $exists = static::where('service_name', $service)
                    ->where('key', $item['key'])
                    ->exists();

                if (!$exists && !empty($item['value'])) {
                    static::setValue($service, $item['key'], $item['value'], [
                        'label' => $item['label'],
                        'description' => $item['description'],
                        'is_encrypted' => $item['is_encrypted'],
                        'is_masked' => $item['is_masked'],
                        'sort_order' => $item['sort_order'],
                    ]);
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Get statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total' => static::count(),
            'active' => static::where('is_active', true)->count(),
            'inactive' => static::where('is_active', false)->count(),
            'encrypted' => static::where('is_encrypted', true)->count(),
            'by_service' => static::selectRaw('service_name, COUNT(*) as count')
                ->groupBy('service_name')
                ->pluck('count', 'service_name'),
        ];
    }
}

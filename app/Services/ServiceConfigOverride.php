<?php

namespace App\Services;

use App\Models\ServiceConfig;
use Illuminate\Support\Facades\Config;

class ServiceConfigOverride
{
    /**
     * Apply all service config overrides from database to runtime config
     * Call this in AppServiceProvider boot() or middleware
     */
    public static function applyAll(): void
    {
        $services = ServiceConfig::where('is_active', true)->get();

        foreach ($services as $config) {
            $value = $config->getDecryptedValue();

            if ($value !== null) {
                // Override Laravel config at runtime
                Config::set("services.{$config->service_name}.{$config->key}", $value);
            }
        }
    }

    /**
     * Apply overrides for a specific service
     */
    public static function applyFor(string $serviceName): void
    {
        $configs = ServiceConfig::where('service_name', $serviceName)
            ->where('is_active', true)
            ->get();

        foreach ($configs as $config) {
            $value = $config->getDecryptedValue();

            if ($value !== null) {
                Config::set("services.{$serviceName}.{$config->key}", $value);
            }
        }
    }

    /**
     * Get a config value (DB override first, then fallback to Laravel config)
     */
    public static function get(string $serviceName, string $key, $default = null)
    {
        // Try database first
        $dbValue = ServiceConfig::getValue($serviceName, $key);

        if ($dbValue !== null) {
            return $dbValue;
        }

        // Fallback to Laravel config
        return Config::get("services.{$serviceName}.{$key}", $default);
    }

    /**
     * Check if a service is properly configured
     */
    public static function isConfigured(string $serviceName): bool
    {
        $requiredKeys = match ($serviceName) {
            'xendit' => ['api_key'],
            'rajaongkir' => ['api_key'],
            default => [],
        };

        foreach ($requiredKeys as $key) {
            $value = static::get($serviceName, $key);

            if (empty($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Test connection for a service
     */
    public static function testConnection(string $serviceName): array
    {
        return match ($serviceName) {
            'xendit' => static::testXenditConnection(),
            'rajaongkir' => static::testRajaOngkirConnection(),
            default => [
                'success' => false,
                'message' => "Test connection tidak tersedia untuk service: {$serviceName}",
            ],
        };
    }

    /**
     * Test Xendit API connection
     */
    private static function testXenditConnection(): array
    {
        try {
            $apiKey = static::get('xendit', 'api_key');
            $baseUrl = static::get('xendit', 'base_url', 'https://api.xendit.co');

            if (empty($apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Xendit API Key belum dikonfigurasi',
                ];
            }

            $response = \Illuminate\Support\Facades\Http::withBasicAuth($apiKey, '')
                ->timeout(10)
                ->get($baseUrl . '/v2/invoices', [
                    'limit' => 1,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Koneksi Xendit berhasil! API Key valid.',
                    'status' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Koneksi Xendit gagal. Status: ' . $response->status(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error koneksi Xendit: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test RajaOngkir API connection
     */
    private static function testRajaOngkirConnection(): array
    {
        try {
            $apiKey = static::get('rajaongkir', 'api_key');
            $baseUrl = static::get('rajaongkir', 'base_url', 'https://rajaongkir.komerce.id/api/v1');

            if (empty($apiKey)) {
                return [
                    'success' => false,
                    'message' => 'RajaOngkir API Key belum dikonfigurasi',
                ];
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'key' => $apiKey,
            ])->timeout(10)
                ->get($baseUrl . '/city');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Koneksi RajaOngkir berhasil! API Key valid.',
                    'status' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Koneksi RajaOngkir gagal. Status: ' . $response->status(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error koneksi RajaOngkir: ' . $e->getMessage(),
            ];
        }
    }
}

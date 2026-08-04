<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        // Use ServiceConfigOverride to get values from DB first, fallback to .env config
        $this->apiKey = \App\Services\ServiceConfigOverride::get('rajaongkir', 'api_key') ?? config('services.rajaongkir.api_key');
        $this->baseUrl = \App\Services\ServiceConfigOverride::get('rajaongkir', 'base_url') ?? config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
    }

    /**
     * Calculate shipping cost
     */
    public function calculateShipping(array $data)
    {
        try {
            // Check if API key is configured
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'RajaOngkir API key tidak dikonfigurasi. Silakan gunakan input manual.',
                    'fallback' => true
                ];
            }

            $response = Http::withHeaders([
                'key' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->timeout(30)->post($this->baseUrl . '/calculate/domestic-cost', [
                'origin' => $data['origin'],
                'destination' => $data['destination'],
                'weight' => $data['weight'],
                'courier' => $data['courier'],
                'price' => 'lowest'
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Validate response structure
                if (
                    isset($responseData['rajaongkir']['results']) &&
                    count($responseData['rajaongkir']['results']) > 0
                ) {
                    return [
                        'success' => true,
                        'data' => $responseData
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Tidak ada layanan pengiriman tersedia untuk rute ini',
                        'fallback' => true
                    ];
                }
            } else {
                $errorBody = $response->body();
                $errorData = json_decode($errorBody, true);

                $errorMessage = 'Gagal menghitung ongkir';
                if (isset($errorData['meta']['message'])) {
                    $errorMessage = $errorData['meta']['message'];
                }

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'fallback' => true
                ];
            }
        } catch (\Exception $e) {
            Log::error('RajaOngkir API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung ongkir. Silakan gunakan input manual.',
                'fallback' => true
            ];
        }
    }

    /**
     * Track shipment
     */
    public function trackShipment(string $awb, string $courier)
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($this->baseUrl . '/track/waybill', [
                'awb' => $awb,
                'courier' => $courier
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal melacak pengiriman: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('RajaOngkir Tracking Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat melacak pengiriman'
            ];
        }
    }

    /**
     * Get available couriers
     */
    public function getAvailableCouriers()
    {
        return [
            'jne' => 'JNE',
            'tiki' => 'TIKI',
            'pos' => 'POS Indonesia',
            'jnt' => 'J&T Express',
            'sicepat' => 'SiCepat',
            'anteraja' => 'AnterAja',
            'lion' => 'Lion Parcel',
            'ninja' => 'Ninja Xpress'
        ];
    }

    /**
     * Get service types for courier
     */
    public function getServiceTypes(string $courier)
    {
        $services = [
            'jne' => ['REG', 'OKE', 'YES'],
            'tiki' => ['REG', 'ECO', 'ONS'],
            'pos' => ['REG', 'KILAT', 'EXPRESS'],
            'jnt' => ['REG', 'EZ', 'COCO'],
            'sicepat' => ['REG', 'BEST', 'EXPRESS'],
            'anteraja' => ['REG', 'EXPRESS'],
            'lion' => ['REG', 'EXPRESS'],
            'ninja' => ['REG', 'EXPRESS']
        ];

        return $services[$courier] ?? ['REG'];
    }
}

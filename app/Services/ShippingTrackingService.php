<?php

namespace App\Services;

use App\Models\ShippingInvoice;
use App\Models\Auction;
use App\Models\Vendor\Transaksi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingTrackingService
{
    protected $rajaOngkirService;
    protected $apiKey;
    protected $baseUrl;

    public function __construct(RajaOngkirService $rajaOngkirService)
    {
        $this->rajaOngkirService = $rajaOngkirService;
        $this->apiKey = config('services.rajaongkir.api_key');
        $this->baseUrl = config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
    }

    /**
     * Create shipping invoice for auction
     */
    public function createShippingInvoice(Auction $auction, array $shippingData)
    {
        try {
            // Generate unique code
            $kode = 'SHIP-' . date('Ymd') . '-' . strtoupper(substr(md5(time()), 0, 6));

            $shippingInvoice = ShippingInvoice::create([
                'kode' => $kode,
                'auction_id' => $auction->id,
                'user_id' => $auction->user_id,
                'vendor_id' => $auction->winner_vendor_id,
                'courier' => $shippingData['courier'],
                'service' => $shippingData['service'],
                'weight' => $shippingData['weight'],
                'shipping_cost' => $shippingData['shipping_cost'],
                'origin_city' => $shippingData['origin_city'],
                'destination_city' => $shippingData['destination_city'],
                'origin_address' => $shippingData['origin_address'],
                'destination_address' => $shippingData['destination_address'],
                'payment_status' => 'pending',
                'shipping_status' => 'pending',
                'notes' => $shippingData['notes'] ?? null
            ]);

            Log::info('Shipping invoice created', [
                'shipping_invoice_id' => $shippingInvoice->id,
                'auction_id' => $auction->id,
                'shipping_cost' => $shippingData['shipping_cost']
            ]);

            return $shippingInvoice;
        } catch (\Exception $e) {
            Log::error('Failed to create shipping invoice', [
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate shipping cost via RajaOngkir
     */
    public function calculateShippingCost(array $data)
    {
        try {
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

                if (isset($responseData['rajaongkir']['results'][0]['costs'])) {
                    $costs = $responseData['rajaongkir']['results'][0]['costs'];
                    $services = [];

                    foreach ($costs as $cost) {
                        $services[] = [
                            'service' => $cost['service'],
                            'description' => $cost['description'],
                            'cost' => $cost['cost'][0]['value'],
                            'etd' => $cost['cost'][0]['etd']
                        ];
                    }

                    return [
                        'success' => true,
                        'services' => $services,
                        'courier' => $responseData['rajaongkir']['results'][0]['code']
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Gagal menghitung ongkir via RajaOngkir',
                'fallback' => true
            ];
        } catch (\Exception $e) {
            Log::error('RajaOngkir API error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => 'Error menghitung ongkir: ' . $e->getMessage(),
                'fallback' => true
            ];
        }
    }

    /**
     * Track shipment via RajaOngkir
     */
    public function trackShipment(ShippingInvoice $shippingInvoice)
    {
        try {
            if (!$shippingInvoice->waybill_number) {
                return [
                    'success' => false,
                    'message' => 'Nomor resi belum tersedia'
                ];
            }

            $response = Http::withHeaders([
                'key' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->timeout(30)->post($this->baseUrl . '/track/waybill', [
                'waybill' => $shippingInvoice->waybill_number,
                'courier' => $shippingInvoice->courier
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['rajaongkir']['result'])) {
                    $trackingData = $responseData['rajaongkir']['result'];

                    // Update shipping status based on tracking
                    $this->updateShippingStatus($shippingInvoice, $trackingData);

                    return [
                        'success' => true,
                        'tracking_data' => $trackingData
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Gagal melacak pengiriman'
            ];
        } catch (\Exception $e) {
            Log::error('Tracking error', [
                'shipping_invoice_id' => $shippingInvoice->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error melacak pengiriman: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update shipping status based on tracking data
     */
    protected function updateShippingStatus(ShippingInvoice $shippingInvoice, array $trackingData)
    {
        try {
            $status = $trackingData['status'] ?? 'pending';
            $delivered = $trackingData['delivered'] ?? false;

            $shippingStatus = match ($status) {
                'PICKUP' => 'processing',
                'PICKUPED' => 'processing',
                'ON_PROCESS' => 'processing',
                'ON_DELIVERY' => 'shipped',
                'DELIVERED' => 'delivered',
                'RETURN' => 'failed',
                default => 'pending'
            };

            $updateData = [
                'shipping_status' => $shippingStatus,
                'tracking_data' => $trackingData
            ];

            if ($shippingStatus === 'shipped' && !$shippingInvoice->shipped_at) {
                $updateData['shipped_at'] = now();
            }

            if ($shippingStatus === 'delivered' && !$shippingInvoice->delivered_at) {
                $updateData['delivered_at'] = now();
            }

            $shippingInvoice->update($updateData);

            // Update auction transaction status
            if ($shippingStatus === 'delivered') {
                $this->updateAuctionTransactionStatus($shippingInvoice);
            }

            Log::info('Shipping status updated', [
                'shipping_invoice_id' => $shippingInvoice->id,
                'status' => $shippingStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update shipping status', [
                'shipping_invoice_id' => $shippingInvoice->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update auction transaction status when delivered
     */
    protected function updateAuctionTransactionStatus(ShippingInvoice $shippingInvoice)
    {
        try {
            $auction = $shippingInvoice->auction;
            if ($auction && $auction->transaksi) {
                $auction->transaksi->update([
                    'tracking_status' => 'selesai'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update auction transaction status', [
                'shipping_invoice_id' => $shippingInvoice->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get available couriers
     */
    public function getAvailableCouriers()
    {
        return [
            ['code' => 'jne', 'name' => 'JNE'],
            ['code' => 'tiki', 'name' => 'TIKI'],
            ['code' => 'pos', 'name' => 'POS Indonesia'],
            ['code' => 'jnt', 'name' => 'J&T Express'],
            ['code' => 'sicepat', 'name' => 'SiCepat'],
            ['code' => 'ninja', 'name' => 'Ninja Xpress'],
            ['code' => 'lion', 'name' => 'Lion Parcel'],
            ['code' => 'ide', 'name' => 'ID Express'],
            ['code' => 'sap', 'name' => 'SAP Express'],
            ['code' => 'jet', 'name' => 'JET Express'],
            ['code' => 'rex', 'name' => 'REX Express'],
            ['code' => 'first', 'name' => 'First Logistics'],
            ['code' => 'pcp', 'name' => 'PCP Express'],
            ['code' => 'esl', 'name' => 'ESL Express'],
            ['code' => 'ncs', 'name' => 'NCS Express'],
            ['code' => 'star', 'name' => 'Star Cargo'],
            ['code' => 'rpx', 'name' => 'RPX Express'],
            ['code' => 'pandu', 'name' => 'Pandu Logistics'],
            ['code' => 'wahana', 'name' => 'Wahana Express'],
            ['code' => 'pahala', 'name' => 'Pahala Express'],
            ['code' => 'cahaya', 'name' => 'Cahaya Express'],
            ['code' => 'sap', 'name' => 'SAP Express'],
            ['code' => 'jet', 'name' => 'JET Express'],
            ['code' => 'indah', 'name' => 'Indah Cargo'],
            ['code' => 'dse', 'name' => 'DSE Express'],
            ['code' => 'first', 'name' => 'First Logistics'],
            ['code' => 'ncs', 'name' => 'NCS Express'],
            ['code' => 'star', 'name' => 'Star Cargo'],
            ['code' => 'rpx', 'name' => 'RPX Express'],
            ['code' => 'pandu', 'name' => 'Pandu Logistics'],
            ['code' => 'wahana', 'name' => 'Wahana Express'],
            ['code' => 'pahala', 'name' => 'Pahala Express'],
            ['code' => 'cahaya', 'name' => 'Cahaya Express'],
            ['code' => 'sap', 'name' => 'SAP Express'],
            ['code' => 'jet', 'name' => 'JET Express'],
            ['code' => 'indah', 'name' => 'Indah Cargo'],
            ['code' => 'dse', 'name' => 'DSE Express']
        ];
    }
}

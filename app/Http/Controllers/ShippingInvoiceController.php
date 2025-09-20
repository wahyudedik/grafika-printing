<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Vendor\Transaksi;
use App\Services\XenditService;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShippingInvoiceMail;

class ShippingInvoiceController extends Controller
{
    protected $xenditService;
    protected $rajaOngkirService;

    public function __construct(XenditService $xenditService, RajaOngkirService $rajaOngkirService)
    {
        $this->xenditService = $xenditService;
        $this->rajaOngkirService = $rajaOngkirService;
    }

    /**
     * Generate shipping invoice for auction
     */
    public function generateShippingInvoice(Request $request, Auction $auction)
    {
        // Debug logging
        \Log::info('Shipping invoice request', [
            'auction_id' => $auction->id,
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $request->validate([
            'weight' => 'required|numeric|min:1',
            'destination_city' => 'required|string',
            'destination_address' => 'required|string',
            'courier' => 'required|string',
            'service' => 'required|string',
            'origin_city' => 'required|string',
            'origin_address' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
            // Get vendor info
            $vendor = $auction->winnerVendor;
            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor pemenang tidak ditemukan'
                ], 400);
            }

            // Calculate shipping cost via RajaOngkir API
            $shippingData = $this->rajaOngkirService->calculateShipping([
                'origin' => $vendor->city_id ?? '151', // Default Jakarta
                'destination' => $request->destination_city,
                'weight' => $request->weight,
                'courier' => $request->courier
            ]);

            if (!$shippingData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghitung ongkir: ' . $shippingData['message']
                ], 400);
            }

            // Create shipping invoice using ShippingTrackingService
            $shippingTrackingService = new \App\Services\ShippingTrackingService($this->rajaOngkirService);

            // Get shipping cost from calculation or use default
            $shippingCost = 0;
            if ($shippingData['success'] && isset($shippingData['services'])) {
                // Use the first service cost
                $shippingCost = $shippingData['services'][0]['cost'] ?? 25000;
            } else {
                // Default shipping cost if API fails
                $shippingCost = 25000;
            }

            $shippingInvoiceData = [
                'courier' => $request->courier,
                'service' => $request->service,
                'weight' => $request->weight,
                'shipping_cost' => $shippingCost,
                'origin_city' => $request->origin_city,
                'destination_city' => $request->destination_city,
                'origin_address' => $request->origin_address,
                'destination_address' => $request->destination_address,
                'notes' => $request->notes ?? null
            ];

            $shippingInvoice = $shippingTrackingService->createShippingInvoice($auction, $shippingInvoiceData);

            return response()->json([
                'success' => true,
                'message' => 'Shipping invoice berhasil dibuat',
                'shipping_invoice' => $shippingInvoice
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating shipping invoice', [
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat invoice pengiriman'
            ], 500);
        }
    }

    /**
     * Update shipping status
     */
    public function updateShippingStatus(Request $request, Auction $auction)
    {
        $request->validate([
            'shipping_status' => 'required|in:pending,processing,shipped,delivered,failed',
            'waybill_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $shippingInvoice = $auction->shippingInvoice;
            if (!$shippingInvoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipping invoice tidak ditemukan'
                ], 400);
            }

            $shippingInvoice->update([
                'shipping_status' => $request->shipping_status,
                'waybill_number' => $request->waybill_number,
                'notes' => $request->notes
            ]);

            // Update auction transaction status
            if ($auction->transaksi) {
                $trackingStatus = match ($request->shipping_status) {
                    'pending' => 'menunggu',
                    'processing' => 'diproses',
                    'shipped' => 'dikirim',
                    'delivered' => 'selesai',
                    'failed' => 'gagal',
                    default => 'menunggu'
                };

                $auction->transaksi->update([
                    'tracking_status' => $trackingStatus
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status pengiriman berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating shipping status', [
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status pengiriman'
            ], 500);
        }
    }

    /**
     * Track shipment
     */
    public function trackShipment(Auction $auction)
    {
        try {
            $shippingInvoice = $auction->shippingInvoice;
            if (!$shippingInvoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipping invoice tidak ditemukan'
                ], 400);
            }

            $shippingTrackingService = new \App\Services\ShippingTrackingService($this->rajaOngkirService);
            $trackingResult = $shippingTrackingService->trackShipment($shippingInvoice);

            return response()->json($trackingResult);
        } catch (\Exception $e) {
            Log::error('Error tracking shipment', [
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melacak pengiriman'
            ], 500);
        }
    }

    /**
     * Handle COD payment completion
     */
    public function handleCODPayment(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,app',
            'amount_paid' => 'required|numeric|min:0',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        try {
            if ($request->payment_method === 'cash') {
                // Cash payment - just update status
                $transaksi->update([
                    'shipping_payment_status' => 'paid_cash',
                    'shipping_payment_date' => now(),
                    'tracking_status' => 'dikirim'
                ]);

                Log::info('COD cash payment completed', [
                    'transaction_id' => $transaksi->id,
                    'amount' => $request->amount_paid
                ]);
            } else {
                // App payment - verify with Xendit
                $paymentStatus = $this->xenditService->getPaymentLink($transaksi->shipping_payment_id);

                if ($paymentStatus && $paymentStatus['status'] === 'PAID') {
                    $transaksi->update([
                        'shipping_payment_status' => 'paid_app',
                        'shipping_payment_date' => now(),
                        'tracking_status' => 'dikirim'
                    ]);

                    Log::info('COD app payment completed', [
                        'transaction_id' => $transaksi->id,
                        'payment_id' => $transaksi->shipping_payment_id
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pembayaran belum dikonfirmasi'
                    ], 400);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran COD berhasil dikonfirmasi'
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling COD payment', [
                'transaction_id' => $transaksi->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran COD'
            ], 500);
        }
    }

    /**
     * Get shipping cost calculation
     */
    public function calculateShippingCost(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'weight' => 'required|numeric|min:1',
            'courier' => 'required|string'
        ]);

        try {
            $shippingData = $this->rajaOngkirService->calculateShipping([
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier
            ]);

            return response()->json($shippingData);
        } catch (\Exception $e) {
            Log::error('Error calculating shipping cost', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung ongkir'
            ], 500);
        }
    }
}

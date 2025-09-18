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
     * Generate shipping invoice for COD payment
     */
    public function generateShippingInvoice(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:1',
            'destination_city' => 'required|string',
            'destination_address' => 'required|string',
            'courier' => 'required|string',
            'service_type' => 'required|string'
        ]);

        try {
            // Calculate shipping cost via RajaOngkir API
            $shippingData = $this->rajaOngkirService->calculateShipping([
                'origin' => $transaksi->vendor->city_id ?? '151', // Default Jakarta
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

            // Get the lowest cost from RajaOngkir
            $calculatedCost = $shippingData['data']['costs'][0]['cost'][0]['value'] ?? $request->shipping_cost;

            // Validate cost difference (prevent fraud)
            $costDifference = abs($calculatedCost - $request->shipping_cost);
            $maxDifference = $calculatedCost * 0.1; // 10% tolerance

            if ($costDifference > $maxDifference) {
                Log::warning('Shipping cost mismatch detected', [
                    'calculated_cost' => $calculatedCost,
                    'vendor_input' => $request->shipping_cost,
                    'difference' => $costDifference,
                    'max_difference' => $maxDifference,
                    'transaction_id' => $transaksi->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Biaya pengiriman tidak sesuai dengan perhitungan sistem. Silakan periksa kembali.',
                    'calculated_cost' => $calculatedCost,
                    'vendor_input' => $request->shipping_cost
                ], 400);
            }

            // Update transaction with shipping details
            $transaksi->update([
                'ongkir' => $calculatedCost,
                'kurir' => $request->courier,
                'alamat_pengiriman' => $request->destination_address,
                'is_cod' => true,
                'tracking_status' => 'dikirim'
            ]);

            // Create shipping invoice
            $invoiceData = [
                'external_id' => 'shipping_' . $transaksi->id . '_' . time(),
                'amount' => $calculatedCost,
                'description' => 'Biaya Pengiriman - ' . $transaksi->kode,
                'customer' => [
                    'given_names' => $transaksi->pelanggan->nama,
                    'email' => $transaksi->pelanggan->email
                ],
                'success_redirect_url' => route('user.tracking.show', $transaksi->auction),
                'failure_redirect_url' => route('user.tracking.show', $transaksi->auction),
                'items' => [
                    [
                        'name' => 'Biaya Pengiriman',
                        'quantity' => 1,
                        'price' => $calculatedCost,
                        'category' => 'Shipping'
                    ]
                ]
            ];

            // Create Xendit payment link
            $paymentLink = $this->xenditService->createPaymentLink($invoiceData);

            if (!$paymentLink) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat invoice pengiriman'
                ], 500);
            }

            // Store payment link in transaction
            $transaksi->update([
                'shipping_payment_link' => $paymentLink['invoice_url'],
                'shipping_payment_id' => $paymentLink['id']
            ]);

            // Send email notification to user
            Mail::to($transaksi->pelanggan->email)->send(new ShippingInvoiceMail($transaksi, $paymentLink));

            Log::info('Shipping invoice generated successfully', [
                'transaction_id' => $transaksi->id,
                'shipping_cost' => $calculatedCost,
                'payment_link' => $paymentLink['invoice_url']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice pengiriman berhasil dibuat',
                'payment_link' => $paymentLink['invoice_url'],
                'shipping_cost' => $calculatedCost
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating shipping invoice', [
                'transaction_id' => $transaksi->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat invoice pengiriman'
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

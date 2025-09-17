<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected $apiKey;
    protected $baseUrl;
    protected $publicKey;
    protected $webhookToken;

    public function __construct()
    {
        $this->apiKey = config('services.xendit.api_key');
        $this->baseUrl = config('services.xendit.base_url');
        $this->publicKey = config('services.xendit.public_key');
        $this->webhookToken = config('services.xendit.webhook_token');
    }

    /**
     * Create a payment link
     */
    public function createPaymentLink(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v2/payment-links', [
                'external_id' => $data['external_id'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'invoice_duration' => $data['invoice_duration'] ?? 86400, // 24 hours default
                'customer' => $data['customer'] ?? null,
                'customer_notification_preference' => [
                    'invoice_created' => ['email'],
                    'invoice_reminder' => ['email'],
                    'invoice_paid' => ['email'],
                    'invoice_expired' => ['email']
                ],
                'success_redirect_url' => $data['success_redirect_url'] ?? null,
                'failure_redirect_url' => $data['failure_redirect_url'] ?? null,
                'payment_methods' => $data['payment_methods'] ?? [
                    'BCA',
                    'BNI',
                    'BRI',
                    'BSI',
                    'MANDIRI',
                    'PERMATA',
                    'ALFAMART',
                    'INDOMARET',
                    'OVO',
                    'DANA',
                    'LINKAJA',
                    'SHOPEEPAY'
                ],
                'currency' => $data['currency'] ?? 'IDR',
                'items' => $data['items'] ?? null,
                'fees' => $data['fees'] ?? null,
                'reminder_time' => $data['reminder_time'] ?? 1,
                'locale' => $data['locale'] ?? 'id'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit Payment Link Creation Failed', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit Service Error', [
                'message' => $e->getMessage(),
                'data' => $data
            ]);
            return null;
        }
    }

    /**
     * Create xenPayment widget
     */
    public function createXenPayment(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v2/xenpayments', [
                'external_id' => $data['external_id'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'currency' => $data['currency'] ?? 'IDR',
                'customer' => $data['customer'] ?? null,
                'payment_methods' => $data['payment_methods'] ?? [
                    'BCA',
                    'BNI',
                    'BRI',
                    'BSI',
                    'MANDIRI',
                    'PERMATA',
                    'ALFAMART',
                    'INDOMARET',
                    'OVO',
                    'DANA',
                    'LINKAJA',
                    'SHOPEEPAY'
                ],
                'items' => $data['items'] ?? null,
                'fees' => $data['fees'] ?? null,
                'locale' => $data['locale'] ?? 'id'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit XenPayment Creation Failed', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit XenPayment Service Error', [
                'message' => $e->getMessage(),
                'data' => $data
            ]);
            return null;
        }
    }

    /**
     * Get payment link by ID
     */
    public function getPaymentLink($paymentLinkId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            ])->get($this->baseUrl . '/v2/payment-links/' . $paymentLinkId);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit Get Payment Link Error', [
                'message' => $e->getMessage(),
                'payment_link_id' => $paymentLinkId
            ]);
            return null;
        }
    }

    /**
     * Get xenPayment by ID
     */
    public function getXenPayment($xenPaymentId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            ])->get($this->baseUrl . '/v2/xenpayments/' . $xenPaymentId);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit Get XenPayment Error', [
                'message' => $e->getMessage(),
                'xen_payment_id' => $xenPaymentId
            ]);
            return null;
        }
    }

    /**
     * Expire payment link
     */
    public function expirePaymentLink($paymentLinkId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            ])->post($this->baseUrl . '/v2/payment-links/' . $paymentLinkId . '/expire');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit Expire Payment Link Error', [
                'message' => $e->getMessage(),
                'payment_link_id' => $paymentLinkId
            ]);
            return null;
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookToken);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods()
    {
        return [
            'bank_transfer' => [
                'BCA',
                'BNI',
                'BRI',
                'BSI',
                'MANDIRI',
                'PERMATA'
            ],
            'ewallet' => [
                'OVO',
                'DANA',
                'LINKAJA',
                'SHOPEEPAY'
            ],
            'retail_outlet' => [
                'ALFAMART',
                'INDOMARET'
            ],
            'paylater' => [
                'KREDIVO',
                'AKULAKU'
            ],
            'qr_code' => [
                'QRIS'
            ]
        ];
    }
}

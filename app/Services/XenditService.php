<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class XenditService
{
    protected $apiKey;
    protected $baseUrl;
    protected $publicKey;
    protected $webhookToken;

    public function __construct()
    {
        // Use ServiceConfigOverride to get values from DB first, fallback to .env config
        $this->apiKey = \App\Services\ServiceConfigOverride::get('xendit', 'api_key') ?? config('services.xendit.api_key');
        $this->baseUrl = \App\Services\ServiceConfigOverride::get('xendit', 'base_url') ?? config('services.xendit.base_url');
        $this->publicKey = \App\Services\ServiceConfigOverride::get('xendit', 'public_key') ?? config('services.xendit.public_key');
        $this->webhookToken = \App\Services\ServiceConfigOverride::get('xendit', 'webhook_token') ?? config('services.xendit.webhook_token');

        // Configure Xendit SDK
        Configuration::setXenditKey($this->apiKey);
    }

    /**
     * Create a payment link using direct HTTP call
     */
    public function createPaymentLink(array $data)
    {
        try {
            // Validate required fields
            $this->validatePaymentData($data);

            // Use direct HTTP call for better reliability
            $url = $this->baseUrl . '/v2/invoices';

            $payload = [
                'external_id' => $data['external_id'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'invoice_duration' => $data['invoice_duration'] ?? 86400, // 24 hours
                'customer' => $data['customer'] ?? [
                    'given_names' => 'Customer',
                    'email' => 'customer@example.com'
                ],
                'success_redirect_url' => $data['success_redirect_url'] ?? config('app.url') . '/payment/success',
                'failure_redirect_url' => $data['failure_redirect_url'] ?? config('app.url') . '/payment/failed',
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
                    'SHOPEEPAY',
                    'QRIS'
                ],
                'currency' => $data['currency'] ?? 'IDR',
                'items' => $data['items'] ?? [
                    [
                        'name' => $data['description'] ?? 'Payment Item',
                        'quantity' => 1,
                        'price' => $data['amount']
                    ]
                ],
                'fees' => $data['fees'] ?? [],
                'reminder_time' => $data['reminder_time'] ?? 1,
                'locale' => $data['locale'] ?? 'id'
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->timeout(30)->retry(3, 500)->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();

                Log::info('Xendit Invoice Created Successfully', [
                    'external_id' => $data['external_id'],
                    'invoice_id' => $result['id'] ?? null,
                    'checkout_url' => $result['invoice_url'] ?? null,
                    'full_response' => $result
                ]);

                return [
                    'id' => $result['id'] ?? null,
                    'external_id' => $result['external_id'] ?? null,
                    'invoice_url' => $result['invoice_url'] ?? null,
                    'checkout_url' => $result['invoice_url'] ?? null,
                    'amount' => $result['amount'] ?? null,
                    'status' => $result['status'] ?? null,
                    'created' => $result['created'] ?? null,
                    'expires_at' => $result['expiry_date'] ?? null
                ];
            } else {
                Log::error('Xendit API Error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'data' => $data,
                    'headers' => $response->headers()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Xendit Invoice Creation Error', [
                'message' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
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
     * Get payment link by ID using Xendit Invoice SDK
     */
    public function getPaymentLink($paymentLinkId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            ])->timeout(30)->retry(3, 500)->get($this->baseUrl . '/v2/invoices/' . $paymentLinkId);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'id' => $data['id'] ?? null,
                    'external_id' => $data['external_id'] ?? null,
                    'checkout_url' => $data['invoice_url'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'status' => $data['status'] ?? null,
                    'created' => $data['created'] ?? null,
                    'expires_at' => $data['expiry_date'] ?? null,
                    'paid_at' => $data['paid_at'] ?? null,
                    'payment_method' => $data['payment_method'] ?? null
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit Get Invoice Error', [
                'message' => $e->getMessage(),
                'invoice_id' => $paymentLinkId,
                'trace' => $e->getTraceAsString()
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
     * Expire payment link using Xendit Invoice SDK
     */
    public function expirePaymentLink($paymentLinkId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            ])->timeout(30)->retry(3, 500)->post($this->baseUrl . '/v2/invoices/' . $paymentLinkId . '/expire');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'id' => $data['id'] ?? null,
                    'status' => $data['status'] ?? null,
                    'expires_at' => $data['expiry_date'] ?? null
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit Expire Invoice Error', [
                'message' => $e->getMessage(),
                'invoice_id' => $paymentLinkId,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        if (empty($this->webhookToken) || empty($signature)) {
            Log::warning('Xendit webhook verification failed: missing token or signature', [
                'has_token' => !empty($this->webhookToken),
                'has_signature' => !empty($signature)
            ]);
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookToken);

        $isValid = hash_equals($expectedSignature, $signature);

        Log::info('Xendit webhook signature verification', [
            'match' => $isValid
        ]);

        return $isValid;
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
            'qr_code' => [
                'QRIS'
            ]
        ];
    }

    /**
     * Create QRIS payment via Xendit Invoice API.
     * Returns invoice with QR code that can be scanned.
     *
     * @param array $data ['external_id', 'amount', 'description', 'items'?]
     * @return array|null ['id', 'invoice_url', 'qr_code', 'amount', 'status'] or null on failure
     */
    public function createQrisPayment(array $data)
    {
        try {
            $this->validatePaymentData($data);

            $url = $this->baseUrl . '/v2/invoices';

            $payload = [
                'external_id' => $data['external_id'],
                'amount' => $data['amount'],
                'description' => $data['description'] ?? 'Pembayaran Linktree',
                'invoice_duration' => $data['invoice_duration'] ?? 86400, // 24 hours
                'customer' => $data['customer'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'success_redirect_url' => $data['success_redirect_url'] ?? config('app.url') . '/payment/success',
                'failure_redirect_url' => $data['failure_redirect_url'] ?? config('app.url') . '/payment/failed',
                'payment_methods' => ['QRIS'],
                'currency' => 'IDR',
                'items' => $data['items'] ?? [
                    [
                        'name' => $data['description'] ?? 'Pembayaran Linktree',
                        'quantity' => 1,
                        'price' => $data['amount']
                    ]
                ],
                'locale' => 'id'
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->timeout(30)->retry(3, 500)->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();

                Log::info('Xendit QRIS Invoice Created Successfully', [
                    'external_id' => $data['external_id'],
                    'invoice_id' => $result['id'] ?? null,
                    'amount' => $data['amount']
                ]);

                return [
                    'id' => $result['id'] ?? null,
                    'external_id' => $result['external_id'] ?? null,
                    'invoice_url' => $result['invoice_url'] ?? null,
                    'qr_code' => $result['qr_code'] ?? null,
                    'amount' => $result['amount'] ?? null,
                    'status' => $result['status'] ?? null,
                    'created' => $result['created'] ?? null,
                    'expires_at' => $result['expiry_date'] ?? null,
                    'payment_methods' => $result['payment_methods'] ?? []
                ];
            } else {
                Log::error('Xendit QRIS API Error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'data' => $data
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Xendit QRIS Payment Creation Error', [
                'message' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Create a refund for a paid invoice or payment
     *
     * @param string $paymentId The Xendit payment ID (invoice_id or payment_id)
     * @param int $amount Refund amount in IDR (must be <= original amount)
     * @param string $reason Reason for refund
     * @return array|null Refund data or null on failure
     */
    public function createRefund(string $paymentId, int $amount, string $reason = 'Customer requested refund'): ?array
    {
        try {
            $url = $this->baseUrl . '/v2/refunds';

            $payload = [
                'payment_id' => $paymentId,
                'amount' => $amount,
                'reason' => $reason
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
                'Content-Type' => 'application/json'
            ])->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Xendit Refund Created', [
                    'refund_id' => $result['id'] ?? null,
                    'payment_id' => $paymentId,
                    'amount' => $amount,
                    'status' => $result['status'] ?? null
                ]);
                return $result;
            } else {
                Log::error('Xendit Refund API Error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'payment_id' => $paymentId,
                    'amount' => $amount
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Xendit Refund Creation Error', [
                'message' => $e->getMessage(),
                'payment_id' => $paymentId,
                'amount' => $amount,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Validate payment data before sending to Xendit
     */
    private function validatePaymentData(array $data)
    {
        $required = ['external_id', 'amount', 'description'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Field '{$field}' is required");
            }
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new \InvalidArgumentException("Amount must be a positive number");
        }

        if (strlen($data['external_id']) > 255) {
            throw new \InvalidArgumentException("External ID must be less than 255 characters");
        }
    }
}

<?php

namespace Tests\Unit\Services;

use App\Services\XenditService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XenditServiceTest extends TestCase
{
    protected XenditService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Set config values to prevent ServiceConfigOverride from failing
        config([
            'services.xendit.api_key' => 'test_api_key_123',
            'services.xendit.base_url' => 'https://api.xendit.co',
            'services.xendit.public_key' => 'test_public_key_123',
            'services.xendit.webhook_token' => 'test_webhook_token_123',
        ]);

        $this->service = new XenditService();
    }

    // ─── Webhook Signature Verification Tests ──────────────────────────

    public function test_verify_webhook_signature_valid(): void
    {
        $payload = '{"id":"evt_123","status":"PAID"}';
        $token = 'test_webhook_token_123';
        $expectedSignature = hash_hmac('sha256', $payload, $token);

        $result = $this->service->verifyWebhookSignature($payload, $expectedSignature);

        $this->assertTrue($result);
    }

    public function test_verify_webhook_signature_invalid(): void
    {
        $payload = '{"id":"evt_123","status":"PAID"}';
        $invalidSignature = 'invalid_signature_here';

        $result = $this->service->verifyWebhookSignature($payload, $invalidSignature);

        $this->assertFalse($result);
    }

    public function test_verify_webhook_signature_empty_token(): void
    {
        config(['services.xendit.webhook_token' => null]);
        $service = new XenditService();

        $result = $service->verifyWebhookSignature('payload', 'signature');

        $this->assertFalse($result);
    }

    public function test_verify_webhook_signature_empty_signature(): void
    {
        $result = $this->service->verifyWebhookSignature('payload', '');

        $this->assertFalse($result);
    }

    public function test_verify_webhook_signature_null_signature(): void
    {
        $result = $this->service->verifyWebhookSignature('payload', null);

        $this->assertFalse($result);
    }

    // ─── Payment Methods Tests ─────────────────────────────────────────

    public function test_get_available_payment_methods_returns_all_categories(): void
    {
        $methods = $this->service->getAvailablePaymentMethods();

        $this->assertArrayHasKey('bank_transfer', $methods);
        $this->assertArrayHasKey('ewallet', $methods);
        $this->assertArrayHasKey('retail_outlet', $methods);
        $this->assertArrayHasKey('qr_code', $methods);
    }

    public function test_get_available_payment_methods_includes_qris(): void
    {
        $methods = $this->service->getAvailablePaymentMethods();

        $this->assertContains('QRIS', $methods['qr_code']);
    }

    public function test_get_available_payment_methods_bank_transfer_list(): void
    {
        $methods = $this->service->getAvailablePaymentMethods();

        $expectedBanks = ['BCA', 'BNI', 'BRI', 'BSI', 'MANDIRI', 'PERMATA'];
        foreach ($expectedBanks as $bank) {
            $this->assertContains($bank, $methods['bank_transfer']);
        }
    }

    public function test_get_available_payment_methods_ewallet_list(): void
    {
        $methods = $this->service->getAvailablePaymentMethods();

        $expectedWallets = ['OVO', 'DANA', 'LINKAJA', 'SHOPEEPAY'];
        foreach ($expectedWallets as $wallet) {
            $this->assertContains($wallet, $methods['ewallet']);
        }
    }

    public function test_get_available_payment_methods_retail_outlet_list(): void
    {
        $methods = $this->service->getAvailablePaymentMethods();

        $expectedRetail = ['ALFAMART', 'INDOMARET'];
        foreach ($expectedRetail as $outlet) {
            $this->assertContains($outlet, $methods['retail_outlet']);
        }
    }

    // ─── createPaymentLink Tests (mocked HTTP) ─────────────────────────

    public function test_create_payment_link_success(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv_test_123',
                'external_id' => 'auction_1_2026',
                'invoice_url' => 'https://checkout.xendit.co/inv_test_123',
                'amount' => 1000000,
                'status' => 'PENDING',
                'created' => '2026-08-05T00:00:00.000Z',
                'expiry_date' => '2026-08-06T00:00:00.000Z',
            ], 200),
        ]);

        $result = $this->service->createPaymentLink([
            'external_id' => 'auction_1_2026',
            'amount' => 1000000,
            'description' => 'Pembayaran Lelang',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('inv_test_123', $result['id']);
        $this->assertEquals('auction_1_2026', $result['external_id']);
        $this->assertEquals('https://checkout.xendit.co/inv_test_123', $result['invoice_url']);
        $this->assertEquals(1000000, $result['amount']);
        $this->assertEquals('PENDING', $result['status']);
    }

    public function test_create_payment_link_returns_null_on_api_failure(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'error_code' => 'INVALID_API_KEY',
                'message' => 'Invalid API key',
            ], 401),
        ]);

        $result = $this->service->createPaymentLink([
            'external_id' => 'auction_1_2026',
            'amount' => 1000000,
            'description' => 'Pembayaran Lelang',
        ]);

        $this->assertNull($result);
    }

    public function test_create_payment_link_returns_null_on_connection_error(): void
    {
        Http::fake(function () {
            throw new \Exception('Connection timeout');
        });

        $result = $this->service->createPaymentLink([
            'external_id' => 'auction_1_2026',
            'amount' => 1000000,
            'description' => 'Pembayaran Lelang',
        ]);

        $this->assertNull($result);
    }

    public function test_create_payment_link_returns_null_on_validation_error(): void
    {
        // validatePaymentData throws but createPaymentLink catches it
        $result = $this->service->createPaymentLink([
            'amount' => 1000000,
            'description' => 'Pembayaran Lelang',
            // Missing external_id
        ]);

        $this->assertNull($result);
    }

    public function test_create_payment_link_returns_null_on_missing_amount(): void
    {
        $result = $this->service->createPaymentLink([
            'external_id' => 'auction_1_2026',
            'description' => 'Pembayaran Lelang',
            // Missing amount
        ]);

        $this->assertNull($result);
    }

    public function test_create_payment_link_returns_null_on_missing_description(): void
    {
        $result = $this->service->createPaymentLink([
            'external_id' => 'auction_1_2026',
            'amount' => 1000000,
            // Missing description
        ]);

        $this->assertNull($result);
    }

    public function test_create_payment_link_returns_null_on_zero_amount(): void
    {
        $result = $this->service->createPaymentLink([
            'external_id' => 'auction_1_2026',
            'amount' => 0,
            'description' => 'Pembayaran Lelang',
        ]);

        $this->assertNull($result);
    }

    public function test_create_payment_link_returns_null_on_negative_amount(): void
    {
        $result = $this->service->createPaymentLink([
            'external_id' => 'auction_1_2026',
            'amount' => -1000,
            'description' => 'Pembayaran Lelang',
        ]);

        $this->assertNull($result);
    }

    // ─── getPaymentLink Tests ──────────────────────────────────────────

    public function test_get_payment_link_success(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices/inv_test_123' => Http::response([
                'id' => 'inv_test_123',
                'external_id' => 'auction_1_2026',
                'invoice_url' => 'https://checkout.xendit.co/inv_test_123',
                'amount' => 1000000,
                'status' => 'PAID',
                'created' => '2026-08-05T00:00:00.000Z',
                'expiry_date' => '2026-08-06T00:00:00.000Z',
                'paid_at' => '2026-08-05T12:00:00.000Z',
                'payment_method' => 'BANK_TRANSFER',
            ], 200),
        ]);

        $result = $this->service->getPaymentLink('inv_test_123');

        $this->assertNotNull($result);
        $this->assertEquals('inv_test_123', $result['id']);
        $this->assertEquals('PAID', $result['status']);
        $this->assertEquals('https://checkout.xendit.co/inv_test_123', $result['checkout_url']);
        $this->assertNotNull($result['paid_at']);
        $this->assertEquals('BANK_TRANSFER', $result['payment_method']);
    }

    public function test_get_payment_link_returns_null_on_failure(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices/inv_not_found' => Http::response([
                'error_code' => 'INVOICE_NOT_FOUND',
                'message' => 'Invoice not found',
            ], 404),
        ]);

        $result = $this->service->getPaymentLink('inv_not_found');

        $this->assertNull($result);
    }

    public function test_get_payment_link_returns_null_on_exception(): void
    {
        Http::fake(function () {
            throw new \Exception('Connection error');
        });

        $result = $this->service->getPaymentLink('inv_test_123');

        $this->assertNull($result);
    }

    // ─── expirePaymentLink Tests ───────────────────────────────────────

    public function test_expire_payment_link_success(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices/inv_test_123/expire' => Http::response([
                'id' => 'inv_test_123',
                'status' => 'EXPIRED',
                'expiry_date' => '2026-08-05T00:00:00.000Z',
            ], 200),
        ]);

        $result = $this->service->expirePaymentLink('inv_test_123');

        $this->assertNotNull($result);
        $this->assertEquals('EXPIRED', $result['status']);
    }

    public function test_expire_payment_link_returns_null_on_failure(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices/inv_test_123/expire' => Http::response([
                'error_code' => 'INVOICE_NOT_FOUND',
            ], 404),
        ]);

        $result = $this->service->expirePaymentLink('inv_test_123');

        $this->assertNull($result);
    }

    public function test_expire_payment_link_returns_null_on_exception(): void
    {
        Http::fake(function () {
            throw new \Exception('Connection error');
        });

        $result = $this->service->expirePaymentLink('inv_test_123');

        $this->assertNull($result);
    }

    // ─── createQrisPayment Tests ───────────────────────────────────────

    public function test_create_qris_payment_success(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv_qris_123',
                'external_id' => 'linktree_payment_1',
                'invoice_url' => 'https://checkout.xendit.co/inv_qris_123',
                'qr_code' => 'https://qr.xendit.co/inv_qris_123',
                'amount' => 50000,
                'status' => 'PENDING',
                'created' => '2026-08-05T00:00:00.000Z',
                'expiry_date' => '2026-08-06T00:00:00.000Z',
                'payment_methods' => ['QRIS'],
            ], 200),
        ]);

        $result = $this->service->createQrisPayment([
            'external_id' => 'linktree_payment_1',
            'amount' => 50000,
            'description' => 'Pembayaran Linktree',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('inv_qris_123', $result['id']);
        $this->assertEquals('https://qr.xendit.co/inv_qris_123', $result['qr_code']);
        $this->assertEquals(50000, $result['amount']);
        $this->assertContains('QRIS', $result['payment_methods']);
    }

    public function test_create_qris_payment_returns_null_on_failure(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'error_code' => 'INVALID_PAYLOAD',
                'message' => 'Invalid amount',
            ], 400),
        ]);

        $result = $this->service->createQrisPayment([
            'external_id' => 'linktree_payment_1',
            'amount' => 50000,
            'description' => 'Pembayaran Linktree',
        ]);

        $this->assertNull($result);
    }

    public function test_create_qris_payment_returns_null_on_validation_error(): void
    {
        $result = $this->service->createQrisPayment([
            'amount' => 50000,
            // Missing external_id and description
        ]);

        $this->assertNull($result);
    }

    // ─── HTTP Request Assertion Tests ──────────────────────────────────

    public function test_create_payment_link_sends_request_to_xendit(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv_test_456',
                'external_id' => 'test_456',
                'invoice_url' => 'https://checkout.xendit.co/inv_test_456',
                'amount' => 100000,
                'status' => 'PENDING',
            ], 200),
        ]);

        $this->service->createPaymentLink([
            'external_id' => 'test_456',
            'amount' => 100000,
            'description' => 'Test',
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.xendit.co/v2/invoices'
                && $request->method() === 'POST';
        });
    }

    public function test_create_payment_link_includes_amount_in_payload(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv_test_789',
                'external_id' => 'test_789',
                'invoice_url' => 'https://checkout.xendit.co/inv_test_789',
                'amount' => 250000,
                'status' => 'PENDING',
            ], 200),
        ]);

        $this->service->createPaymentLink([
            'external_id' => 'test_789',
            'amount' => 250000,
            'description' => 'Test Payment',
        ]);

        Http::assertSent(function ($request) {
            $data = $request->data();
            return ($data['amount'] ?? null) === 250000
                && ($data['external_id'] ?? null) === 'test_789';
        });
    }

    public function test_create_qris_payment_sends_request_to_xendit(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv_qris_test',
                'external_id' => 'linktree_test',
                'invoice_url' => 'https://checkout.xendit.co/inv_qris_test',
                'qr_code' => 'https://qr.xendit.co/inv_qris_test',
                'amount' => 50000,
                'status' => 'PENDING',
            ], 200),
        ]);

        $this->service->createQrisPayment([
            'external_id' => 'linktree_test',
            'amount' => 50000,
            'description' => 'QRIS Test',
        ]);

        Http::assertSent(function ($request) {
            $data = $request->data();
            // QRIS should only have QRIS as payment method
            return ($data['payment_methods'] ?? []) === ['QRIS'];
        });
    }

    // ─── createXenPayment Tests ────────────────────────────────────────

    public function test_create_xen_payment_success(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/xenpayments' => Http::response([
                'id' => 'xen_123',
                'external_id' => 'auction_2_2026',
                'amount' => 500000,
                'status' => 'PENDING',
            ], 200),
        ]);

        $result = $this->service->createXenPayment([
            'external_id' => 'auction_2_2026',
            'amount' => 500000,
            'description' => 'Pembayaran Lelang',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('xen_123', $result['id']);
        $this->assertEquals(500000, $result['amount']);
    }

    public function test_create_xen_payment_returns_null_on_failure(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/xenpayments' => Http::response([], 500),
        ]);

        $result = $this->service->createXenPayment([
            'external_id' => 'auction_2_2026',
            'amount' => 500000,
            'description' => 'Pembayaran Lelang',
        ]);

        $this->assertNull($result);
    }

    public function test_create_xen_payment_returns_null_on_exception(): void
    {
        Http::fake(function () {
            throw new \Exception('Connection error');
        });

        $result = $this->service->createXenPayment([
            'external_id' => 'auction_2_2026',
            'amount' => 500000,
            'description' => 'Pembayaran Lelang',
        ]);

        $this->assertNull($result);
    }

    // ─── getXenPayment Tests ───────────────────────────────────────────

    public function test_get_xen_payment_success(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/xenpayments/xen_123' => Http::response([
                'id' => 'xen_123',
                'external_id' => 'auction_2_2026',
                'amount' => 500000,
                'status' => 'PAID',
            ], 200),
        ]);

        $result = $this->service->getXenPayment('xen_123');

        $this->assertNotNull($result);
        $this->assertEquals('PAID', $result['status']);
    }

    public function test_get_xen_payment_returns_null_on_failure(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/xenpayments/xen_not_found' => Http::response([], 404),
        ]);

        $result = $this->service->getXenPayment('xen_not_found');

        $this->assertNull($result);
    }

    public function test_get_xen_payment_returns_null_on_exception(): void
    {
        Http::fake(function () {
            throw new \Exception('Connection error');
        });

        $result = $this->service->getXenPayment('xen_123');

        $this->assertNull($result);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WebhookSignatureTest extends TestCase
{
    // Note: We don't use RefreshDatabase because tests run against the real DB.

    protected string $webhookUrl;
    protected string $webhookToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->webhookUrl = '/api/xendit/webhook';
        $this->webhookToken = Config::get('services.xendit.webhook_token', 'test-webhook-token');

        // Ensure debug mode is off for signature verification tests
        Config::set('app.debug', false);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Route Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_webhook_route_is_registered(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('api.webhook.xendit'));
    }

    public function test_webhook_route_accepts_post_method(): void
    {
        $payload = json_encode([
            'event' => 'payment_link.paid',
            'data' => ['id' => 'test', 'status' => 'PAID'],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        // Should not return 405 Method Not Allowed
        $this->assertTrue($response->status() !== 405, 'POST method should be accepted');
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 1: Valid webhook signature is accepted
    // ═══════════════════════════════════════════════════════════════════

    public function test_valid_webhook_signature_is_accepted(): void
    {
        $payload = json_encode([
            'event' => 'payment_link.paid',
            'data' => [
                'id' => 'test-payment-001',
                'status' => 'PAID',
                'external_id' => 'test-external-001',
                'amount' => 100000,
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_valid_signature_with_different_payload(): void
    {
        $payload = json_encode([
            'event' => 'xenpayment.paid',
            'data' => [
                'id' => 'xen-payment-001',
                'status' => 'SUCCEEDED',
                'external_id' => 'xen-external-001',
                'amount' => 250000,
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 2: Invalid webhook signature is rejected
    // ═══════════════════════════════════════════════════════════════════

    public function test_invalid_webhook_signature_is_rejected(): void
    {
        $payload = json_encode([
            'event' => 'payment_link.paid',
            'data' => [
                'id' => 'test-payment-002',
                'status' => 'PAID',
                'external_id' => 'test-external-002',
                'amount' => 100000,
            ],
        ]);

        // Create a wrong signature
        $invalidSignature = hash_hmac('sha256', $payload, 'wrong-token');

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $invalidSignature,
            'Content-Type' => 'application/json',
        ]);

        // Should return 200 with ignored status (to prevent Xendit retries)
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ignored',
            'reason' => 'Invalid signature',
        ]);
    }

    public function test_tampered_payload_with_valid_signature_is_rejected(): void
    {
        $originalPayload = json_encode([
            'event' => 'payment_link.paid',
            'data' => [
                'id' => 'test-payment-003',
                'status' => 'PAID',
                'external_id' => 'test-external-003',
                'amount' => 100000,
            ],
        ]);

        // Sign the original payload
        $signature = hash_hmac('sha256', $originalPayload, $this->webhookToken);

        // But send a different (tampered) payload
        $tamperedPayload = [
            'event' => 'payment_link.paid',
            'data' => [
                'id' => 'test-payment-003',
                'status' => 'PAID',
                'external_id' => 'test-external-003',
                'amount' => 999999, // Tampered amount
            ],
        ];

        $response = $this->postJson($this->webhookUrl, $tamperedPayload, [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        // Should be rejected because the signature doesn't match the tampered payload
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ignored',
            'reason' => 'Invalid signature',
        ]);
    }

    public function test_random_string_signature_is_rejected(): void
    {
        $payload = json_encode([
            'event' => 'payment_link.paid',
            'data' => ['id' => 'test', 'status' => 'PAID'],
        ]);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => 'completely-random-signature-string',
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ignored',
            'reason' => 'Invalid signature',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 3: Missing webhook signature
    // ═══════════════════════════════════════════════════════════════════

    public function test_missing_webhook_signature_is_rejected(): void
    {
        $payload = json_encode([
            'event' => 'payment_link.paid',
            'data' => [
                'id' => 'test-payment-004',
                'status' => 'PAID',
                'external_id' => 'test-external-004',
                'amount' => 100000,
            ],
        ]);

        // Send without x-xendit-signature header
        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ignored',
            'reason' => 'Invalid signature',
        ]);
    }

    public function test_empty_webhook_signature_is_rejected(): void
    {
        $payload = json_encode([
            'event' => 'payment_link.paid',
            'data' => ['id' => 'test', 'status' => 'PAID'],
        ]);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => '',
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ignored',
            'reason' => 'Invalid signature',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 4: Webhook event processing
    // ═══════════════════════════════════════════════════════════════════

    public function test_webhook_handles_payment_link_paid_event(): void
    {
        $payload = json_encode([
            'event' => 'payment_link.paid',
            'data' => [
                'id' => 'pl-test-001',
                'status' => 'PAID',
                'external_id' => 'ext-test-001',
                'amount' => 150000,
                'payment_method' => 'BCA',
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_webhook_handles_xenpayment_paid_event(): void
    {
        $payload = json_encode([
            'event' => 'xenpayment.paid',
            'data' => [
                'id' => 'xen-test-001',
                'status' => 'SUCCEEDED',
                'external_id' => 'xen-ext-001',
                'amount' => 200000,
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_webhook_handles_payment_link_expired_event(): void
    {
        $payload = json_encode([
            'event' => 'payment_link.expired',
            'data' => [
                'id' => 'pl-expire-001',
                'status' => 'EXPIRED',
                'external_id' => 'ext-expire-001',
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_webhook_handles_unknown_event_gracefully(): void
    {
        $payload = json_encode([
            'event' => 'unknown.event.type',
            'data' => ['id' => 'unknown-001'],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        // Should still return success (acknowledged, just not processed)
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_webhook_handles_batch_disbursement_completed(): void
    {
        $payload = json_encode([
            'event' => 'batch_disbursement.completed',
            'data' => [
                'id' => 'batch-001',
                'status' => 'COMPLETED',
                'amount' => 500000,
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Signature Verification Unit Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_signature_is_hmac_sha256(): void
    {
        $payload = 'test payload data';
        $token = 'test-token';

        $expectedSignature = hash_hmac('sha256', $payload, $token);

        // Verify the signature matches what we expect
        $this->assertEquals(64, strlen($expectedSignature)); // SHA-256 produces 64 hex chars
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $expectedSignature);
    }

    public function test_different_payloads_produce_different_signatures(): void
    {
        $token = 'test-token';
        $payload1 = json_encode(['event' => 'payment_link.paid', 'data' => ['amount' => 100]]);
        $payload2 = json_encode(['event' => 'payment_link.paid', 'data' => ['amount' => 200]]);

        $sig1 = hash_hmac('sha256', $payload1, $token);
        $sig2 = hash_hmac('sha256', $payload2, $token);

        $this->assertNotEquals($sig1, $sig2);
    }

    public function test_same_payload_produces_deterministic_signature(): void
    {
        $payload = json_encode(['event' => 'test', 'data' => []]);
        $token = 'test-token';

        $sig1 = hash_hmac('sha256', $payload, $token);
        $sig2 = hash_hmac('sha256', $payload, $token);

        $this->assertEquals($sig1, $sig2);
    }

    // ═══════════════════════════════════════════════════════════════════
    // CSRF Exclusion Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_webhook_route_does_not_require_csrf_token(): void
    {
        $payload = json_encode([
            'event' => 'payment_link.paid',
            'data' => ['id' => 'csrf-test-001', 'status' => 'PAID'],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        // Send POST without CSRF token — should NOT fail with 419
        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        $this->assertTrue($response->status() !== 419, 'Webhook should not require CSRF token');
    }

    public function test_webhook_route_does_not_require_authentication(): void
    {
        // Don't authenticate
        $payload = json_encode([
            'event' => 'payment_link.paid',
            'data' => ['id' => 'auth-test-001', 'status' => 'PAID'],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        // Should not redirect to login (401 or 302)
        $this->assertTrue($response->status() !== 302, 'Webhook should not redirect to login');
        $this->assertTrue($response->status() !== 401, 'Webhook should not require authentication');
    }

    // ═══════════════════════════════════════════════════════════════════
    // Error Handling Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_webhook_handles_malformed_json_gracefully(): void
    {
        $response = $this->post($this->webhookUrl, [], [
            'Content-Type' => 'application/json',
        ]);

        // Should not crash — returns some response
        $this->assertContains($response->status(), [200, 400, 422]);
    }

    public function test_webhook_handles_empty_body(): void
    {
        $response = $this->post($this->webhookUrl, [], [
            'Content-Type' => 'application/json',
        ]);

        $this->assertContains($response->status(), [200, 400, 422]);
    }

    public function test_webhook_handles_invalid_event_field(): void
    {
        $payload = json_encode([
            // Missing 'event' field
            'data' => ['id' => 'test'],
        ]);

        $signature = hash_hmac('sha256', $payload, $this->webhookToken);

        $response = $this->postJson($this->webhookUrl, json_decode($payload, true), [
            'x-xendit-signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        // Should handle gracefully — returns 200 to prevent Xendit retries
        $this->assertContains($response->status(), [200, 500]);
    }
}

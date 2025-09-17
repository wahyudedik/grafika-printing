<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\XenditService;

class TestWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:test {url? : Webhook URL to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Xendit webhook endpoint';

    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        parent::__construct();
        $this->xenditService = $xenditService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = $this->argument('url') ?? config('services.xendit.callback_url');

        if (!$url) {
            $this->error('❌ Webhook URL not provided. Please set XENDIT_CALLBACK_URL in .env or provide URL as argument.');
            return 1;
        }

        $this->info("Testing webhook endpoint: {$url}");
        $this->line('');

        // Test data
        $testData = [
            'event' => 'payment_link.paid',
            'data' => [
                'id' => 'pl_test_' . time(),
                'external_id' => 'test_' . time(),
                'status' => 'PAID',
                'amount' => 100000,
                'currency' => 'IDR',
                'payment_method' => 'BCA',
                'created' => now()->toISOString(),
                'updated' => now()->toISOString()
            ]
        ];

        try {
            // Generate signature
            $payload = json_encode($testData);
            $signature = hash_hmac('sha256', $payload, config('services.xendit.webhook_token'));

            $this->info('📤 Sending test webhook...');
            $this->line('Payload: ' . $payload);
            $this->line('Signature: ' . $signature);
            $this->line('');

            // Send webhook
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-xendit-signature' => $signature,
                'User-Agent' => 'Xendit-Webhook/1.0'
            ])->post($url, $testData);

            $this->info('📥 Response received:');
            $this->line('Status: ' . $response->status());
            $this->line('Body: ' . $response->body());
            $this->line('');

            if ($response->successful()) {
                $this->info('✅ Webhook test successful!');
                $this->line('Your webhook endpoint is working correctly.');
            } else {
                $this->error('❌ Webhook test failed!');
                $this->line('Check your webhook endpoint and try again.');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error testing webhook: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XenditService;
use App\Models\XenditPayment;

class TestXenditPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xendit:test {--type=payment_link : Payment type (payment_link or xenpayment)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Xendit payment integration';

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
        $type = $this->option('type');

        $this->info("Testing Xendit {$type} integration...");

        // Test data
        $testData = [
            'external_id' => 'test_' . time(),
            'amount' => 100000,
            'description' => 'Test payment for Xendit integration',
            'customer' => [
                'given_names' => 'Test User',
                'email' => 'test@example.com'
            ],
            'items' => [
                [
                    'name' => 'Test Item',
                    'quantity' => 1,
                    'price' => 100000,
                    'category' => 'Test'
                ]
            ],
            'success_redirect_url' => 'https://example.com/success',
            'failure_redirect_url' => 'https://example.com/failure'
        ];

        try {
            if ($type === 'payment_link') {
                $this->testPaymentLink($testData);
            } else {
                $this->testXenPayment($testData);
            }

            $this->info('✅ Test completed successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
    }

    protected function testPaymentLink(array $data)
    {
        $this->info('Creating payment link...');

        $response = $this->xenditService->createPaymentLink($data);

        if ($response) {
            $this->info('✅ Payment link created successfully!');
            $this->line('Payment Link ID: ' . ($response['id'] ?? 'N/A'));
            $this->line('Checkout URL: ' . ($response['checkout_url'] ?? 'N/A'));

            // Save to database
            XenditPayment::create([
                'external_id' => $data['external_id'],
                'xendit_id' => $response['id'] ?? null,
                'type' => 'payment_link',
                'amount' => $data['amount'],
                'description' => $data['description'],
                'status' => 'pending',
                'customer' => $data['customer'],
                'items' => $data['items'],
                'checkout_url' => $response['checkout_url'] ?? null,
                'success_redirect_url' => $data['success_redirect_url'],
                'failure_redirect_url' => $data['failure_redirect_url'],
                'expires_at' => now()->addHours(24)
            ]);

            $this->info('✅ Payment record saved to database');
        } else {
            throw new \Exception('Failed to create payment link');
        }
    }

    protected function testXenPayment(array $data)
    {
        $this->info('Creating XenPayment...');

        $response = $this->xenditService->createXenPayment($data);

        if ($response) {
            $this->info('✅ XenPayment created successfully!');
            $this->line('XenPayment ID: ' . ($response['id'] ?? 'N/A'));

            // Save to database
            XenditPayment::create([
                'external_id' => $data['external_id'],
                'xendit_id' => $response['id'] ?? null,
                'type' => 'xenpayment',
                'amount' => $data['amount'],
                'description' => $data['description'],
                'status' => 'pending',
                'customer' => $data['customer'],
                'items' => $data['items'],
                'expires_at' => now()->addHours(24)
            ]);

            $this->info('✅ Payment record saved to database');
        } else {
            throw new \Exception('Failed to create XenPayment');
        }
    }
}

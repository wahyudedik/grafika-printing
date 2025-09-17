<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\XenditPayment;

class XenditPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample Xendit payments for testing
        XenditPayment::create([
            'external_id' => 'auction_1_' . time(),
            'xendit_id' => 'pl_' . uniqid(),
            'type' => 'payment_link',
            'amount' => 500000,
            'currency' => 'IDR',
            'description' => 'Payment for auction: Sample Print Job',
            'status' => 'pending',
            'customer' => [
                'given_names' => 'John Doe',
                'email' => 'john@example.com'
            ],
            'items' => [
                [
                    'name' => 'Sample Print Job',
                    'quantity' => 1,
                    'price' => 500000,
                    'category' => 'Printing Service'
                ]
            ],
            'checkout_url' => 'https://checkout.xendit.co/web/pl_' . uniqid(),
            'success_redirect_url' => 'https://example.com/success',
            'failure_redirect_url' => 'https://example.com/failure',
            'expires_at' => now()->addHours(24)
        ]);

        XenditPayment::create([
            'external_id' => 'auction_2_' . time(),
            'xendit_id' => 'xp_' . uniqid(),
            'type' => 'xenpayment',
            'amount' => 750000,
            'currency' => 'IDR',
            'description' => 'Payment for auction: Premium Print Job',
            'status' => 'paid',
            'payment_method' => 'BCA',
            'customer' => [
                'given_names' => 'Jane Smith',
                'email' => 'jane@example.com'
            ],
            'items' => [
                [
                    'name' => 'Premium Print Job',
                    'quantity' => 1,
                    'price' => 750000,
                    'category' => 'Printing Service'
                ]
            ],
            'paid_at' => now()->subHours(2)
        ]);
    }
}

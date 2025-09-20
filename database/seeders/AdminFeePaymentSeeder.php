<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminFeeTransaction;
use App\Models\Auction;
use App\Models\Vendor;
use App\Models\User;
use App\Models\XenditPayment;
use Illuminate\Support\Facades\DB;

class AdminFeePaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            $this->createAdminFeeTransactions();
            $this->createXenditPayments();
            
            DB::commit();
            $this->command->info('✅ Admin fee and payment dummy data created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating admin fee and payment dummy data: ' . $e->getMessage());
        }
    }

    private function createAdminFeeTransactions()
    {
        $this->command->info('Creating admin fee transactions...');
        
        $auctions = Auction::where('status', 'paid')->get();
        
        foreach ($auctions as $auction) {
            if ($auction->admin_fee_amount > 0) {
                $status = $this->getRandomTransactionStatus();
                $paymentMethod = $this->getRandomPaymentMethod();
                $paymentReference = $this->getRandomPaymentReference();
                
                AdminFeeTransaction::create([
                    'auction_id' => $auction->id,
                    'vendor_id' => $auction->winner_vendor_id,
                    'user_id' => $auction->user_id,
                    'auction_amount' => $auction->winning_bid,
                    'admin_fee_amount' => $auction->admin_fee_amount,
                    'payment_gateway_fee' => $auction->payment_gateway_fee,
                    'total_amount' => $auction->total_amount_with_fees,
                    'vendor_receives' => $auction->vendor_receives,
                    'admin_receives' => $auction->admin_receives,
                    'status' => $status,
                    'fee_breakdown' => $auction->fee_breakdown,
                    'payment_method' => $status === 'paid' ? $paymentMethod : null,
                    'payment_reference' => $status === 'paid' ? $paymentReference : null,
                    'paid_at' => $status === 'paid' ? now()->subDays(rand(1, 30)) : null,
                    'notes' => $this->getRandomTransactionNotes($status),
                    'created_at' => now()->subDays(rand(1, 30))
                ]);
            }
        }
    }

    private function createXenditPayments()
    {
        $this->command->info('Creating Xendit payments...');
        
        $auctions = Auction::where('status', 'paid')->get();
        
        foreach ($auctions as $auction) {
            // Create Xendit payment record
            XenditPayment::create([
                'external_id' => 'auction_' . $auction->id . '_' . time(),
                'xendit_id' => 'xendit_' . $auction->id . '_' . uniqid(),
                'type' => 'payment_link',
                'amount' => $auction->total_amount_with_fees,
                'description' => 'Pembayaran Lelang: ' . $auction->title,
                'status' => 'paid',
                'customer' => [
                    'given_names' => $auction->user->name,
                    'email' => $auction->user->email
                ],
                'items' => [
                    [
                        'name' => $auction->title,
                        'quantity' => $auction->quantity,
                        'price' => $auction->total_amount_with_fees,
                        'category' => 'Printing Service'
                    ]
                ],
                'checkout_url' => 'https://checkout.xendit.co/web/' . uniqid(),
                'success_redirect_url' => route('user.auctions.show', $auction) . '?payment=success',
                'failure_redirect_url' => route('user.auctions.show', $auction) . '?payment=failed',
                'expires_at' => now()->addHours(24),
                'auction_id' => $auction->id,
                'webhook_data' => $this->getRandomWebhookData(),
                'created_at' => now()->subDays(rand(1, 30))
            ]);
        }
    }

    private function getRandomTransactionStatus()
    {
        $statuses = ['pending', 'paid', 'failed', 'refunded'];
        $weights = [10, 70, 15, 5]; // 10% pending, 70% paid, 15% failed, 5% refunded
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'paid';
    }

    private function getRandomPaymentMethod()
    {
        $methods = ['bank_transfer', 'credit_card', 'debit_card', 'ewallet', 'retail_outlet'];
        return $methods[array_rand($methods)];
    }

    private function getRandomPaymentReference()
    {
        return 'PAY_' . rand(100000, 999999) . '_' . time();
    }

    private function getRandomTransactionNotes($status)
    {
        $notes = [
            'pending' => [
                'Menunggu konfirmasi pembayaran',
                'Pembayaran sedang diproses',
                'Menunggu verifikasi pembayaran'
            ],
            'paid' => [
                'Pembayaran berhasil diproses',
                'Transaksi selesai dengan sukses',
                'Pembayaran telah dikonfirmasi'
            ],
            'failed' => [
                'Pembayaran gagal diproses',
                'Transaksi dibatalkan',
                'Pembayaran tidak berhasil'
            ],
            'refunded' => [
                'Pembayaran telah dikembalikan',
                'Refund berhasil diproses',
                'Pengembalian dana selesai'
            ]
        ];
        
        $statusNotes = $notes[$status] ?? $notes['paid'];
        return $statusNotes[array_rand($statusNotes)];
    }

    private function getRandomWebhookData()
    {
        return [
            'id' => 'xendit_' . uniqid(),
            'external_id' => 'auction_' . rand(1000, 9999),
            'user_id' => rand(1, 10),
            'status' => 'PAID',
            'merchant_name' => 'Grafika Printing',
            'merchant_profile_picture_url' => 'https://example.com/logo.png',
            'amount' => rand(100000, 2000000),
            'description' => 'Pembayaran Lelang',
            'invoice_url' => 'https://checkout.xendit.co/web/' . uniqid(),
            'expiry_date' => now()->addDays(1)->toISOString(),
            'created' => now()->subDays(rand(1, 30))->toISOString(),
            'updated' => now()->subDays(rand(1, 30))->toISOString(),
            'currency' => 'IDR',
            'paid_at' => now()->subDays(rand(1, 30))->toISOString(),
            'payment_method' => 'BANK_TRANSFER',
            'payment_channel' => 'BCA',
            'payment_destination' => 'BCA',
            'success_redirect_url' => 'https://example.com/success',
            'failure_redirect_url' => 'https://example.com/failure',
            'items' => [
                [
                    'name' => 'Printing Service',
                    'quantity' => 1,
                    'price' => rand(100000, 2000000),
                    'category' => 'Printing Service'
                ]
            ],
            'fees' => [
                [
                    'type' => 'ADMIN',
                    'value' => rand(5000, 50000)
                ]
            ],
            'customer' => [
                'given_names' => 'John Doe',
                'email' => 'john@example.com',
                'mobile_number' => '+6281234567890'
            ],
            'customer_notification_preference' => [
                'invoice_created' => ['email'],
                'invoice_reminder' => ['email'],
                'invoice_paid' => ['email'],
                'invoice_expired' => ['email']
            ]
        ];
    }
}

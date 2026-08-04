<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorWallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VendorWalletSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding Vendor Wallets...');

        $vendors = Vendor::all();

        if ($vendors->isEmpty()) {
            $this->command->warn('⚠️ No vendors found. Skipping wallet seeding.');
            return;
        }

        foreach ($vendors as $vendor) {
            $wallet = VendorWallet::firstOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'balance' => 1250000.00,
                    'pending_balance' => 350000.00,
                    'total_earned' => 5750000.00,
                    'total_withdrawn' => 4500000.00,
                    'is_active' => true,
                ]
            );

            // Create sample wallet transactions
            $transactions = [
                [
                    'vendor_wallet_id' => $wallet->id,
                    'vendor_id' => $vendor->id,
                    'transaction_code' => 'TXN-' . strtoupper(Str::random(8)),
                    'type' => 'credit',
                    'category' => 'auction_payment',
                    'amount' => 2500000.00,
                    'balance_before' => 0,
                    'balance_after' => 2500000.00,
                    'description' => 'Pembayaran dari lelang #1 - Cetak Nota NCR',
                    'reference_id' => '1',
                    'reference_type' => 'auction',
                    'status' => 'completed',
                    'metadata' => json_encode(['payment_method' => 'xendit']),
                ],
                [
                    'vendor_wallet_id' => $wallet->id,
                    'vendor_id' => $vendor->id,
                    'transaction_code' => 'TXN-' . strtoupper(Str::random(8)),
                    'type' => 'credit',
                    'category' => 'auction_payment',
                    'amount' => 1750000.00,
                    'balance_before' => 2500000.00,
                    'balance_after' => 4250000.00,
                    'description' => 'Pembayaran dari lelang #2 - Banner Promosi',
                    'reference_id' => '2',
                    'reference_type' => 'auction',
                    'status' => 'completed',
                    'metadata' => json_encode(['payment_method' => 'xendit']),
                ],
                [
                    'vendor_wallet_id' => $wallet->id,
                    'vendor_id' => $vendor->id,
                    'transaction_code' => 'TXN-' . strtoupper(Str::random(8)),
                    'type' => 'debit',
                    'category' => 'withdrawal',
                    'amount' => 1500000.00,
                    'balance_before' => 4250000.00,
                    'balance_after' => 2750000.00,
                    'description' => 'Penarikan dana ke BCA ****1234',
                    'reference_id' => null,
                    'reference_type' => 'withdrawal',
                    'status' => 'completed',
                    'metadata' => json_encode(['bank' => 'BCA', 'account' => '****1234']),
                ],
                [
                    'vendor_wallet_id' => $wallet->id,
                    'vendor_id' => $vendor->id,
                    'transaction_code' => 'TXN-' . strtoupper(Str::random(8)),
                    'type' => 'credit',
                    'category' => 'auction_payment',
                    'amount' => 1500000.00,
                    'balance_before' => 2750000.00,
                    'balance_after' => 4250000.00,
                    'description' => 'Pembayaran dari lelang #3 - Kartu Nama Premium',
                    'reference_id' => '3',
                    'reference_type' => 'auction',
                    'status' => 'completed',
                    'metadata' => json_encode(['payment_method' => 'xendit']),
                ],
                [
                    'vendor_wallet_id' => $wallet->id,
                    'vendor_id' => $vendor->id,
                    'transaction_code' => 'TXN-' . strtoupper(Str::random(8)),
                    'type' => 'debit',
                    'category' => 'withdrawal',
                    'amount' => 3000000.00,
                    'balance_before' => 4250000.00,
                    'balance_after' => 1250000.00,
                    'description' => 'Penarikan dana ke Mandiri ****5678',
                    'reference_id' => null,
                    'reference_type' => 'withdrawal',
                    'status' => 'completed',
                    'metadata' => json_encode(['bank' => 'Mandiri', 'account' => '****5678']),
                ],
                [
                    'vendor_wallet_id' => $wallet->id,
                    'vendor_id' => $vendor->id,
                    'transaction_code' => 'TXN-' . strtoupper(Str::random(8)),
                    'type' => 'credit',
                    'category' => 'bonus',
                    'amount' => 350000.00,
                    'balance_before' => 1250000.00,
                    'balance_after' => 1600000.00,
                    'description' => 'Bonus dari admin - Vendor Top Performer',
                    'reference_id' => null,
                    'reference_type' => 'bonus',
                    'status' => 'completed',
                    'metadata' => json_encode(['reason' => 'top_performer']),
                ],
                [
                    'vendor_wallet_id' => $wallet->id,
                    'vendor_id' => $vendor->id,
                    'transaction_code' => 'TXN-' . strtoupper(Str::random(8)),
                    'type' => 'debit',
                    'category' => 'withdrawal',
                    'amount' => 350000.00,
                    'balance_before' => 1600000.00,
                    'balance_after' => 1250000.00,
                    'description' => 'Penarikan dana pending',
                    'reference_id' => null,
                    'reference_type' => 'withdrawal',
                    'status' => 'pending',
                    'metadata' => null,
                ],
            ];

            foreach ($transactions as $txn) {
                // Use updateOrCreate to avoid duplicates on re-seed
                \App\Models\VendorWalletTransaction::updateOrCreate(
                    ['transaction_code' => $txn['transaction_code']],
                    $txn
                );
            }

            $this->command->info("✅ Created wallet for vendor: {$vendor->name} (Balance: Rp " . number_format($wallet->balance) . ")");
        }
    }
}

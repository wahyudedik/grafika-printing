<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\VendorWithdrawal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WithdrawalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            $this->createWalletTransactions();
            $this->createWithdrawals();
            
            DB::commit();
            $this->command->info('✅ Withdrawal dummy data created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating withdrawal dummy data: ' . $e->getMessage());
        }
    }

    private function createWalletTransactions()
    {
        $this->command->info('Creating wallet transactions...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $wallet = $vendor->wallet;
            if (!$wallet) continue;
            
            // Create 20-50 transactions per vendor
            $transactionCount = rand(20, 50);
            
            for ($i = 0; $i < $transactionCount; $i++) {
                $transactionType = rand(0, 1) ? 'credit' : 'debit';
                $amount = rand(50000, 2000000);
                
                if ($transactionType === 'debit' && $wallet->balance < $amount) {
                    $transactionType = 'credit'; // Force credit if insufficient balance
                }
                
                $category = $this->getRandomCategory($transactionType);
                $description = $this->getRandomDescription($category);
                
                VendorWalletTransaction::create([
                    'vendor_wallet_id' => $wallet->id,
                    'vendor_id' => $vendor->id,
                    'type' => $transactionType,
                    'amount' => $amount,
                    'category' => $category,
                    'description' => $description,
                    'reference_id' => $this->getRandomReferenceId($category),
                    'reference_type' => $this->getRandomReferenceType($category),
                    'metadata' => $this->getRandomMetadata($category),
                    'status' => 'completed',
                    'created_at' => now()->subDays(rand(1, 90))
                ]);
                
                // Update wallet balance
                if ($transactionType === 'credit') {
                    $wallet->increment('balance', $amount);
                    $wallet->increment('total_earned', $amount);
                } else {
                    $wallet->decrement('balance', $amount);
                    $wallet->increment('total_withdrawn', $amount);
                }
            }
        }
    }

    private function createWithdrawals()
    {
        $this->command->info('Creating withdrawals...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $wallet = $vendor->wallet;
            if (!$wallet || $wallet->balance < 100000) continue;
            
            // Create 3-8 withdrawals per vendor
            $withdrawalCount = rand(3, 8);
            
            for ($i = 0; $i < $withdrawalCount; $i++) {
                $amount = rand(100000, min($wallet->balance, 2000000));
                $method = $this->getRandomWithdrawalMethod();
                
                $withdrawal = VendorWithdrawal::create([
                    'vendor_id' => $vendor->id,
                    'vendor_wallet_id' => $wallet->id,
                    'amount' => $amount,
                    'method' => $method,
                    'account_number' => $this->getRandomAccountNumber($method),
                    'account_name' => $vendor->name,
                    'bank_name' => $this->getRandomBankName($method),
                    'status' => $this->getRandomWithdrawalStatus(),
                    'notes' => $this->getRandomWithdrawalNotes(),
                    'fee' => $this->calculateWithdrawalFee($amount, $method),
                    'admin_notes' => $this->getRandomAdminNotes(),
                    'processed_by' => $this->getRandomAdminId(),
                    'processed_at' => now()->subDays(rand(1, 60)),
                    'created_at' => now()->subDays(rand(1, 90))
                ]);
                
                // Update withdrawal status based on random status
                if (in_array($withdrawal->status, ['completed', 'processing'])) {
                    $withdrawal->update([
                        'processed_at' => now()->subDays(rand(1, 30)),
                        'processed_by' => $this->getRandomAdminId()
                    ]);
                }
            }
        }
    }

    private function getRandomCategory($type)
    {
        if ($type === 'credit') {
            $categories = [
                'auction_payment',
                'pos_transaction',
                'bonus',
                'refund',
                'commission'
            ];
        } else {
            $categories = [
                'withdrawal',
                'fee',
                'penalty',
                'refund'
            ];
        }
        
        return $categories[array_rand($categories)];
    }

    private function getRandomDescription($category)
    {
        $descriptions = [
            'auction_payment' => [
                'Payment from auction #12345',
                'Payment from auction #12346',
                'Payment from auction #12347',
                'Auction completion payment',
                'Winning bid payment'
            ],
            'pos_transaction' => [
                'POS transaction payment',
                'Direct order payment',
                'Customer payment',
                'POS sale commission',
                'Transaction revenue'
            ],
            'withdrawal' => [
                'Withdrawal request #001',
                'Withdrawal request #002',
                'Monthly withdrawal',
                'Emergency withdrawal',
                'Scheduled withdrawal'
            ],
            'bonus' => [
                'Performance bonus',
                'Loyalty bonus',
                'Referral bonus',
                'Achievement bonus',
                'Special bonus'
            ],
            'fee' => [
                'Withdrawal fee',
                'Processing fee',
                'Service fee',
                'Transaction fee',
                'Maintenance fee'
            ]
        ];
        
        $categoryDescriptions = $descriptions[$category] ?? ['General transaction'];
        return $categoryDescriptions[array_rand($categoryDescriptions)];
    }

    private function getRandomReferenceId($category)
    {
        if (in_array($category, ['auction_payment', 'pos_transaction'])) {
            return rand(1000, 9999);
        }
        return null;
    }

    private function getRandomReferenceType($category)
    {
        if (in_array($category, ['auction_payment', 'pos_transaction'])) {
            return $category === 'auction_payment' ? 'auction' : 'transaction';
        }
        return null;
    }

    private function getRandomMetadata($category)
    {
        $metadata = [
            'auction_payment' => [
                'auction_id' => rand(1000, 9999),
                'user_id' => rand(1, 10),
                'admin_fee_deducted' => rand(5000, 50000)
            ],
            'pos_transaction' => [
                'transaction_id' => rand(1000, 9999),
                'customer_id' => rand(1, 20),
                'commission_rate' => rand(5, 15)
            ],
            'withdrawal' => [
                'withdrawal_id' => rand(1000, 9999),
                'bank_code' => rand(100, 999),
                'processing_time' => rand(1, 3) . ' days'
            ]
        ];
        
        return $metadata[$category] ?? [];
    }

    private function getRandomWithdrawalMethod()
    {
        $methods = ['bank_transfer', 'ewallet', 'cash'];
        return $methods[array_rand($methods)];
    }

    private function getRandomAccountNumber($method)
    {
        if ($method === 'bank_transfer') {
            return rand(1000000000, 9999999999);
        } elseif ($method === 'ewallet') {
            return rand(1000000000, 9999999999);
        }
        return null;
    }

    private function getRandomBankName($method)
    {
        if ($method === 'bank_transfer') {
            $banks = ['BCA', 'BNI', 'BRI', 'MANDIRI', 'BSI', 'PERMATA'];
            return $banks[array_rand($banks)];
        }
        return null;
    }

    private function getRandomWithdrawalStatus()
    {
        $statuses = ['pending', 'approved', 'processing', 'completed', 'failed', 'cancelled'];
        $weights = [20, 25, 15, 30, 5, 5]; // 20% pending, 25% approved, 15% processing, 30% completed, 5% failed, 5% cancelled
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'pending';
    }

    private function getRandomWithdrawalNotes()
    {
        $notes = [
            'Withdrawal for business expenses',
            'Monthly salary payment',
            'Equipment purchase',
            'Emergency fund',
            'Investment capital',
            'Operational expenses',
            'Tax payment',
            'Insurance payment'
        ];
        
        return $notes[array_rand($notes)];
    }

    private function calculateWithdrawalFee($amount, $method)
    {
        $fees = [
            'bank_transfer' => 5000,
            'ewallet' => 2500,
            'cash' => 10000
        ];
        
        return $fees[$method] ?? 5000;
    }

    private function getRandomAdminNotes()
    {
        $notes = [
            'Withdrawal approved after verification',
            'Documents verified, processing withdrawal',
            'Withdrawal completed successfully',
            'Additional verification required',
            'Withdrawal processed as requested',
            'All requirements met, approved',
            'Standard processing time applied',
            'Withdrawal completed within SLA'
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getRandomAdminId()
    {
        $admin = User::where('usertype', 'dev')->first();
        return $admin ? $admin->id : 1;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\VendorWithdrawal;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessAutomaticWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'withdrawal:process-automatic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automatic withdrawals for vendors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic withdrawal processing...');

        try {
            // Get vendors with automatic withdrawal enabled
            $vendors = Vendor::where('auto_withdrawal_enabled', true)
                ->where('auto_withdrawal_date', now()->day)
                ->with('getOrCreateWallet')
                ->get();

            $this->info("Found {$vendors->count()} vendors for automatic withdrawal");

            $processedCount = 0;
            $totalAmount = 0;

            foreach ($vendors as $vendor) {
                try {
                    $wallet = $vendor->getOrCreateWallet();
                    $minWithdrawal = config('app.min_withdrawal', 50000);
                    $autoWithdrawalAmount = $vendor->auto_withdrawal_amount ?? $wallet->balance;

                    // Check if vendor has sufficient balance
                    if ($wallet->balance < $minWithdrawal) {
                        $this->warn("Vendor {$vendor->name} has insufficient balance: Rp " . number_format($wallet->balance, 0, ',', '.'));
                        continue;
                    }

                    // Check if vendor has pending withdrawal
                    $pendingWithdrawal = VendorWithdrawal::where('vendor_id', $vendor->id)
                        ->whereIn('status', ['pending', 'approved', 'processing'])
                        ->first();

                    if ($pendingWithdrawal) {
                        $this->warn("Vendor {$vendor->name} has pending withdrawal");
                        continue;
                    }

                    // Create automatic withdrawal
                    $withdrawal = VendorWithdrawal::createRequest(
                        $vendor->id,
                        $autoWithdrawalAmount,
                        $vendor->auto_withdrawal_method ?? 'bank_transfer',
                        $vendor->auto_withdrawal_account_number,
                        $vendor->auto_withdrawal_account_name,
                        $vendor->auto_withdrawal_bank_name,
                        'Automatic withdrawal - ' . now()->format('Y-m-d')
                    );

                    // Auto approve if configured
                    if (config('app.auto_approve_withdrawals', false)) {
                        $admin = User::where('usertype', 'admin')->first();
                        if ($admin) {
                            $withdrawal->approve($admin->id, 'Automatic approval');
                        }
                    }

                    $processedCount++;
                    $totalAmount += $autoWithdrawalAmount;

                    $this->info("Created automatic withdrawal for {$vendor->name}: Rp " . number_format($autoWithdrawalAmount, 0, ',', '.'));

                    Log::info('Automatic withdrawal created', [
                        'vendor_id' => $vendor->id,
                        'withdrawal_id' => $withdrawal->id,
                        'amount' => $autoWithdrawalAmount,
                        'method' => $vendor->auto_withdrawal_method
                    ]);
                } catch (\Exception $e) {
                    $this->error("Failed to process automatic withdrawal for {$vendor->name}: " . $e->getMessage());

                    Log::error('Automatic withdrawal processing failed', [
                        'vendor_id' => $vendor->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->info("Automatic withdrawal processing completed");
            $this->info("Processed: {$processedCount} vendors");
            $this->info("Total amount: Rp " . number_format($totalAmount, 0, ',', '.'));

            Log::info('Automatic withdrawal processing completed', [
                'processed_count' => $processedCount,
                'total_amount' => $totalAmount
            ]);
        } catch (\Exception $e) {
            $this->error("Automatic withdrawal processing failed: " . $e->getMessage());

            Log::error('Automatic withdrawal processing failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
}

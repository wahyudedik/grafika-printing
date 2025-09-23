<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\XenditPayment;
use App\Models\Auction;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('💳 Creating payment data with Xendit integration...');

        $auctions = Auction::where('status', 'active')->get();

        foreach ($auctions as $auction) {
            $this->createPaymentsForAuction($auction);
        }

        $this->command->info('✅ Payment seeding completed successfully!');
    }

    private function createPaymentsForAuction($auction)
    {
        $paymentMethods = ['BANK_TRANSFER', 'CREDIT_CARD', 'EWALLET', 'RETAIL_OUTLET'];
        $paymentStatuses = ['pending', 'paid', 'expired', 'failed'];
        $bankCodes = ['BCA', 'BNI', 'BRI', 'MANDIRI', 'BSI'];

        // Create 1-3 payments per auction
        $paymentCount = rand(1, 3);

        for ($i = 0; $i < $paymentCount; $i++) {
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $status = $paymentStatuses[array_rand($paymentStatuses)];
            $amount = $auction->budget + rand(10000, 100000); // Add admin fees

            XenditPayment::create([
                'auction_id' => $auction->id,
                'user_id' => $auction->user_id,
                'external_id' => 'payment_' . $auction->id . '_' . time() . '_' . $i,
                'amount' => $amount,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'bank_code' => in_array($paymentMethod, ['BANK_TRANSFER']) ? $bankCodes[array_rand($bankCodes)] : null,
                'account_number' => in_array($paymentMethod, ['BANK_TRANSFER']) ? '1234567890' : null,
                'expiry_date' => now()->addDays(1),
                'created' => now(),
                'updated' => now(),
                'uuid' => Str::uuid()->toString(),
            ]);
        }
    }
}

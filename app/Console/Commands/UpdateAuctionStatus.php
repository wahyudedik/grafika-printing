<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\XenditPayment;
use App\Services\XenditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAuctionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auction:update-status {auction_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update auction status based on Xendit payment status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $auctionId = $this->argument('auction_id');

        if ($auctionId) {
            $this->updateSingleAuction($auctionId);
        } else {
            $this->updateAllAuctions();
        }
    }

    protected function updateSingleAuction($auctionId)
    {
        $auction = Auction::find($auctionId);

        if (!$auction) {
            $this->error("Auction with ID {$auctionId} not found.");
            return;
        }

        if ($auction->status !== 'waiting_payment') {
            $this->info("Auction {$auctionId} is not in waiting_payment status.");
            return;
        }

        $this->updateAuctionFromXendit($auction);
    }

    protected function updateAllAuctions()
    {
        $auctions = Auction::where('status', 'waiting_payment')->get();

        $this->info("Found {$auctions->count()} auctions in waiting_payment status.");

        foreach ($auctions as $auction) {
            $this->updateAuctionFromXendit($auction);
        }
    }

    protected function updateAuctionFromXendit(Auction $auction)
    {
        try {
            $xenditService = app(XenditService::class);

            // Get payment record
            $payment = XenditPayment::where('auction_id', $auction->id)->first();

            if (!$payment) {
                $this->warn("No payment record found for auction {$auction->id}");
                return;
            }

            // Get payment status from Xendit
            $xenditData = $xenditService->getPaymentLink($payment->xendit_id);

            if (!$xenditData) {
                $this->warn("Could not get payment status from Xendit for auction {$auction->id}");
                return;
            }

            $this->info("Xendit status for auction {$auction->id}: {$xenditData['status']}");

            if ($xenditData['status'] === 'PAID' || $xenditData['status'] === 'SETTLED') {
                // Update auction status
                $auction->update(['status' => 'paid']);

                // Update payment status
                $payment->update([
                    'status' => 'paid',
                    'webhook_data' => $xenditData
                ]);

                // Process auction payment
                $this->processAuctionPayment($auction, $payment);

                $this->info("✅ Auction {$auction->id} status updated to paid");
            } else {
                $this->info("Auction {$auction->id} payment is still {$xenditData['status']}");
            }
        } catch (\Exception $e) {
            $this->error("Error updating auction {$auction->id}: " . $e->getMessage());
            Log::error('Error updating auction status', [
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function processAuctionPayment(Auction $auction, XenditPayment $payment)
    {
        try {
            // Find winning bid
            $winningBid = $auction->bids()
                ->where('status', 'accepted')
                ->first();

            if ($winningBid) {
                // Create order in vendor's POS system
                $auctionToPosService = new \App\Services\AuctionToPosService();
                $transaction = $auctionToPosService->createTransactionFromAuction($auction, $winningBid);

                // Add funds to vendor wallet
                $wallet = \App\Models\VendorWallet::firstOrCreate(['vendor_id' => $winningBid->vendor_id]);
                $wallet->increment('balance', (float) $payment->amount);

                Log::info('Auction payment processed successfully', [
                    'auction_id' => $auction->id,
                    'payment_id' => $payment->id,
                    'vendor_id' => $winningBid->vendor_id,
                    'amount' => $payment->amount
                ]);

                $this->info("✅ Payment processed for auction {$auction->id}");
            }
        } catch (\Exception $e) {
            $this->error("Error processing payment for auction {$auction->id}: " . $e->getMessage());
            Log::error('Error processing auction payment', [
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

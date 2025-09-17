<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\XenditPayment;
use App\Services\XenditService;
use App\Models\Auction;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;

class XenditWebhookController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    /**
     * Handle Xendit webhook callbacks
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            // Verify webhook signature
            $signature = $request->header('x-xendit-signature');
            $payload = $request->getContent();

            if (!$this->xenditService->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Xendit webhook signature verification failed', [
                    'signature' => $signature,
                    'payload' => $payload
                ]);

                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $data = $request->json()->all();
            $event = $data['event'];

            Log::info('Xendit webhook received', [
                'event' => $event,
                'data' => $data
            ]);

            switch ($event) {
                case 'payment_link.paid':
                    $this->handlePaymentLinkPaid($data);
                    break;

                case 'payment_link.expired':
                    $this->handlePaymentLinkExpired($data);
                    break;

                case 'xenpayment.paid':
                    $this->handleXenPaymentPaid($data);
                    break;

                case 'xenpayment.expired':
                    $this->handleXenPaymentExpired($data);
                    break;

                default:
                    Log::info('Unhandled Xendit webhook event', ['event' => $event]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Xendit webhook processing error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Handle payment link paid event
     */
    protected function handlePaymentLinkPaid(array $data): void
    {
        $paymentData = $data['data'];
        $externalId = $paymentData['external_id'];

        // Update payment record
        $payment = XenditPayment::where('external_id', $externalId)->first();

        if ($payment) {
            $payment->update([
                'status' => 'paid',
                'payment_method' => $paymentData['payment_method'] ?? null,
                'paid_at' => now(),
                'webhook_data' => $data
            ]);

            // Process auction payment
            $this->processAuctionPayment($payment);
        }
    }

    /**
     * Handle payment link expired event
     */
    protected function handlePaymentLinkExpired(array $data): void
    {
        $paymentData = $data['data'];
        $externalId = $paymentData['external_id'];

        $payment = XenditPayment::where('external_id', $externalId)->first();

        if ($payment) {
            $payment->update([
                'status' => 'expired',
                'webhook_data' => $data
            ]);
        }
    }

    /**
     * Handle xenpayment paid event
     */
    protected function handleXenPaymentPaid(array $data): void
    {
        $paymentData = $data['data'];
        $externalId = $paymentData['external_id'];

        $payment = XenditPayment::where('external_id', $externalId)->first();

        if ($payment) {
            $payment->update([
                'status' => 'paid',
                'payment_method' => $paymentData['payment_method'] ?? null,
                'paid_at' => now(),
                'webhook_data' => $data
            ]);

            // Process auction payment
            $this->processAuctionPayment($payment);
        }
    }

    /**
     * Handle xenpayment expired event
     */
    protected function handleXenPaymentExpired(array $data): void
    {
        $paymentData = $data['data'];
        $externalId = $paymentData['external_id'];

        $payment = XenditPayment::where('external_id', $externalId)->first();

        if ($payment) {
            $payment->update([
                'status' => 'expired',
                'webhook_data' => $data
            ]);
        }
    }

    /**
     * Process auction payment after successful payment
     */
    protected function processAuctionPayment(XenditPayment $payment): void
    {
        try {
            // Extract auction ID from external_id (format: auction_{id}_{timestamp})
            if (preg_match('/^auction_(\d+)_/', $payment->external_id, $matches)) {
                $auctionId = $matches[1];
                $auction = Auction::find($auctionId);

                if ($auction) {
                    // Update auction status to paid
                    $auction->update(['status' => 'paid']);

                    // Find winning bid
                    $winningBid = $auction->bids()
                        ->where('is_winner', true)
                        ->first();

                    if ($winningBid) {
                        // Create order in vendor's POS system
                        $this->createVendorOrder($auction, $winningBid);

                        // Add funds to vendor wallet
                        $this->addToVendorWallet($winningBid->vendor_id, (float) $payment->amount);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing auction payment', [
                'payment_id' => $payment->id,
                'external_id' => $payment->external_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create order in vendor's POS system
     */
    protected function createVendorOrder(Auction $auction, $winningBid): void
    {
        // This would integrate with your existing POS system
        // For now, we'll just log the action
        Log::info('Creating vendor order for auction', [
            'auction_id' => $auction->id,
            'vendor_id' => $winningBid->vendor_id,
            'amount' => $winningBid->amount
        ]);

        // TODO: Implement actual order creation in POS system
    }

    /**
     * Add funds to vendor wallet
     */
    protected function addToVendorWallet(int $vendorId, float $amount): void
    {
        try {
            $wallet = VendorWallet::firstOrCreate(['vendor_id' => $vendorId]);

            // Add transaction
            VendorWalletTransaction::create([
                'vendor_wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => 'Payment from auction',
                'status' => 'completed'
            ]);

            // Update wallet balance
            $wallet->increment('balance', $amount);
        } catch (\Exception $e) {
            Log::error('Error adding funds to vendor wallet', [
                'vendor_id' => $vendorId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\XenditPayment;
use App\Services\XenditService;
use App\Services\StockService;
use App\Models\Auction;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Notifications\VendorNewOrderNotification;
use App\Services\AuditLogService;

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
            // Log incoming webhook for debugging
            Log::info('Xendit webhook received', [
                'method' => $request->method(),
                'ip' => $request->ip(),
                'content_type' => $request->header('Content-Type'),
            ]);

            // Verify webhook signature
            $signature = $request->header('x-xendit-signature');
            $payload = $request->getContent();

            // For development, we might want to skip signature verification temporarily
            if (config('app.debug') && empty($signature)) {
                Log::warning('Skipping webhook signature verification in debug mode');
            } else {
                if (!$this->xenditService->verifyWebhookSignature($payload, $signature)) {
                    Log::warning('Xendit webhook signature verification failed');

                    // Return 200 to prevent retries, but log the issue
                    return response()->json(['status' => 'ignored', 'reason' => 'Invalid signature']);
                }
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

                case 'batch_disbursement.completed':
                    $this->handleBatchDisbursementCompleted($data);
                    break;

                case 'batch_disbursement.failed':
                    $this->handleBatchDisbursementFailed($data);
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

            // Return 200 to prevent retries, but log the error
            return response()->json(['status' => 'error', 'message' => 'Processing failed but acknowledged']);
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

            // Route based on external_id prefix
            if (preg_match('/^pos[_-]/', $externalId)) {
                $this->processPosPayment($payment, $data);
            } elseif (preg_match('/^auction[_-]/', $externalId)) {
                $this->processAuctionPayment($payment);
            } else {
                Log::info('Unhandled payment prefix in payment_link.paid', ['external_id' => $externalId]);
            }
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

            // Route based on external_id prefix
            if (preg_match('/^pos[_-]/', $externalId)) {
                $this->processPosPayment($payment, $data);
            } elseif (preg_match('/^auction[_-]/', $externalId)) {
                $this->processAuctionPayment($payment);
            } else {
                Log::info('Unhandled payment prefix in xenpayment.paid', ['external_id' => $externalId]);
            }
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

            // Restore stock for POS payment on expiry
            if (preg_match('/^pos[_-]/', $externalId)) {
                $this->restorePosStock($payment);
            }
        }
    }

    /**
     * Handle payment link expired event — also restore stock for POS
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

            // Restore stock for POS payment on expiry
            if (preg_match('/^pos[_-]/', $externalId)) {
                $this->restorePosStock($payment);
            }
        }
    }

    /**
     * Process auction payment after successful payment
     */
    protected function processAuctionPayment(XenditPayment $payment): void
    {
        try {
            // Extract auction ID from external_id (format: auction_{id}_{timestamp})
            // Support both formats: auction_{id}_{ts} and auction-{id}-{ts}
            if (preg_match('/^auction[_-](\d+)[_-]/', $payment->external_id, $matches)) {
                $auctionId = $matches[1];
                $auction = Auction::find($auctionId);

                if ($auction) {
                    // Update auction status to paid
                    $auction->update(['status' => 'paid']);

                    // Find winning bid
                    $winningBid = $auction->bids()
                        ->where('is_winning', true)
                        ->first();

                    if ($winningBid) {
                        // Create order in vendor's POS system
                        $this->createVendorOrder($auction, $winningBid);

                        // NOTE: Do NOT add funds to vendor wallet here!
                        // Funds are only released to vendor wallet after user confirms delivery
                        // via DeliveryConfirmationController::processVendorPayment()
                        // This prevents double-payment to vendors.

                        Log::info('Auction payment processed successfully', [
                            'auction_id' => $auction->id,
                            'payment_id' => $payment->id,
                            'vendor_id' => $winningBid->vendor_id,
                            'amount' => $payment->amount,
                            'note' => 'Payment received. Funds held until delivery confirmation.'
                        ]);
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
     * Process POS payment after successful payment
     */
    protected function processPosPayment(XenditPayment $payment, array $data): void
    {
        try {
            // Extract transaksi ID from external_id: pos_{id}_{timestamp}
            if (preg_match('/^pos[_-](\d+)[_-]/', $payment->external_id, $matches)) {
                $transaksiId = $matches[1];
                $transaksi = \App\Models\Vendor\Transaksi::find($transaksiId);

                if ($transaksi) {
                    // Update transaksi status
                    $transaksi->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                        'paid_at' => now(),
                        'diproses_at' => now(),
                    ]);

                    // Update XenditPayment transaksi_id if not set
                    if (!$payment->transaksi_id) {
                        $payment->update(['transaksi_id' => $transaksi->id]);
                    }

                    // Decrement stock
                    $stockService = app(StockService::class);
                    $stockService->decrementStock($transaksi);

                    // Send notification to vendor
                    $vendor = $transaksi->vendor;
                    if ($vendor) {
                        $vendor->notify(new VendorNewOrderNotification($transaksi));
                    }

                    // Audit log
                    AuditLogService::logFinancialTransaction([
                        'vendor_id' => $transaksi->vendor_id,
                        'action_type' => 'payment_completed',
                        'entity_type' => 'Transaksi',
                        'entity_id' => $transaksi->id,
                        'amount' => $payment->amount,
                        'status' => 'completed',
                        'transaction_reference' => $payment->external_id,
                        'notes' => "POS payment completed via webhook - #{$transaksi->kode}",
                    ]);

                    Log::info('POS payment processed successfully', [
                        'transaksi_id' => $transaksi->id,
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                    ]);
                } else {
                    Log::warning('POS payment: Transaksi not found', [
                        'transaksi_id' => $transaksiId,
                        'external_id' => $payment->external_id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing POS payment', [
                'payment_id' => $payment->id,
                'external_id' => $payment->external_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Restore stock for expired/failed POS payment
     */
    protected function restorePosStock(XenditPayment $payment): void
    {
        try {
            if (preg_match('/^pos[_-](\d+)[_-]/', $payment->external_id, $matches)) {
                $transaksiId = $matches[1];
                $transaksi = \App\Models\Vendor\Transaksi::find($transaksiId);

                if ($transaksi && $transaksi->status === 'payment_pending') {
                    // Only restore if stock was already decremented (status was processing)
                    // If status is still payment_pending, stock was not yet decremented
                    Log::info('POS payment expired, no stock to restore (payment was still pending)', [
                        'transaksi_id' => $transaksi->id,
                    ]);
                } elseif ($transaksi) {
                    $stockService = app(StockService::class);
                    $stockService->restoreStock($transaksi);

                    Log::info('Stock restored for expired POS payment', [
                        'transaksi_id' => $transaksi->id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error restoring stock for expired POS payment', [
                'payment_id' => $payment->id,
                'external_id' => $payment->external_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create order in vendor's POS system
     */
    protected function createVendorOrder(Auction $auction, $winningBid): void
    {
        try {
            // Use AuctionToPosService to create POS transaction
            $auctionToPosService = new \App\Services\AuctionToPosService();
            $transaction = $auctionToPosService->createTransactionFromAuction($auction, $winningBid);

            Log::info('Vendor order created successfully', [
                'auction_id' => $auction->id,
                'vendor_id' => $winningBid->vendor_id,
                'transaction_id' => $transaction->id,
                'amount' => $winningBid->bid_amount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create vendor order', [
                'auction_id' => $auction->id,
                'vendor_id' => $winningBid->vendor_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
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

    /**
     * Handle batch disbursement completed event
     */
    protected function handleBatchDisbursementCompleted(array $data): void
    {
        try {
            $disbursementData = $data['data'];

            Log::info('Batch disbursement completed', [
                'batch_id' => $disbursementData['id'] ?? null,
                'status' => $disbursementData['status'] ?? null,
                'data' => $disbursementData
            ]);

            // Update withdrawal status if exists
            if (isset($disbursementData['id'])) {
                // Find withdrawal record by batch ID
                $withdrawal = \App\Models\VendorWithdrawal::where('batch_id', $disbursementData['id'])->first();

                if ($withdrawal) {
                    $withdrawal->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'webhook_data' => $data
                    ]);

                    Log::info('Withdrawal status updated to completed', [
                        'withdrawal_id' => $withdrawal->id,
                        'batch_id' => $disbursementData['id']
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error handling batch disbursement completed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Handle batch disbursement failed event
     */
    protected function handleBatchDisbursementFailed(array $data): void
    {
        try {
            $disbursementData = $data['data'];

            Log::info('Batch disbursement failed', [
                'batch_id' => $disbursementData['id'] ?? null,
                'status' => $disbursementData['status'] ?? null,
                'data' => $disbursementData
            ]);

            // Update withdrawal status if exists
            if (isset($disbursementData['id'])) {
                // Find withdrawal record by batch ID
                $withdrawal = \App\Models\VendorWithdrawal::where('batch_id', $disbursementData['id'])->first();

                if ($withdrawal) {
                    $withdrawal->update([
                        'status' => 'failed',
                        'failed_at' => now(),
                        'webhook_data' => $data
                    ]);

                    Log::info('Withdrawal status updated to failed', [
                        'withdrawal_id' => $withdrawal->id,
                        'batch_id' => $disbursementData['id']
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error handling batch disbursement failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }
}

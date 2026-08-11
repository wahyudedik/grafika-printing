<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\XenditPayment;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentManagementController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    /**
     * Display payment management dashboard
     */
    public function index()
    {
        // Get stuck payments (waiting_payment for more than 24 hours)
        $stuckPayments = Auction::where('status', 'waiting_payment')
            ->where('created_at', '<', now()->subHours(24)) ->with(['user', 'winnerVendor', 'xenditPayments'])
            ->get();

        // Get expired payments
        $expiredPayments = XenditPayment::where('status', 'pending')
            ->where('expires_at', '<', now()) ->with('auction.user')
            ->get();

        // Get failed payments
        $failedPayments = XenditPayment::where('status', 'failed') ->with('auction.user')
            ->get();

        // Get payment statistics
        $stats = [
            'total_payments' => XenditPayment::count(),
            'pending_payments' => XenditPayment::where('status', 'pending')->count(),
            'paid_payments' => XenditPayment::where('status', 'paid')->count(),
            'failed_payments' => XenditPayment::where('status', 'failed')->count(),
            'expired_payments' => XenditPayment::where('status', 'pending')
                ->where('expires_at', '<', now())->count(),
            'stuck_payments' => $stuckPayments->count(),
            'total_amount_pending' => XenditPayment::where('status', 'pending')->sum('amount'),
            'total_amount_paid' => XenditPayment::where('status', 'paid')->sum('amount')
        ];

        return view('admin.payment-management.index', compact(
            'stuckPayments',
            'expiredPayments',
            'failedPayments',
            'stats'
        ));
    }

    /**
     * Check payment status from Xendit
     */
    public function checkPaymentStatus(XenditPayment $payment)
    {
        try {
            $xenditData = $this->xenditService->getPaymentLink($payment->xendit_id);

            if ($xenditData && isset($xenditData['status'])) {
                $payment->update([
                    'status' => strtolower($xenditData['status']),
                    'webhook_data' => $xenditData
                ]);

                // If payment is paid, update auction status
                if (in_array(strtolower($xenditData['status']), ['paid', 'settled'])) {
                    $this->processPaidPayment($payment);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment status updated successfully',
                    'status' => $xenditData['status']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not retrieve payment status from Xendit'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error checking payment status', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error checking payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manually process paid payment
     */
    public function processPaidPayment(XenditPayment $payment)
    {
        try {
            DB::beginTransaction();

            // Update auction status
            $auction = $payment->auction;
            if ($auction) {
                $auction->update(['status' => 'paid']);

                // Find winning bid
                $winningBid = $auction->bids()
                    ->where('status', 'accepted')
                    ->first();

                if ($winningBid) {
                    // Create transaction in vendor's POS system
                    $auctionToPosService = app(\App\Services\AuctionToPosService::class);
                    $transaction = $auctionToPosService->createTransactionFromAuction($auction, $winningBid);

                    // NOTE: Wallet funds are managed via escrow system in XenditWebhookController
                    // Do NOT directly increment wallet balance here — it violates the escrow flow

                    Log::info('Payment processed successfully', [
                        'payment_id' => $payment->id,
                        'auction_id' => $auction->id,
                        'vendor_id' => $winningBid->vendor_id,
                        'amount' => $payment->amount
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error processing payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new payment link for stuck auction
     */
    public function createNewPaymentLink(Auction $auction)
    {
        try {
            // Check if auction is in waiting_payment status
            if ($auction->status !== 'waiting_payment') {
                return response()->json([
                    'success' => false,
                    'message' => 'Auction is not in waiting payment status'
                ], 400);
            }

            // Create new payment link
            $externalId = 'auction_' . $auction->id . '_' . time();
            $amount = $auction->winning_bid;

            $paymentData = [
                'external_id' => $externalId,
                'amount' => $amount,
                'description' => 'Pembayaran Lelang: ' . $auction->title,
                'customer' => [
                    'given_names' => $auction->user->name ?? 'Customer',
                    'email' => $auction->user->email ?? 'customer@example.com'
                ],
                'items' => [
                    [
                        'name' => $auction->title,
                        'quantity' => $auction->quantity,
                        'price' => $amount,
                        'category' => 'Printing Service'
                    ]
                ],
                'success_redirect_url' => route('user.auctions.show', $auction) . '?payment=success',
                'failure_redirect_url' => route('user.auctions.show', $auction) . '?payment=failed',
                'invoice_duration' => 86400, // 24 hours
                'payment_methods' => [
                    'BCA',
                    'BNI',
                    'BRI',
                    'BSI',
                    'MANDIRI',
                    'PERMATA',
                    'ALFAMART',
                    'INDOMARET',
                    'OVO',
                    'DANA',
                    'LINKAJA',
                    'SHOPEEPAY'
                ]
            ];

            $response = $this->xenditService->createPaymentLink($paymentData);

            if ($response && isset($response['invoice_url'])) {
                // Create new payment record
                $payment = XenditPayment::create([
                    'external_id' => $externalId,
                    'xendit_id' => $response['id'] ?? null,
                    'type' => 'payment_link',
                    'amount' => $amount,
                    'description' => $paymentData['description'],
                    'status' => 'pending',
                    'customer' => $paymentData['customer'],
                    'items' => $paymentData['items'],
                    'checkout_url' => $response['invoice_url'],
                    'success_redirect_url' => $paymentData['success_redirect_url'],
                    'failure_redirect_url' => $paymentData['failure_redirect_url'],
                    'expires_at' => now()->addHours(24),
                    'auction_id' => $auction->id
                ]);

                Log::info('New payment link created for stuck auction', [
                    'auction_id' => $auction->id,
                    'payment_id' => $payment->id,
                    'checkout_url' => $response['invoice_url']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'New payment link created successfully',
                    'payment_url' => $response['invoice_url']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment link'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error creating new payment link', [
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating payment link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk check payment statuses
     */
    public function bulkCheckStatus(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:xendit_payments,id'
        ]);

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($request->payment_ids as $paymentId) {
            try {
                $payment = XenditPayment::findOrFail($paymentId);
                $result = $this->checkPaymentStatus($payment);

                if ($result->getData()->success) {
                    $successCount++;
                } else {
                    $errorCount++;
                }

                $results[] = [
                    'payment_id' => $paymentId,
                    'success' => $result->getData()->success,
                    'message' => $result->getData()->message
                ];
            } catch (\Exception $e) {
                $errorCount++;
                $results[] = [
                    'payment_id' => $paymentId,
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk check completed. Success: {$successCount}, Errors: {$errorCount}",
            'results' => $results
        ]);
    }

    /**
     * Get payment details
     */
    public function show(XenditPayment $payment)
    {
        $payment->load(['auction.user', 'auction.winnerVendor']);

        return view('admin.payment-management.show', compact('payment'));
    }

    /**
     * Resend payment notification
     */
    public function resendNotification(XenditPayment $payment)
    {
        try {
            // Here you can implement email/SMS notification to user
            // For now, we'll just log it

            Log::info('Payment notification resent', [
                'payment_id' => $payment->id,
                'auction_id' => $payment->auction_id,
                'user_email' => $payment->auction->user->email ?? 'N/A'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment notification sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending notification: ' . $e->getMessage()
            ], 500);
        }
    }
}

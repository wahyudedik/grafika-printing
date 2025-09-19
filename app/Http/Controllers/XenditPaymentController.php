<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\XenditPayment;
use App\Models\Auction;
use App\Services\XenditService;

class XenditPaymentController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    /**
     * Show payment page for auction
     */
    public function showPaymentPage(Auction $auction)
    {
        // Check if auction is waiting for payment
        if ($auction->status !== 'waiting_payment') {
            return redirect()->route('auctions.show', $auction)
                ->with('error', 'Lelang ini tidak memerlukan pembayaran.');
        }

        // Check if user is the auction owner
        if ($auction->user_id !== Auth::id()) {
            abort(403);
        }

        return view('payments.auction-payment', compact('auction'));
    }

    /**
     * Create payment link for auction
     */
    public function createPaymentLink(Request $request, Auction $auction): JsonResponse
    {
        try {
            // Check if auction is waiting for payment
            if ($auction->status !== 'waiting_payment') {
                return response()->json(['error' => 'Lelang ini tidak memerlukan pembayaran.'], 400);
            }

            // Check if user is the auction owner
            if ($auction->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized.'], 403);
            }

            $request->validate([
                'payment_type' => 'required|in:payment_link,xenpayment',
                'customer' => 'nullable|array',
                'customer.given_names' => 'required_with:customer|string|max:255',
                'customer.email' => 'required_with:customer|email|max:255',
                'items' => 'nullable|array'
            ]);

            $externalId = 'auction_' . $auction->id . '_' . time();
            $amount = $auction->winning_bid; // Use winning bid amount

            $paymentData = [
                'external_id' => $externalId,
                'amount' => $amount,
                'description' => 'Pembayaran Lelang: ' . $auction->title,
                'customer' => $request->customer ?? [
                    'given_names' => Auth::user()->name ?? 'Customer',
                    'email' => Auth::user()->email ?? 'customer@example.com'
                ],
                'items' => $request->items ?? [
                    [
                        'name' => $auction->title,
                        'quantity' => $auction->quantity,
                        'price' => $amount,
                        'category' => 'Printing Service'
                    ]
                ],
                'success_redirect_url' => config('services.xendit.redirect_url') . route('auctions.show', $auction, false) . '?payment=success',
                'failure_redirect_url' => config('services.xendit.redirect_url') . route('auctions.show', $auction, false) . '?payment=failed',
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

            $response = null;
            if ($request->payment_type === 'payment_link') {
                Log::info('Creating payment link with data:', $paymentData);
                $response = $this->xenditService->createPaymentLink($paymentData);
                Log::info('Payment link response:', $response);
            } else {
                Log::info('Creating XenPayment with data:', $paymentData);
                $response = $this->xenditService->createXenPayment($paymentData);
                Log::info('XenPayment response:', $response);
            }

            if (!$response) {
                Log::error('Failed to create payment - response is null');
                return response()->json(['error' => 'Failed to create payment'], 500);
            }

            // Save payment record
            $payment = XenditPayment::create([
                'external_id' => $externalId,
                'xendit_id' => $response['id'] ?? null,
                'type' => $request->payment_type,
                'amount' => $amount,
                'description' => $paymentData['description'],
                'status' => 'pending',
                'customer' => $paymentData['customer'],
                'items' => $paymentData['items'],
                'checkout_url' => $response['checkout_url'] ?? null,
                'success_redirect_url' => $paymentData['success_redirect_url'],
                'failure_redirect_url' => $paymentData['failure_redirect_url'],
                'expires_at' => now()->addHours(24),
                'auction_id' => $auction->id
            ]);

            return response()->json([
                'success' => true,
                'payment' => $payment,
                'checkout_url' => $response['invoice_url'] ?? $response['checkout_url'] ?? null,
                'xenpayment_id' => $response['id'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating Xendit payment', [
                'auction_id' => $auction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(XenditPayment $payment): JsonResponse
    {
        try {
            if ($payment->type === 'payment_link') {
                $xenditData = $this->xenditService->getPaymentLink($payment->xendit_id);
            } else {
                $xenditData = $this->xenditService->getXenPayment($payment->xendit_id);
            }

            if ($xenditData) {
                $payment->update([
                    'status' => $xenditData['status'] ?? $payment->status,
                    'webhook_data' => $xenditData
                ]);
            }

            return response()->json([
                'payment' => $payment,
                'status' => $payment->status
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting payment status', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to get payment status'], 500);
        }
    }

    /**
     * Get available payment methods
     */
    public function getPaymentMethods(): JsonResponse
    {
        try {
            $methods = $this->xenditService->getAvailablePaymentMethods();

            return response()->json([
                'success' => true,
                'payment_methods' => $methods
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting payment methods', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to get payment methods'], 500);
        }
    }

    /**
     * Expire payment
     */
    public function expirePayment(XenditPayment $payment): JsonResponse
    {
        try {
            if ($payment->type === 'payment_link') {
                $response = $this->xenditService->expirePaymentLink($payment->xendit_id);
            } else {
                // XenPayment doesn't have expire functionality
                return response()->json(['error' => 'Cannot expire XenPayment'], 400);
            }

            if ($response) {
                $payment->update(['status' => 'expired']);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment expired successfully'
                ]);
            }

            return response()->json(['error' => 'Failed to expire payment'], 500);
        } catch (\Exception $e) {
            Log::error('Error expiring payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to expire payment'], 500);
        }
    }

    /**
     * Show payment page
     */
    public function showPayment(Request $request, XenditPayment $payment)
    {
        // Check if user has access to this payment
        if (!Auth::check()) {
            abort(403, 'Unauthorized access to payment');
        }

        return view('payments.xendit', compact('payment'));
    }
}

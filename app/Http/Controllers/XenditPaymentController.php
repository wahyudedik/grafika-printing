<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
     * Create payment link for auction
     */
    public function createPaymentLink(Request $request, Auction $auction): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'payment_type' => 'required|in:payment_link,xenpayment',
                'customer' => 'nullable|array',
                'items' => 'nullable|array'
            ]);

            $externalId = 'auction_' . $auction->id . '_' . time();

            $paymentData = [
                'external_id' => $externalId,
                'amount' => $request->amount,
                'description' => 'Payment for auction: ' . $auction->title,
                'customer' => $request->customer ?? [
                    'given_names' => auth()->user()?->name ?? 'Customer',
                    'email' => auth()->user()?->email ?? 'customer@example.com'
                ],
                'items' => $request->items ?? [
                    [
                        'name' => $auction->title,
                        'quantity' => 1,
                        'price' => $request->amount,
                        'category' => 'Printing Service'
                    ]
                ],
                'success_redirect_url' => route('auctions.show', $auction) . '?payment=success',
                'failure_redirect_url' => route('auctions.show', $auction) . '?payment=failed',
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
                $response = $this->xenditService->createPaymentLink($paymentData);
            } else {
                $response = $this->xenditService->createXenPayment($paymentData);
            }

            if (!$response) {
                return response()->json(['error' => 'Failed to create payment'], 500);
            }

            // Save payment record
            $payment = XenditPayment::create([
                'external_id' => $externalId,
                'xendit_id' => $response['id'] ?? null,
                'type' => $request->payment_type,
                'amount' => $request->amount,
                'description' => $paymentData['description'],
                'status' => 'pending',
                'customer' => $paymentData['customer'],
                'items' => $paymentData['items'],
                'checkout_url' => $response['checkout_url'] ?? null,
                'success_redirect_url' => $paymentData['success_redirect_url'],
                'failure_redirect_url' => $paymentData['failure_redirect_url'],
                'expires_at' => now()->addHours(24)
            ]);

            return response()->json([
                'success' => true,
                'payment' => $payment,
                'checkout_url' => $response['checkout_url'] ?? null,
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
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to payment');
        }

        return view('payments.xendit', compact('payment'));
    }
}

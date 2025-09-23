<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Services\AdminFeeService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentConfirmationController extends Controller
{
    protected $adminFeeService;
    protected $xenditService;

    public function __construct(AdminFeeService $adminFeeService, XenditService $xenditService)
    {
        $this->adminFeeService = $adminFeeService;
        $this->xenditService = $xenditService;
    }

    /**
     * Show payment confirmation page with fee breakdown
     */
    public function show(Auction $auction): View|RedirectResponse
    {
        // Get winning bid
        $winningBid = $auction->bids()
            ->where('is_winning', true)
            ->with('vendor')
            ->first();

        if (!$winningBid) {
            return redirect()
                ->route('auctions.show', $auction)
                ->with('error', 'No winning bid found for this auction.');
        }

        // Calculate fees
        $feeCalculation = $this->adminFeeService->calculateTotalFees(
            $winningBid->bid_amount,
            'bank_transfer' // Default payment method
        );

        return view('payments.confirmation', compact(
            'auction',
            'winningBid',
            'feeCalculation'
        ));
    }

    /**
     * Process payment confirmation and create payment link
     */
    public function process(Request $request, Auction $auction): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|string|in:bank_transfer,credit_card,ewallet,retail_outlet',
            'agree_terms' => 'required|accepted'
        ]);

        // Get winning bid
        $winningBid = $auction->bids()
            ->where('is_winning', true)
            ->with('vendor')
            ->first();

        if (!$winningBid) {
            return redirect()
                ->route('auctions.show', $auction)
                ->with('error', 'No winning bid found for this auction.');
        }

        try {
            // Calculate fees based on selected payment method
            $feeCalculation = $this->adminFeeService->calculateTotalFees(
                $winningBid->bid_amount,
                $request->payment_method
            );

            // Update auction with fee information
            $auction->update([
                'admin_fee_amount' => $feeCalculation['admin_fee'],
                'payment_gateway_fee' => $feeCalculation['payment_gateway_fee'],
                'total_amount_with_fees' => $feeCalculation['total_amount'],
                'vendor_receives' => $feeCalculation['vendor_receives'],
                'admin_receives' => $feeCalculation['admin_receives'],
                'fee_breakdown' => $feeCalculation['breakdown'],
                'fees_calculated' => true
            ]);

            // Create admin fee transaction
            $this->adminFeeService->createTransaction(
                $auction->id,
                $winningBid->vendor_id,
                $auction->user_id,
                $winningBid->bid_amount,
                $request->payment_method
            );

            // Create Xendit payment link
            $paymentData = [
                'external_id' => 'auction-' . $auction->id . '-' . time(),
                'amount' => $feeCalculation['total_amount'],
                'description' => "Payment for auction: {$auction->title}",
                'customer' => [
                    'given_names' => $auction->user->name,
                    'email' => $auction->user->email,
                ],
                'payment_methods' => [$request->payment_method],
                'success_redirect_url' => route('payments.success', $auction),
                'failure_redirect_url' => route('payments.failure', $auction),
                'callback_url' => route('xendit.webhook'),
                'expires_at' => now()->addDays(1)->toISOString(),
                'metadata' => [
                    'auction_id' => $auction->id,
                    'vendor_id' => $winningBid->vendor_id,
                    'bid_amount' => $winningBid->bid_amount,
                    'admin_fee' => $feeCalculation['admin_fee'],
                    'payment_gateway_fee' => $feeCalculation['payment_gateway_fee']
                ]
            ];

            $paymentLink = $this->xenditService->createPaymentLink($paymentData);

            // Create payment record
            $payment = $auction->xenditPayments()->create([
                'external_id' => $paymentData['external_id'],
                'amount' => $feeCalculation['total_amount'],
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'expiry_date' => now()->addDays(1),
                'created' => now(),
                'updated' => now(),
                'user_id' => $auction->user_id
            ]);

            return redirect($paymentLink['invoice_url'])
                ->with('success', 'Payment link created successfully! Please complete your payment.');
        } catch (\Exception $e) {
            return redirect()
                ->route('payments.confirmation', $auction)
                ->with('error', 'Failed to create payment: ' . $e->getMessage());
        }
    }

    /**
     * Show payment success page
     */
    public function success(Auction $auction): View
    {
        return view('payments.success', compact('auction'));
    }

    /**
     * Show payment failure page
     */
    public function failure(Auction $auction): View
    {
        return view('payments.failure', compact('auction'));
    }
}

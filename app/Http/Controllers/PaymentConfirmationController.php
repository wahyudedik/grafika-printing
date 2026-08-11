<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Services\AdminFeeService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\FlashMessage;
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
        // Authorization: only auction owner or admin can view payment confirmation
        if ($auction->user_id !== Auth::id()
            && !in_array(Auth::user()->usertype, ['dev', 'admin'])
        ) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        // Get winning bid
        $winningBid = $auction->bids()
            ->where('is_winning', true) ->with('vendor')
            ->first();

        if (!$winningBid) {
            return FlashMessage::error(redirect()->route('auctions.show', $auction), 'No winning bid found for this auction.');
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
        // Authorization: only auction owner can process payment
        if ($auction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk memproses pembayaran ini.');
        }

        $request->validate([
            'payment_method' => 'required|string|in:bank_transfer,credit_card,ewallet,retail_outlet,qris',
            'agree_terms' => 'required|accepted'
        ]);

        // Get winning bid
        $winningBid = $auction->bids()
            ->where('is_winning', true) ->with('vendor')
            ->first();

        if (!$winningBid) {
            return FlashMessage::error(redirect()->route('auctions.show', $auction), 'No winning bid found for this auction.');
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
                'external_id' => 'auction_' . $auction->id . '_' . time(),
                'amount' => $feeCalculation['total_amount'],
                'description' => "Payment for auction: {$auction->title}",
                'customer' => [
                    'given_names' => $auction->user->name,
                    'email' => $auction->user->email,
                ],
                'payment_methods' => [$request->payment_method],
                'success_redirect_url' => route('payments.success', $auction),
                'failure_redirect_url' => route('payments.failure', $auction),
                'callback_url' => route('api.xendit.webhook'),
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

            if (!isset($paymentLink['invoice_url'])) {
                throw new \Exception('Payment link URL not received from Xendit');
            }

            return FlashMessage::success(redirect($paymentLink['invoice_url']), 'Link pembayaran berhasil dibuat! Silakan selesaikan pembayaran Anda.');
        } catch (\Exception $e) {
            return FlashMessage::error(redirect()->route('payments.confirmation', $auction), 'Gagal membuat pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Show payment success page
     */
    public function success(Auction $auction): View
    {
        // Authorization: only auction owner can view success page
        if ($auction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return view('payments.success', compact('auction'));
    }

    /**
     * Show payment failure page
     */
    public function failure(Auction $auction): View
    {
        // Authorization: only auction owner can view failure page
        if ($auction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return view('payments.failure', compact('auction'));
    }
}

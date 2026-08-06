<?php

namespace App\Http\Controllers;

use App\Models\DeliveryConfirmation;
use App\Models\Auction;
use App\Models\VendorWallet;
use App\Models\XenditPayment;
use App\Models\FinancialAuditLog;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeliveryConfirmationController extends Controller
{
    /**
     * Display listing of user's delivery confirmations
     */
    public function index()
    {
        $confirmations = DeliveryConfirmation::with(['auction', 'vendor'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.delivery-confirmation.index', compact('confirmations'));
    }

    /**
     * Show delivery confirmation form
     */
    public function create(Auction $auction)
    {
        // Check if user owns this auction
        if ($auction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if auction is paid
        if ($auction->status !== 'paid') {
            abort(400, 'Auction must be paid first');
        }

        // Check if already confirmed
        $existingConfirmation = DeliveryConfirmation::where('auction_id', $auction->id)->first();
        if ($existingConfirmation) {
            return redirect()->route('user.auctions.show', $auction)
                ->with('info', 'Delivery confirmation already exists');
        }

        return view('user.delivery-confirmation.create', compact('auction'));
    }

    /**
     * Store delivery confirmation
     */
    public function store(Request $request, Auction $auction)
    {
        $request->validate([
            'delivery_status' => 'required|in:delivered,disputed',
            'delivery_notes' => 'nullable|string|max:1000',
            'user_rating' => 'nullable|integer|min:1|max:5',
            'user_feedback' => 'nullable|string|max:1000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'dispute_reason' => 'required_if:delivery_status,disputed|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Handle photo uploads
            $photoUrls = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('delivery-confirmations', 'public');
                    $photoUrls[] = Storage::url($path);
                }
            }

            // Create delivery confirmation
            $confirmation = DeliveryConfirmation::create([
                'auction_id' => $auction->id,
                'user_id' => Auth::id(),
                'vendor_id' => $auction->winner_vendor_id,
                'delivery_status' => $request->delivery_status,
                'delivery_date' => now(),
                'delivery_notes' => $request->delivery_notes,
                'user_rating' => $request->user_rating,
                'user_feedback' => $request->user_feedback,
                'photos' => $photoUrls,
                'dispute_reason' => $request->dispute_reason
            ]);

            // If delivered and confirmed, process payment to vendor
            if ($request->delivery_status === 'delivered') {
                $this->processVendorPayment($auction, $confirmation);
            }

            DB::commit();

            return redirect()->route('user.auctions.show', $auction)
                ->with('success', 'Delivery confirmation submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error submitting confirmation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show delivery confirmation details
     */
    public function show(DeliveryConfirmation $confirmation)
    {
        // Check if user has access
        if ($confirmation->user_id !== Auth::id() && $confirmation->vendor_id !== Auth::user()->vendor_id) {
            abort(403, 'Unauthorized');
        }

        return view('user.delivery-confirmation.show', compact('confirmation'));
    }

    /**
     * Confirm delivery (for vendors)
     */
    public function confirm(DeliveryConfirmation $confirmation)
    {
        // Check if vendor owns this confirmation
        if ($confirmation->vendor_id !== Auth::user()->vendor_id) {
            abort(403, 'Unauthorized');
        }

        if ($confirmation->delivery_status !== 'delivered') {
            abort(400, 'Can only confirm delivered items');
        }

        $confirmation->update([
            'delivery_status' => 'confirmed',
            'confirmed_at' => now()
        ]);

        // Process payment to vendor
        $this->processVendorPayment($confirmation->auction, $confirmation);

        return redirect()->back()
            ->with('success', 'Delivery confirmed successfully');
    }

    /**
     * Process payment to vendor
     * Flow: User bayar lelang → Vendor cetak → Vendor kirim → User bayar ongkir CASH → User konfirmasi → Vendor dapat bayar
     */
    private function processVendorPayment(Auction $auction, DeliveryConfirmation $confirmation)
    {
        // Get vendor wallet
        $wallet = VendorWallet::firstOrCreate(['vendor_id' => $auction->winner_vendor_id]);

        // Calculate amount to transfer (auction amount - admin fees)
        // Admin fee sudah dipotong saat user bayar lelang
        $amount = $auction->winning_bid;
        if ($auction->admin_fee_amount) {
            $amount = $auction->winning_bid - $auction->admin_fee_amount;
        }

        // Add to vendor wallet
        $wallet->addCredit(
            $amount,
            'auction_payment',
            'Payment from auction #' . $auction->id . ' (after delivery confirmation)',
            $auction->id,
            'auction',
            [
                'auction_id' => $auction->id,
                'user_id' => $auction->user_id,
                'delivery_confirmation_id' => $confirmation->id,
                'admin_fee_deducted' => $auction->admin_fee_amount,
                'shipping_paid_by_user' => 'cash_on_delivery'
            ]
        );

        // Update auction status
        $auction->update(['status' => 'completed']);

        // Log the payment process
        Log::info('Vendor payment processed', [
            'auction_id' => $auction->id,
            'vendor_id' => $auction->winner_vendor_id,
            'amount_paid_to_vendor' => $amount,
            'admin_fee_deducted' => $auction->admin_fee_amount,
            'delivery_confirmation_id' => $confirmation->id
        ]);
    }

    /**
     * Resolve dispute
     */
    public function resolveDispute(Request $request, DeliveryConfirmation $confirmation)
    {
        $request->validate([
            'resolution' => 'required|in:refund,partial_refund,rework',
            'admin_notes' => 'required|string|max:1000',
            'refund_amount' => 'required_if:resolution,refund,partial_refund|numeric|min:0'
        ]);

        // Only admin can resolve disputes
        if (Auth::user()->usertype !== 'dev') {
            abort(403, 'Only admin can resolve disputes');
        }

        try {
            DB::beginTransaction();

            $confirmation->update([
                'delivery_status' => 'resolved',
                'dispute_resolved_at' => now()
            ]);

            // Handle different resolutions
            switch ($request->resolution) {
                case 'refund':
                    $this->processRefund($confirmation->auction, $request->refund_amount);
                    break;
                case 'partial_refund':
                    $this->processRefund($confirmation->auction, $request->refund_amount);
                    break;
                case 'rework':
                    // Reset auction status for rework
                    $confirmation->auction->update(['status' => 'active']);
                    break;
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Dispute resolved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error resolving dispute: ' . $e->getMessage());
        }
    }

    /**
     * Process refund via Xendit
     * Flow: Admin resolve dispute → Refund via Xendit → Vendor wallet deducted → User notified
     */
    private function processRefund(Auction $auction, $amount)
    {
        // Find the original payment for this auction
        $payment = XenditPayment::where('auction_id', $auction->id)
            ->where('status', 'paid')
            ->first();

        if (!$payment) {
            throw new \Exception('No paid payment found for auction #' . $auction->id);
        }

        // Validate refund amount doesn't exceed original payment
        if ($amount > $payment->amount) {
            throw new \Exception('Refund amount (' . number_format($amount) . ') exceeds original payment amount (' . number_format($payment->amount) . ')');
        }

        // Call Xendit refund API
        $xenditService = app(XenditService::class);
        $refundResult = $xenditService->createRefund(
            $payment->xendit_id,
            (int) $amount,
            'Refund for auction #' . $auction->id . ' - Dispute resolved'
        );

        if (!$refundResult) {
            throw new \Exception('Failed to process refund via Xendit. Please try again or contact support.');
        }

        // Deduct from vendor wallet if payment was already released
        if ($auction->status === 'completed') {
            $wallet = VendorWallet::where('vendor_id', $auction->winner_vendor_id)->first();
            if ($wallet && $wallet->balance >= $amount) {
                $wallet->addDebit(
                    $amount,
                    'auction_refund',
                    'Refund deducted for auction #' . $auction->id . ' - Dispute resolved',
                    $auction->id,
                    'auction',
                    [
                        'auction_id' => $auction->id,
                        'refund_id' => $refundResult['id'] ?? null,
                        'refund_amount' => $amount,
                        'reason' => 'dispute_refund'
                    ]
                );
            }
        }

        // Update auction status
        $auction->update([
            'status' => 'refunded',
            'delivery_status' => 'refunded'
        ]);

        // Log the refund in financial audit
        FinancialAuditLog::create([
            'user_id' => Auth::id(),
            'vendor_id' => $auction->winner_vendor_id,
            'action_type' => 'refund',
            'entity_type' => 'auction',
            'entity_id' => $auction->id,
            'old_data' => [
                'status' => $auction->getOriginal('status'),
                'amount_paid' => $payment->amount
            ],
            'new_data' => [
                'status' => 'refunded',
                'refund_amount' => $amount,
                'refund_id' => $refundResult['id'] ?? null
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'transaction_reference' => $refundResult['id'] ?? null,
            'amount' => $amount,
            'status' => 'completed',
            'notes' => 'Refund processed for auction #' . $auction->id . ' - Dispute resolved',
            'risk_level' => FinancialAuditLog::RISK_HIGH
        ]);

        Log::info('Refund processed successfully', [
            'auction_id' => $auction->id,
            'payment_id' => $payment->id,
            'xendit_refund_id' => $refundResult['id'] ?? null,
            'refund_amount' => $amount,
            'vendor_id' => $auction->winner_vendor_id,
            'admin_id' => Auth::id()
        ]);
    }
}

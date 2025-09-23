<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auction;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AdminFeeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuctionApprovalController extends Controller
{
    protected $adminFeeService;

    public function __construct(AdminFeeService $adminFeeService)
    {
        $this->adminFeeService = $adminFeeService;
    }

    /**
     * Display pending auctions for approval
     */
    public function index(Request $request): View
    {
        $query = Auction::with(['user', 'bids.vendor'])
            ->pendingApproval()
            ->orderBy('created_at', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auctions = $query->paginate(20);

        return view('admin.auctions.approval.index', compact('auctions'));
    }

    /**
     * Show auction details for approval
     */
    public function show(Auction $auction): View
    {
        $auction->load(['user', 'bids.vendor', 'approvedBy']);

        return view('admin.auctions.approval.show', compact('auction'));
    }

    /**
     * Approve auction
     */
    public function approve(Request $request, Auction $auction): RedirectResponse
    {
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Calculate admin fees
            $feeCalculation = $this->adminFeeService->calculateTotalFees(
                $auction->budget,
                $request->payment_method ?? 'bank_transfer'
            );

            // Update auction with fees
            $auction->update([
                'admin_fee_amount' => $feeCalculation['admin_fee'],
                'payment_gateway_fee' => $feeCalculation['payment_gateway_fee'],
                'total_amount_with_fees' => $feeCalculation['total_amount'],
                'vendor_receives' => $feeCalculation['vendor_receives'],
                'admin_receives' => $feeCalculation['admin_receives'],
                'fee_breakdown' => $feeCalculation['breakdown'],
                'fees_calculated' => true
            ]);

            // Approve the auction
            $auction->approve(Auth::id(), $request->approval_notes);

            // Create admin fee transaction
            $this->adminFeeService->createTransaction(
                $auction->id,
                null, // vendor_id will be set when winner is selected
                $auction->user_id,
                $auction->budget,
                $request->payment_method ?? 'bank_transfer'
            );

            return redirect()
                ->route('admin.auctions.approval.index')
                ->with('success', 'Auction approved successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to approve auction: ' . $e->getMessage());
        }
    }

    /**
     * Reject auction
     */
    public function reject(Request $request, Auction $auction): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        try {
            $auction->reject(Auth::id(), $request->rejection_reason);

            return redirect()
                ->route('admin.auctions.approval.index')
                ->with('success', 'Auction rejected successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to reject auction: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve auctions
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $request->validate([
            'auction_ids' => 'required|array',
            'auction_ids.*' => 'exists:auctions,id'
        ]);

        $approved = 0;
        $errors = [];

        foreach ($request->auction_ids as $auctionId) {
            try {
                $auction = Auction::findOrFail($auctionId);

                if ($auction->isPendingApproval()) {
                    $auction->approve(Auth::id(), 'Bulk approved');
                    $approved++;
                }
            } catch (\Exception $e) {
                $errors[] = "Auction {$auctionId}: " . $e->getMessage();
            }
        }

        $message = "Successfully approved {$approved} auctions.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return redirect()
            ->route('admin.auctions.approval.index')
            ->with('success', $message);
    }

    /**
     * Get approval statistics
     */
    public function statistics(): View
    {
        $stats = [
            'pending' => Auction::pendingApproval()->count(),
            'approved_today' => Auction::approved()
                ->whereDate('admin_approval_date', today())
                ->count(),
            'rejected_today' => Auction::rejected()
                ->whereDate('admin_approval_date', today())
                ->count(),
            'total_approved' => Auction::approved()->count(),
            'total_rejected' => Auction::rejected()->count(),
        ];

        return view('admin.auctions.approval.statistics', compact('stats'));
    }

    /**
     * Get fee preview for auction
     */
    public function feePreview(Request $request, Auction $auction)
    {
        $request->validate([
            'payment_method' => 'required|string'
        ]);

        try {
            $feeCalculation = $this->adminFeeService->getFeePreview(
                $auction->budget,
                $request->payment_method
            );

            return response()->json([
                'success' => true,
                'data' => $feeCalculation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediationRequest;
use App\Models\EscrowPayment;
use App\Models\OrderTracking;
use App\Services\OrderTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MediationController extends Controller
{
    protected $orderTrackingService;

    public function __construct(OrderTrackingService $orderTrackingService)
    {
        $this->orderTrackingService = $orderTrackingService;
    }

    /**
     * Show mediation requests
     */
    public function index(Request $request): View
    {
        $query = MediationRequest::with(['auction', 'vendor', 'user', 'requestedBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $mediationRequests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.mediation.index', compact('mediationRequests'));
    }

    /**
     * Show mediation request details
     */
    public function show(MediationRequest $mediationRequest): View
    {
        $mediationRequest->load([
            'auction',
            'vendor',
            'user',
            'requestedBy',
            'resolvedBy'
        ]);

        return view('admin.mediation.show', compact('mediationRequest'));
    }

    /**
     * Start mediation review
     */
    public function startReview(MediationRequest $mediationRequest): RedirectResponse
    {
        $mediationRequest->update([
            'status' => MediationRequest::STATUS_IN_REVIEW,
            'admin_notes' => 'Mediation review started by admin'
        ]);

        return redirect()->back()
            ->with('success', 'Mediation review started');
    }

    /**
     * Resolve mediation
     */
    public function resolve(Request $request, MediationRequest $mediationRequest): RedirectResponse
    {
        $request->validate([
            'admin_decision' => 'required|string|in:favor_user,favor_vendor,compromise,no_fault',
            'resolution' => 'required|string|max:1000',
            'compensation_amount' => 'nullable|numeric|min:0',
            'penalty_amount' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($mediationRequest, $request) {
            // Resolve mediation
            $mediationRequest->resolve(
                Auth::id(),
                $request->admin_decision,
                $request->resolution,
                $request->compensation_amount ?? 0,
                $request->penalty_amount ?? 0
            );

            // Update escrow payment based on decision
            $this->handleEscrowPayment($mediationRequest, $request->admin_decision);

            // Update order tracking
            $orderTracking = OrderTracking::where('auction_id', $mediationRequest->auction_id)->first();
            if ($orderTracking) {
                $orderTracking->update([
                    'status' => OrderTracking::STATUS_COMPLETED,
                    'mediation_status' => 'resolved',
                    'mediation_resolution' => $request->resolution
                ]);
            }
        });

        return redirect()->route('admin.mediation.index')
            ->with('success', 'Mediation resolved successfully');
    }

    /**
     * Close mediation
     */
    public function close(MediationRequest $mediationRequest): RedirectResponse
    {
        $mediationRequest->close();

        return redirect()->back()
            ->with('success', 'Mediation closed');
    }

    /**
     * Handle escrow payment based on mediation decision
     */
    protected function handleEscrowPayment(MediationRequest $mediationRequest, string $decision)
    {
        $escrowPayment = EscrowPayment::where('auction_id', $mediationRequest->auction_id)->first();

        if (!$escrowPayment) {
            return;
        }

        switch ($decision) {
            case MediationRequest::DECISION_FAVOR_USER:
                // Refund to user
                $escrowPayment->refund('Mediation resolved in favor of user');
                break;

            case MediationRequest::DECISION_FAVOR_VENDOR:
                // Release to vendor
                $escrowPayment->release('Mediation resolved in favor of vendor');
                break;

            case MediationRequest::DECISION_COMPROMISE:
                // Partial release based on compromise
                $this->handleCompromisePayment($escrowPayment, $mediationRequest);
                break;

            case MediationRequest::DECISION_NO_FAULT:
                // Release to vendor (no fault found)
                $escrowPayment->release('Mediation resolved - no fault found');
                break;
        }
    }

    /**
     * Handle compromise payment
     */
    protected function handleCompromisePayment(EscrowPayment $escrowPayment, MediationRequest $mediationRequest)
    {
        $compensationAmount = $mediationRequest->compensation_amount ?? 0;
        $penaltyAmount = $mediationRequest->penalty_amount ?? 0;

        // Calculate final amounts
        $vendorAmount = $escrowPayment->vendor_amount - $compensationAmount - $penaltyAmount;
        $userRefund = $compensationAmount;

        if ($vendorAmount > 0) {
            // Release partial amount to vendor
            $escrowPayment->update([
                'vendor_amount' => $vendorAmount,
                'status' => EscrowPayment::STATUS_RELEASED,
                'released_at' => now(),
                'release_reason' => 'Mediation compromise - partial release'
            ]);
        }

        if ($userRefund > 0) {
            // Process user refund
            $this->processUserRefund($escrowPayment, $userRefund);
        }
    }

    /**
     * Process user refund
     */
    protected function processUserRefund(EscrowPayment $escrowPayment, float $amount)
    {
        // Implementation for user refund
        // This could involve creating a refund transaction
        // or updating user wallet balance
    }

    /**
     * Get mediation statistics
     */
    public function statistics(): View
    {
        $stats = [
            'total_requests' => MediationRequest::count(),
            'pending_requests' => MediationRequest::where('status', MediationRequest::STATUS_PENDING)->count(),
            'in_review' => MediationRequest::where('status', MediationRequest::STATUS_IN_REVIEW)->count(),
            'resolved' => MediationRequest::where('status', MediationRequest::STATUS_RESOLVED)->count(),
            'favor_user' => MediationRequest::where('admin_decision', MediationRequest::DECISION_FAVOR_USER)->count(),
            'favor_vendor' => MediationRequest::where('admin_decision', MediationRequest::DECISION_FAVOR_VENDOR)->count(),
            'compromise' => MediationRequest::where('admin_decision', MediationRequest::DECISION_COMPROMISE)->count(),
            'no_fault' => MediationRequest::where('admin_decision', MediationRequest::DECISION_NO_FAULT)->count()
        ];

        return view('admin.mediation.statistics', compact('stats'));
    }
}

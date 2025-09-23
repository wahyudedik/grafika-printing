<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\OrderTracking;
use App\Models\EscrowPayment;
use App\Models\MediationRequest;
use App\Services\OrderTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class OrderTrackingController extends Controller
{
    protected $orderTrackingService;

    public function __construct(OrderTrackingService $orderTrackingService)
    {
        $this->orderTrackingService = $orderTrackingService;
    }

    /**
     * Show order tracking for user
     */
    public function index(): View
    {
        $user = Auth::user();

        $orderTrackings = OrderTracking::where('user_id', $user->id)
            ->with(['auction', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.order-tracking.index', compact('orderTrackings'));
    }

    /**
     * Show specific order tracking
     */
    public function show(OrderTracking $orderTracking): View
    {
        // Check if user can view this order tracking
        if ($orderTracking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $orderTracking->load(['auction', 'vendor', 'user']);

        return view('user.order-tracking.show', compact('orderTracking'));
    }

    /**
     * Show vendor order tracking
     */
    public function vendorIndex(): View
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            abort(403, 'Vendor access required');
        }

        $orderTrackings = OrderTracking::where('vendor_id', $vendor->id)
            ->with(['auction', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vendor.order-tracking.index', compact('orderTrackings'));
    }

    /**
     * Update order status (vendor)
     */
    public function updateStatus(Request $request, OrderTracking $orderTracking): RedirectResponse
    {
        // Check if vendor can update this order tracking
        $vendor = Auth::user()->vendorUser->first();
        if (!$vendor || $orderTracking->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|string',
            'status_description' => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:100',
            'estimated_delivery' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000'
        ]);

        $this->orderTrackingService->updateStatus(
            $orderTracking,
            $request->status,
            $request->status_description,
            $request->tracking_number,
            $request->estimated_delivery,
            $request->notes
        );

        return redirect()->back()
            ->with('success', 'Order status updated successfully');
    }

    /**
     * Request mediation
     */
    public function requestMediation(Request $request, OrderTracking $orderTracking): RedirectResponse
    {
        // Check if user can request mediation for this order
        if ($orderTracking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'evidence_files' => 'nullable|array|max:5',
            'evidence_files.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $this->orderTrackingService->requestMediation(
            $orderTracking,
            $request->reason,
            $request->description,
            $request->file('evidence_files', [])
        );

        return redirect()->back()
            ->with('success', 'Mediation request submitted successfully');
    }

    /**
     * Confirm delivery (user)
     */
    public function confirmDelivery(Request $request, OrderTracking $orderTracking): RedirectResponse
    {
        // Check if user can confirm delivery for this order
        if ($orderTracking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'delivery_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500'
        ]);

        $this->orderTrackingService->confirmDelivery(
            $orderTracking,
            $request->file('delivery_photo'),
            $request->rating,
            $request->feedback
        );

        return redirect()->back()
            ->with('success', 'Delivery confirmed successfully');
    }

    /**
     * Get tracking status for API
     */
    public function getTrackingStatus(OrderTracking $orderTracking)
    {
        // Check if user can view this order tracking
        if ($orderTracking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json([
            'status' => $orderTracking->status,
            'status_label' => $orderTracking->status_label,
            'status_color' => $orderTracking->status_color,
            'status_description' => $orderTracking->status_description,
            'tracking_number' => $orderTracking->tracking_number,
            'estimated_delivery' => $orderTracking->estimated_delivery,
            'actual_delivery' => $orderTracking->actual_delivery,
            'notes' => $orderTracking->notes,
            'is_mediation_requested' => $orderTracking->is_mediation_requested,
            'mediation_status' => $orderTracking->mediation_status,
            'updated_at' => $orderTracking->updated_at
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\OrderTracking;
use App\Models\EscrowPayment;
use App\Models\MediationRequest;
use App\Models\VendorRating;
use App\Models\VendorWallet;
use App\Services\TenantManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderTrackingService
{
    protected $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Create order tracking after payment
     */
    public function createOrderTracking(Auction $auction)
    {
        DB::transaction(function () use ($auction) {
            // Create order tracking
            $orderTracking = OrderTracking::create([
                'auction_id' => $auction->id,
                'vendor_id' => $auction->winningBid->vendor_id,
                'user_id' => $auction->user_id,
                'status' => OrderTracking::STATUS_PAYMENT_RECEIVED,
                'status_description' => 'Payment received, waiting for vendor confirmation',
                'created_by' => $auction->user_id
            ]);

            // Create escrow payment
            $escrowPayment = EscrowPayment::create([
                'auction_id' => $auction->id,
                'vendor_id' => $auction->winningBid->vendor_id,
                'user_id' => $auction->user_id,
                'amount' => $auction->total_amount_with_fees,
                'admin_fee' => $auction->admin_fee_amount,
                'vendor_amount' => $auction->vendor_receives,
                'status' => EscrowPayment::STATUS_PENDING,
                'created_by' => $auction->user_id
            ]);

            // Update auction status
            $auction->update([
                'status' => 'in_production',
                'production_started_at' => now()
            ]);

            // Notify vendor
            $this->notifyVendor($auction, $orderTracking);
        });
    }

    /**
     * Update order status
     */
    public function updateStatus(
        OrderTracking $orderTracking,
        string $status,
        ?string $statusDescription = null,
        ?string $trackingNumber = null,
        ?string $estimatedDelivery = null,
        ?string $notes = null
    ) {
        $orderTracking->update([
            'status' => $status,
            'status_description' => $statusDescription,
            'tracking_number' => $trackingNumber,
            'estimated_delivery' => $estimatedDelivery,
            'notes' => $notes,
            'updated_by' => Auth::id()
        ]);

        // Notify user of status change
        $this->notifyUser($orderTracking);

        // If status is shipped, update auction
        if ($status === OrderTracking::STATUS_SHIPPED) {
            $orderTracking->auction->update([
                'delivery_status' => 'shipped',
                'tracking_number' => $trackingNumber
            ]);
        }
    }

    /**
     * Request mediation
     */
    public function requestMediation(
        OrderTracking $orderTracking,
        string $reason,
        string $description,
        array $evidenceFiles = []
    ) {
        if (!$orderTracking->canRequestMediation()) {
            throw new \Exception('Mediation cannot be requested at this stage');
        }

        DB::transaction(function () use ($orderTracking, $reason, $description, $evidenceFiles) {
            // Create mediation request
            $mediationRequest = MediationRequest::create([
                'auction_id' => $orderTracking->auction_id,
                'vendor_id' => $orderTracking->vendor_id,
                'user_id' => $orderTracking->user_id,
                'requested_by' => Auth::id(),
                'reason' => $reason,
                'description' => $description,
                'status' => MediationRequest::STATUS_PENDING,
                'evidence_files' => $this->storeEvidenceFiles($evidenceFiles)
            ]);

            // Update order tracking
            $orderTracking->update([
                'status' => OrderTracking::STATUS_MEDIATION,
                'is_mediation_requested' => true,
                'mediation_reason' => $reason,
                'mediation_status' => 'pending'
            ]);

            // Update escrow payment
            $escrowPayment = EscrowPayment::where('auction_id', $orderTracking->auction_id)->first();
            if ($escrowPayment) {
                $escrowPayment->dispute($reason);
            }

            // Notify admin
            $this->notifyAdminMediation($mediationRequest);
        });
    }

    /**
     * Confirm delivery
     */
    public function confirmDelivery(
        OrderTracking $orderTracking,
        $deliveryPhoto,
        int $rating,
        ?string $feedback = null
    ) {
        DB::transaction(function () use ($orderTracking, $deliveryPhoto, $rating, $feedback) {
            // Update order tracking
            $orderTracking->update([
                'status' => OrderTracking::STATUS_DELIVERED,
                'actual_delivery' => now(),
                'updated_by' => Auth::id()
            ]);

            // Store delivery photo
            $photoPath = $deliveryPhoto->store('delivery-photos', 'public');

            // Create vendor rating
            VendorRating::create([
                'vendor_id' => $orderTracking->vendor_id,
                'user_id' => $orderTracking->user_id,
                'auction_id' => $orderTracking->auction_id,
                'rating' => $rating,
                'comment' => $feedback,
                'is_verified' => true,
                'delivery_photo' => $photoPath
            ]);

            // Release escrow payment to vendor
            $this->releaseEscrowPayment($orderTracking);

            // Update auction
            $orderTracking->auction->update([
                'delivery_status' => 'delivered',
                'user_rating' => $rating,
                'user_feedback' => $feedback,
                'completion_date' => now()
            ]);

            // Notify vendor
            $this->notifyVendorDeliveryConfirmed($orderTracking);
        });
    }

    /**
     * Release escrow payment to vendor
     */
    protected function releaseEscrowPayment(OrderTracking $orderTracking)
    {
        $escrowPayment = EscrowPayment::where('auction_id', $orderTracking->auction_id)->first();

        if ($escrowPayment && $escrowPayment->isPending()) {
            $escrowPayment->release('Delivery confirmed by user');
        }
    }

    /**
     * Store evidence files
     */
    protected function storeEvidenceFiles(array $files): array
    {
        $storedFiles = [];

        foreach ($files as $file) {
            $path = $file->store('mediation-evidence', 'public');
            $storedFiles[] = $path;
        }

        return $storedFiles;
    }

    /**
     * Notify vendor of new order
     */
    protected function notifyVendor(Auction $auction, OrderTracking $orderTracking)
    {
        // Implementation for vendor notification
        // This could be email, SMS, or in-app notification
    }

    /**
     * Notify user of status change
     */
    protected function notifyUser(OrderTracking $orderTracking)
    {
        // Implementation for user notification
        // This could be email, SMS, or in-app notification
    }

    /**
     * Notify admin of mediation request
     */
    protected function notifyAdminMediation(MediationRequest $mediationRequest)
    {
        // Implementation for admin notification
        // This could be email, SMS, or in-app notification
    }

    /**
     * Notify vendor of delivery confirmation
     */
    protected function notifyVendorDeliveryConfirmed(OrderTracking $orderTracking)
    {
        // Implementation for vendor notification
        // This could be email, SMS, or in-app notification
    }
}

<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\ManualTransferOrder;
use App\Services\TenantManager;
use App\Http\Responses\FlashMessage;
use Illuminate\Http\Request;



class VendorManualTransferController extends Controller
{


    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Display a listing of manual transfer orders
     */
    public function index(Request $request)
    {
        $vendorId = $this->tenantManager->getVendorId();

        $query = ManualTransferOrder::where('vendor_id', $vendorId);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $statistics = [
            'total' => ManualTransferOrder::where('vendor_id', $vendorId)->count(),
            'pending' => ManualTransferOrder::where('vendor_id', $vendorId)->pending()->count(),
            'paid' => ManualTransferOrder::where('vendor_id', $vendorId)->where('status', 'paid')->count(),
            'completed' => ManualTransferOrder::where('vendor_id', $vendorId)->where('status', 'completed')->count(),
            'rejected' => ManualTransferOrder::where('vendor_id', $vendorId)->where('status', 'rejected')->count(),
        ];

        return view('vendor.manual-transfers.index', compact('orders', 'statistics'));
    }

    /**
     * Display the specified order
     */
    public function show(ManualTransferOrder $order)
    {
        $this->authorizeOrder($order);

        return view('vendor.manual-transfers.show', compact('order'));
    }

    /**
     * Confirm/complete an order
     */
    public function confirm(ManualTransferOrder $order)
    {
        $this->authorizeOrder($order);

        if (!$order->isPaid()) {
            return FlashMessage::backError('Hanya order yang sudah dibayar yang bisa dikonfirmasi.');
        }

        $order->complete();

        return FlashMessage::backSuccess("Order {$order->order_number} berhasil dikonfirmasi.");
    }

    /**
     * Reject an order
     */
    public function reject(Request $request, ManualTransferOrder $order)
    {
        $this->authorizeOrder($order);

        if ($order->status === ManualTransferOrder::STATUS_COMPLETED) {
            return FlashMessage::backError('Order yang sudah selesai tidak bisa ditolak.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $order->reject($request->rejection_reason);

        return FlashMessage::backSuccess("Order {$order->order_number} berhasil ditolak.");
    }

    /**
     * Authorize that the order belongs to the current vendor
     */
    private function authorizeOrder(ManualTransferOrder $order): void
    {
        $vendorId = $this->tenantManager->getVendorId();

        if ($order->vendor_id !== $vendorId) {
            abort(403, 'Anda tidak memiliki akses ke order ini.');
        }
    }
}

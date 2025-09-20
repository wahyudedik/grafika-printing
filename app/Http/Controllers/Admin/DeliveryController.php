<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryConfirmation;
use App\Models\ShippingInvoice;
use App\Models\Vendor\Transaksi;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * Display delivery confirmations
     */
    public function index(Request $request)
    {
        $query = DeliveryConfirmation::with(['transaction', 'vendor', 'shippingInvoice']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by vendor
        if ($request->has('vendor_id') && $request->vendor_id !== '') {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('confirmation_code', 'like', "%{$search}%")
                    ->orWhereHas('transaction', function ($transactionQuery) use ($search) {
                        $transactionQuery->where('kode_transaksi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $deliveryConfirmations = $query->orderBy('created_at', 'desc')->paginate(50);

        // Statistics
        $stats = [
            'total_confirmations' => DeliveryConfirmation::count(),
            'pending_confirmations' => DeliveryConfirmation::where('status', 'pending')->count(),
            'confirmed' => DeliveryConfirmation::where('status', 'confirmed')->count(),
            'rejected' => DeliveryConfirmation::where('status', 'rejected')->count(),
            'today_confirmations' => DeliveryConfirmation::whereDate('created_at', today())->count()
        ];

        // Get vendors for filter
        $vendors = \App\Models\Vendor::select('id', 'name')->get();

        return view('dev.delivery.index', compact('deliveryConfirmations', 'stats', 'vendors'));
    }

    /**
     * Show delivery confirmation details
     */
    public function show($id)
    {
        $deliveryConfirmation = DeliveryConfirmation::with(['transaction', 'vendor', 'shippingInvoice'])->findOrFail($id);

        return view('dev.delivery.show', compact('deliveryConfirmation'));
    }

    /**
     * Approve delivery confirmation
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $deliveryConfirmation = DeliveryConfirmation::findOrFail($id);

        $deliveryConfirmation->update([
            'status' => 'confirmed',
            'admin_notes' => $request->admin_notes,
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id()
        ]);

        // Update related transaction status
        if ($deliveryConfirmation->transaction) {
            $deliveryConfirmation->transaction->update([
                'status' => 'completed',
                'delivery_confirmed_at' => now()
            ]);
        }

        return redirect()->back()->with('toast_success', 'Delivery confirmation approved successfully');
    }

    /**
     * Reject delivery confirmation
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        $deliveryConfirmation = DeliveryConfirmation::findOrFail($id);

        $deliveryConfirmation->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'rejected_at' => now(),
            'rejected_by' => auth()->id()
        ]);

        return redirect()->back()->with('toast_success', 'Delivery confirmation rejected');
    }

    /**
     * Export delivery confirmations
     */
    public function export(Request $request)
    {
        $query = DeliveryConfirmation::with(['transaction', 'vendor']);

        // Apply same filters as index
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id') && $request->vendor_id !== '') {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $deliveryConfirmations = $query->orderBy('created_at', 'desc')->get();

        $filename = 'delivery_confirmations_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($deliveryConfirmations) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Confirmation Code',
                'Vendor',
                'Transaction Code',
                'Status',
                'Customer Name',
                'Customer Phone',
                'Delivery Address',
                'Admin Notes',
                'Created At',
                'Confirmed At'
            ]);

            foreach ($deliveryConfirmations as $confirmation) {
                fputcsv($file, [
                    $confirmation->id,
                    $confirmation->confirmation_code,
                    $confirmation->vendor->name ?? 'N/A',
                    $confirmation->transaction->kode_transaksi ?? 'N/A',
                    $confirmation->status,
                    $confirmation->customer_name,
                    $confirmation->customer_phone,
                    $confirmation->delivery_address,
                    $confirmation->admin_notes ?? 'N/A',
                    $confirmation->created_at->format('Y-m-d H:i:s'),
                    $confirmation->confirmed_at ? $confirmation->confirmed_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

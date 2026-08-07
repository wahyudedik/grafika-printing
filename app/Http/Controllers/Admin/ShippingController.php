<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingInvoice;
use App\Models\DeliveryConfirmation;
use App\Models\Vendor\Transaksi;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Display shipping tracking dashboard
     */
    public function index(Request $request)
    {
        $query = ShippingInvoice::with(['transaction', 'vendor']);

        // Filter by shipping_status
        if ($request->has('status') && $request->status !== '') {
            $query->where('shipping_status', $request->status);
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
                $q->where('kode', 'like', "%{$search}%")
                        ->orWhere('waybill_number', 'like', "%{$search}%")
                    ->orWhereHas('transaction', function ($transactionQuery) use ($search) {
                        $transactionQuery->where('kode_transaksi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $shippingInvoices = $query->orderBy('created_at', 'desc')->paginate(50);

        // Statistics
        $stats = [
            'total_shipments' => ShippingInvoice::count(),
            'pending_shipments' => ShippingInvoice::where('shipping_status', 'pending')->count(),
            'in_transit' => ShippingInvoice::where('shipping_status', 'shipped')->count(),
            'delivered' => ShippingInvoice::where('shipping_status', 'delivered')->count(),
            'failed' => ShippingInvoice::where('shipping_status', 'failed')->count(),
            'today_shipments' => ShippingInvoice::whereDate('created_at', today())->count()
        ];

        // Get vendors for filter
        $vendors = \App\Models\Vendor::select('id', 'name')->get();

        return view('dev.shipping.index', compact('shippingInvoices', 'stats', 'vendors'));
    }

    /**
     * Show shipping details
     */
    public function show($id)
    {
        $shippingInvoice = ShippingInvoice::with(['transaction', 'vendor', 'deliveryConfirmation'])->findOrFail($id);

        return view('dev.shipping.show', compact('shippingInvoice'));
    }

    /**
     * Track shipping status
     */
    public function track($id)
    {
        $shippingInvoice = ShippingInvoice::findOrFail($id);

        try {
            $rajaOngkirService = new RajaOngkirService();
            $trackingResult = $rajaOngkirService->trackShipment($shippingInvoice->resi, $shippingInvoice->courier);

            // Update status based on tracking result
            if ($trackingResult['success']) {
                $shippingInvoice->update([
                    'shipping_status' => $trackingResult['status'],
                    'tracking_data' => $trackingResult['data']
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $trackingResult
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track shipment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update shipping status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,failed',
            'notes' => 'nullable|string|max:500'
        ]);

        $shippingInvoice = ShippingInvoice::findOrFail($id);

        $shippingInvoice->update([
            'shipping_status' => $request->status,
            'notes' => $request->notes,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('toast_success', 'Shipping status updated successfully');
    }

    /**
     * Get shipping invoices
     */
    public function invoices(Request $request)
    {
        $query = ShippingInvoice::with(['transaction', 'vendor']);

        // Filter by shipping_status
        if ($request->has('status') && $request->status !== '') {
            $query->where('shipping_status', $request->status);
        }

        // Filter by vendor
        if ($request->has('vendor_id') && $request->vendor_id !== '') {
            $query->where('vendor_id', $request->vendor_id);
        }

        $shippingInvoices = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('dev.shipping.invoices', compact('shippingInvoices'));
    }

    /**
     * Export shipping data
     */
    public function export(Request $request)
    {
        $query = ShippingInvoice::with(['transaction', 'vendor']);

        // Apply same filters as index
        if ($request->has('status') && $request->status !== '') {
            $query->where('shipping_status', $request->status);
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

        $shippingInvoices = $query->orderBy('created_at', 'desc')->get();

        $filename = 'shipping_tracking_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($shippingInvoices) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Kode',
                'Vendor',
                'Transaction Code',
                'Resi',
                'Status',
                'Service',
                'Cost',
                'Created At',
                'Updated At'
            ]);

            foreach ($shippingInvoices as $invoice) {
                fputcsv($file, [
                    $invoice->id,
                    $invoice->kode,
                    $invoice->vendor->name ?? 'N/A',
                    $invoice->transaction->kode_transaksi ?? 'N/A',
                    $invoice->resi ?? 'N/A',
                    $invoice->shipping_status,
                    $invoice->service,
                    $invoice->shipping_cost,
                    $invoice->created_at->format('Y-m-d H:i:s'),
                    $invoice->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

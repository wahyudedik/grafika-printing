<?php

namespace App\Http\Controllers;

use App\Models\FinancialAuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class VendorAuditLogController extends Controller
{
    /**
     * Display audit logs for vendor
     */
    public function index(Request $request)
    {
        $vendorId = auth()->user()->vendor_id;

        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not found');
        }

        $query = FinancialAuditLog::forVendor($vendorId)
            ->with(['user', 'vendor'])
            ->orderBy('created_at', 'desc');

        // Filter by action type
        if ($request->has('action_type') && $request->action_type !== '') {
            $query->where('action_type', $request->action_type);
        }

        // Filter by entity type
        if ($request->has('entity_type') && $request->entity_type !== '') {
            $query->where('entity_type', $request->entity_type);
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
                $q->where('transaction_reference', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(30);

        $stats = [
            'total_logs' => FinancialAuditLog::forVendor($vendorId)->count(),
            'today_logs' => FinancialAuditLog::forVendor($vendorId)
                ->whereDate('created_at', today())->count(),
            'financial_actions' => FinancialAuditLog::forVendor($vendorId)
                ->financialActions()->count()
        ];

        return view('vendor.audit-logs.index', compact('logs', 'stats'));
    }

    /**
     * Show specific audit log for vendor
     */
    public function show($id)
    {
        $vendorId = auth()->user()->vendor_id;

        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not found');
        }

        $log = FinancialAuditLog::forVendor($vendorId)
            ->with(['user', 'vendor'])
            ->findOrFail($id);

        return view('vendor.audit-logs.show', compact('log'));
    }

    /**
     * Get financial audit logs for vendor
     */
    public function financial()
    {
        $vendorId = auth()->user()->vendor_id;

        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not found');
        }

        $logs = FinancialAuditLog::forVendor($vendorId)
            ->financialActions()
            ->with(['user', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('vendor.audit-logs.financial', compact('logs'));
    }

    /**
     * Export vendor audit logs
     */
    public function export(Request $request)
    {
        $vendorId = auth()->user()->vendor_id;

        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not found');
        }

        $query = FinancialAuditLog::forVendor($vendorId)->with(['user', 'vendor']);

        // Apply filters
        if ($request->has('action_type') && $request->action_type !== '') {
            $query->where('action_type', $request->action_type);
        }

        if ($request->has('entity_type') && $request->entity_type !== '') {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'vendor_audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Action',
                'Entity Type',
                'Entity ID',
                'Amount',
                'Status',
                'Risk Level',
                'Transaction Reference',
                'Notes',
                'Created At'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->action_type,
                    $log->entity_type,
                    $log->entity_id,
                    $log->amount ?? 'N/A',
                    $log->status,
                    $log->risk_level,
                    $log->transaction_reference ?? 'N/A',
                    $log->notes ?? 'N/A',
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

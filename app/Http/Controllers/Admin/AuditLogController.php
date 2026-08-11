<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display audit logs for admin
     */
    public function index(Request $request)
    {
        $query = FinancialAuditLog::with(['user', 'vendor'])
            ->orderBy('created_at', 'desc');

        // Filter by risk level
        if ($request->has('risk_level') && $request->risk_level !== '') {
            $query->where('risk_level', $request->risk_level);
        }

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
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $query->paginate(50);

        $stats = [
            'total_logs' => FinancialAuditLog::count(),
            'high_risk' => FinancialAuditLog::highRisk()->count(),
            'today_logs' => FinancialAuditLog::whereDate('created_at', today())->count(),
            'financial_actions' => FinancialAuditLog::financialActions()->count()
        ];

        return view('dev.audit-logs.index', compact('logs', 'stats'));
    }

    /**
     * Show specific audit log
     */
    public function show($id)
    {
        $log = FinancialAuditLog::with(['user', 'vendor'])->findOrFail($id);

        return view('dev.audit-logs.show', compact('log'));
    }

    /**
     * Get high-risk transactions
     */
    public function highRisk()
    {
        $logs = AuditLogService::getHighRiskTransactions(100);

        return view('dev.audit-logs.high-risk', compact('logs'));
    }

    /**
     * Get financial audit logs
     */
    public function financial()
    {
        $logs = FinancialAuditLog::financialActions() ->with(['user', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('dev.audit-logs.financial', compact('logs'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $query = FinancialAuditLog::with(['user', 'vendor']);

        // Apply same filters as index
        if ($request->has('risk_level') && $request->risk_level !== '') {
            $query->where('risk_level', $request->risk_level);
        }

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

        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'User',
                'Vendor',
                'Action',
                'Entity Type',
                'Entity ID',
                'Amount',
                'Status',
                'Risk Level',
                'IP Address',
                'User Agent',
                'Transaction Reference',
                'Notes',
                'Created At'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user->name ?? 'N/A',
                    $log->vendor->name ?? 'N/A',
                    $log->action_type,
                    $log->entity_type,
                    $log->entity_id,
                    $log->amount ?? 'N/A',
                    $log->status,
                    $log->risk_level,
                    $log->ip_address,
                    $log->user_agent,
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

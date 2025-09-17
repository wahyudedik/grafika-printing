<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorWithdrawal;
use App\Models\VendorWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalManagementController extends Controller
{
    /**
     * Display withdrawal requests for admin
     */
    public function index()
    {
        $withdrawals = VendorWithdrawal::with(['vendor', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => VendorWithdrawal::where('status', 'pending')->count(),
            'approved' => VendorWithdrawal::where('status', 'approved')->count(),
            'completed' => VendorWithdrawal::where('status', 'completed')->count(),
            'rejected' => VendorWithdrawal::where('status', 'rejected')->count(),
            'total_pending_amount' => VendorWithdrawal::where('status', 'pending')->sum('amount'),
            'total_approved_amount' => VendorWithdrawal::where('status', 'approved')->sum('amount')
        ];

        return view('dev.withdrawals.index', compact('withdrawals', 'stats'));
    }

    /**
     * Show withdrawal details
     */
    public function show(VendorWithdrawal $withdrawal)
    {
        $withdrawal->load(['vendor', 'processedBy', 'vendorWallet']);

        return view('dev.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Approve withdrawal request
     */
    public function approve(Request $request, VendorWithdrawal $withdrawal)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);

        try {
            $withdrawal->approve(Auth::id(), $request->admin_notes);

            return redirect()->route('admin.withdrawals.show', $withdrawal)
                ->with('success', 'Penarikan berhasil disetujui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject withdrawal request
     */
    public function reject(Request $request, VendorWithdrawal $withdrawal)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        try {
            $withdrawal->reject(Auth::id(), $request->admin_notes);

            return redirect()->route('admin.withdrawals.show', $withdrawal)
                ->with('success', 'Penarikan berhasil ditolak!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Complete withdrawal (after payment is made)
     */
    public function complete(Request $request, VendorWithdrawal $withdrawal)
    {
        $request->validate([
            'payment_proof' => 'nullable|array',
            'payment_proof.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        try {
            $paymentProof = null;
            if ($request->hasFile('payment_proof')) {
                $proofs = [];
                foreach ($request->file('payment_proof') as $file) {
                    $proofs[] = $file->store('withdrawal-proofs', 'public');
                }
                $paymentProof = $proofs;
            }

            $withdrawal->complete($paymentProof);

            return redirect()->route('admin.withdrawals.show', $withdrawal)
                ->with('success', 'Penarikan berhasil diselesaikan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get withdrawal statistics
     */
    public function statistics()
    {
        $stats = [
            'total_withdrawals' => VendorWithdrawal::count(),
            'total_amount' => VendorWithdrawal::sum('amount'),
            'total_fees' => VendorWithdrawal::sum('fee'),
            'total_net_amount' => VendorWithdrawal::sum('net_amount'),
            'pending_count' => VendorWithdrawal::where('status', 'pending')->count(),
            'approved_count' => VendorWithdrawal::where('status', 'approved')->count(),
            'completed_count' => VendorWithdrawal::where('status', 'completed')->count(),
            'rejected_count' => VendorWithdrawal::where('status', 'rejected')->count()
        ];

        // Monthly statistics
        $monthlyStats = VendorWithdrawal::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as count,
                SUM(amount) as total_amount,
                SUM(fee) as total_fee,
                SUM(net_amount) as total_net_amount
            ')
            ->where('status', 'completed')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Method statistics
        $methodStats = VendorWithdrawal::selectRaw('
                method,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->groupBy('method')
            ->get();

        return view('dev.withdrawals.statistics', compact('stats', 'monthlyStats', 'methodStats'));
    }
}

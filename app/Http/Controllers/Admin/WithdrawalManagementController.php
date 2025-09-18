<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorWithdrawal;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WithdrawalManagementController extends Controller
{
    /**
     * Display all withdrawal requests
     */
    public function index(Request $request)
    {
        $query = VendorWithdrawal::with(['vendor', 'processedBy']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by vendor
        if ($request->has('vendor_id') && $request->vendor_id !== '') {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdrawals = $query->orderBy('created_at', 'desc')->paginate(20);
        $vendors = Vendor::orderBy('name')->get();
        $statuses = ['pending', 'approved', 'rejected', 'processing', 'completed', 'failed'];

        return view('admin.withdrawal.index', compact('withdrawals', 'vendors', 'statuses'));
    }

    /**
     * Show withdrawal details
     */
    public function show(VendorWithdrawal $withdrawal)
    {
        $withdrawal->load(['vendor', 'processedBy', 'vendorWallet']);

        return view('admin.withdrawal.show', compact('withdrawal'));
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

            Log::info('Withdrawal approved by admin', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
                'amount' => $withdrawal->amount
            ]);

            return redirect()->back()
                ->with('toast_success', 'Penarikan berhasil disetujui');
        } catch (\Exception $e) {
            Log::error('Withdrawal approval failed', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('toast_error', 'Gagal menyetujui penarikan: ' . $e->getMessage());
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

            Log::info('Withdrawal rejected by admin', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
                'amount' => $withdrawal->amount,
                'reason' => $request->admin_notes
            ]);

            return redirect()->back()
                ->with('toast_success', 'Penarikan berhasil ditolak');
        } catch (\Exception $e) {
            Log::error('Withdrawal rejection failed', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('toast_error', 'Gagal menolak penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Complete withdrawal request
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
                $paymentProof = [];
                foreach ($request->file('payment_proof') as $file) {
                    $path = $file->store('withdrawal-proofs', 'public');
                    $paymentProof[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }
            }

            $withdrawal->complete($paymentProof);

            Log::info('Withdrawal completed by admin', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
                'amount' => $withdrawal->amount
            ]);

            return redirect()->back()
                ->with('toast_success', 'Penarikan berhasil diselesaikan');
        } catch (\Exception $e) {
            Log::error('Withdrawal completion failed', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('toast_error', 'Gagal menyelesaikan penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Get withdrawal statistics
     */
    public function statistics()
    {
        $stats = [
            'total_pending' => VendorWithdrawal::where('status', 'pending')->count(),
            'total_approved' => VendorWithdrawal::where('status', 'approved')->count(),
            'total_completed' => VendorWithdrawal::where('status', 'completed')->count(),
            'total_rejected' => VendorWithdrawal::where('status', 'rejected')->count(),
            'total_amount_pending' => VendorWithdrawal::where('status', 'pending')->sum('amount'),
            'total_amount_completed' => VendorWithdrawal::where('status', 'completed')->sum('amount'),
            'total_fees' => VendorWithdrawal::where('status', 'completed')->sum('fee')
        ];

        return response()->json($stats);
    }

    /**
     * Bulk approve withdrawals
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'withdrawal_ids' => 'required|array',
            'withdrawal_ids.*' => 'exists:vendor_withdrawals,id'
        ]);

        try {
            $withdrawals = VendorWithdrawal::whereIn('id', $request->withdrawal_ids)
                ->where('status', 'pending')
                ->get();

            $approvedCount = 0;
            foreach ($withdrawals as $withdrawal) {
                try {
                    $withdrawal->approve(Auth::id(), 'Bulk approval');
                    $approvedCount++;
                } catch (\Exception $e) {
                    Log::error('Bulk approval failed for withdrawal', [
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Bulk withdrawal approval', [
                'admin_id' => Auth::id(),
                'requested_count' => count($request->withdrawal_ids),
                'approved_count' => $approvedCount
            ]);

            return redirect()->back()
                ->with('toast_success', "Berhasil menyetujui {$approvedCount} penarikan");
        } catch (\Exception $e) {
            Log::error('Bulk approval failed', [
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('toast_error', 'Gagal melakukan bulk approval: ' . $e->getMessage());
        }
    }
}

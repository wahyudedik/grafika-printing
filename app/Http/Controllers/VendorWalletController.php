<?php

namespace App\Http\Controllers;

use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorWalletController extends Controller
{
    /**
     * Display vendor wallet dashboard
     */
    public function index()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            abort(403, 'Anda tidak memiliki akses vendor');
        }

        $wallet = $vendor->getOrCreateWallet();

        // Get recent transactions
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get pending withdrawals
        $pendingWithdrawals = $wallet->withdrawals()
            ->whereIn('status', ['pending', 'approved', 'processing'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics
        $stats = [
            'total_earned' => $wallet->total_earned,
            'total_withdrawn' => $wallet->total_withdrawn,
            'available_balance' => $wallet->available_balance,
            'pending_withdrawals' => $pendingWithdrawals->sum('amount')
        ];

        return view('vendor.wallet.index', compact('wallet', 'transactions', 'pendingWithdrawals', 'stats'));
    }

    /**
     * Display wallet transactions
     */
    public function transactions()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            abort(403, 'Anda tidak memiliki akses vendor');
        }

        $wallet = $vendor->getOrCreateWallet();

        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('vendor.wallet.transactions', compact('wallet', 'transactions'));
    }

    /**
     * Display withdrawal requests
     */
    public function withdrawals()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            abort(403, 'Anda tidak memiliki akses vendor');
        }

        $wallet = $vendor->getOrCreateWallet();

        $withdrawals = $wallet->withdrawals()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('vendor.wallet.withdrawals', compact('wallet', 'withdrawals'));
    }

    /**
     * Show withdrawal form
     */
    public function createWithdrawal()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            abort(403, 'Anda tidak memiliki akses vendor');
        }

        $wallet = $vendor->getOrCreateWallet();

        return view('vendor.wallet.create-withdrawal', compact('wallet'));
    }

    /**
     * Store withdrawal request
     */
    public function storeWithdrawal(Request $request)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            abort(403, 'Anda tidak memiliki akses vendor');
        }

        $request->validate([
            'amount' => 'required|numeric|min:10000', // Minimum 10,000
            'method' => 'required|in:bank_transfer,e_wallet,cash',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $withdrawal = VendorWithdrawal::createRequest(
                $vendor->id,
                $request->amount,
                $request->method,
                $request->account_number,
                $request->account_name,
                $request->bank_name,
                $request->notes
            );

            return redirect()->route('vendor.wallet.withdrawals')
                ->with('success', 'Permintaan penarikan dana berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show withdrawal details
     */
    public function showWithdrawal(VendorWithdrawal $withdrawal)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor || $withdrawal->vendor_id !== $vendor->id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat penarikan ini');
        }

        return view('vendor.wallet.show-withdrawal', compact('withdrawal'));
    }

    /**
     * Cancel withdrawal request
     */
    public function cancelWithdrawal(VendorWithdrawal $withdrawal)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor || $withdrawal->vendor_id !== $vendor->id) {
            abort(403, 'Anda tidak memiliki akses untuk membatalkan penarikan ini');
        }

        if ($withdrawal->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Penarikan tidak dapat dibatalkan');
        }

        $withdrawal->update(['status' => 'cancelled']);

        return redirect()->route('vendor.wallet.withdrawals')
            ->with('success', 'Penarikan berhasil dibatalkan');
    }
}

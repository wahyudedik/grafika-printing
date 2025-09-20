<?php

namespace App\Http\Controllers;

use App\Models\VendorWithdrawal;
use App\Models\VendorWallet;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VendorWithdrawalController extends Controller
{
    /**
     * Display withdrawal requests for vendor
     */
    public function index()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('vendor.dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan');
        }

        $withdrawals = VendorWithdrawal::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $wallet = $vendor->getOrCreateWallet();
        $minWithdrawal = config('app.min_withdrawal', 50000);

        return view('vendor.withdrawal.index', compact('withdrawals', 'wallet', 'minWithdrawal'));
    }

    /**
     * Show withdrawal form
     */
    public function create()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('vendor.dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan');
        }

        $wallet = $vendor->getOrCreateWallet();
        $minWithdrawal = config('app.min_withdrawal', 50000);

        return view('vendor.withdrawal.create', compact('wallet', 'minWithdrawal'));
    }

    /**
     * Store withdrawal request
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:bank_transfer,e_wallet,cash',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('vendor.dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan');
        }

        $wallet = $vendor->getOrCreateWallet();
        $minWithdrawal = config('app.min_withdrawal', 50000);

        // Check minimum withdrawal amount
        if ($request->amount < $minWithdrawal) {
            return redirect()->back()
                ->with('toast_error', "Minimum penarikan adalah Rp " . number_format($minWithdrawal, 0, ',', '.'));
        }

        // Check if vendor has sufficient balance
        if (!$wallet->hasSufficientBalance($request->amount)) {
            return redirect()->back()
                ->with('toast_error', 'Saldo tidak mencukupi');
        }

        try {
            $withdrawal = VendorWithdrawal::createRequest(
                $vendor->id,
                $request->input('amount'),
                $request->input('method'),
                $request->input('account_number'),
                $request->input('account_name'),
                $request->input('bank_name'),
                $request->input('notes')
            );

            Log::info('Withdrawal request created', [
                'vendor_id' => $vendor->id,
                'withdrawal_id' => $withdrawal->id,
                'amount' => $request->input('amount'),
                'method' => $request->input('method')
            ]);

            return redirect()->route('vendor.withdrawal.index')
                ->with('toast_success', 'Permintaan penarikan berhasil diajukan! Kode: ' . $withdrawal->withdrawal_code);
        } catch (\Exception $e) {
            Log::error('Withdrawal request failed', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('toast_error', 'Gagal mengajukan penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Show withdrawal details
     */
    public function show(VendorWithdrawal $withdrawal)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor || $withdrawal->vendor_id !== $vendor->id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat penarikan ini');
        }

        return view('vendor.withdrawal.show', compact('withdrawal'));
    }

    /**
     * Cancel withdrawal request
     */
    public function cancel(VendorWithdrawal $withdrawal)
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor || $withdrawal->vendor_id !== $vendor->id) {
            abort(403, 'Anda tidak memiliki akses untuk membatalkan penarikan ini');
        }

        if ($withdrawal->status !== 'pending') {
            return redirect()->back()
                ->with('toast_error', 'Penarikan tidak dapat dibatalkan');
        }

        try {
            $withdrawal->update(['status' => 'cancelled']);

            Log::info('Withdrawal request cancelled', [
                'vendor_id' => $vendor->id,
                'withdrawal_id' => $withdrawal->id
            ]);

            return redirect()->route('vendor.withdrawal.index')
                ->with('toast_success', 'Penarikan berhasil dibatalkan');
        } catch (\Exception $e) {
            Log::error('Withdrawal cancellation failed', [
                'vendor_id' => $vendor->id,
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('toast_error', 'Gagal membatalkan penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Get withdrawal fee calculation
     */
    public function calculateFee(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:bank_transfer,e_wallet,cash'
        ]);

        $fee = VendorWithdrawal::calculateFee($request->input('amount'), $request->input('method'));
        $netAmount = $request->input('amount') - $fee;

        return response()->json([
            'success' => true,
            'amount' => $request->input('amount'),
            'fee' => $fee,
            'net_amount' => $netAmount
        ]);
    }

    /**
     * Get withdrawal history
     */
    public function history()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('vendor.dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan');
        }

        $withdrawals = VendorWithdrawal::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('vendor.withdrawal.history', compact('withdrawals'));
    }
}

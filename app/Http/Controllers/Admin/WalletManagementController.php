<?php

namespace App\Http\Controllers\Admin;

use App\Http\Responses\FlashMessage;

use App\Http\Controllers\Controller;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\Vendor;
use Illuminate\Http\Request;

class WalletManagementController extends Controller
{
    /**
     * Display wallet management dashboard
     */
    public function index(Request $request)
    {
        $query = VendorWallet::with(['vendor'])
            ->withCount('transactions')
            ->with(['transactions' => function ($q) {
                $q->latest()->limit(1);
            }]);

        // Filter by vendor
        if ($request->has('vendor_id') && $request->vendor_id !== '') {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('vendor', function ($vendorQuery) use ($search) {
                $vendorQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $wallets = $query->orderBy('updated_at', 'desc')->paginate(50);

        // Statistics
        $stats = [
            'total_wallets' => VendorWallet::count(),
            'active_wallets' => VendorWallet::where('status', 'active')->count(),
            'frozen_wallets' => VendorWallet::where('status', 'frozen')->count(),
            'total_balance' => VendorWallet::sum('balance'),
            'total_available_balance' => VendorWallet::sum('available_balance'),
            'total_pending_balance' => VendorWallet::sum('pending_balance')
        ];

        // Get vendors for filter
        $vendors = Vendor::select('id', 'name')->get();

        return view('dev.wallets.index', compact('wallets', 'stats', 'vendors'));
    }

    /**
     * Show wallet details
     */
    public function show($id)
    {
        $wallet = VendorWallet::with(['vendor', 'transactions' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(20);
        }])->findOrFail($id);

        return view('dev.wallets.show', compact('wallet'));
    }

    /**
     * Show wallet transactions
     */
    public function transactions($id, Request $request)
    {
        $wallet = VendorWallet::with('vendor')->findOrFail($id);

        $query = $wallet->transactions();

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('dev.wallets.transactions', compact('wallet', 'transactions'));
    }

    /**
     * Freeze wallet
     */
    public function freeze($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $wallet = VendorWallet::findOrFail($id);

        $wallet->update([
            'status' => 'frozen',
            'frozen_reason' => $request->reason,
            'frozen_at' => now(),
            'frozen_by' => auth()->id()
        ]);

        return FlashMessage::backSuccess('Wallet frozen successfully');
    }

    /**
     * Unfreeze wallet
     */
    public function unfreeze($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $wallet = VendorWallet::findOrFail($id);

        $wallet->update([
            'status' => 'active',
            'unfrozen_reason' => $request->reason,
            'unfrozen_at' => now(),
            'unfrozen_by' => auth()->id()
        ]);

        return FlashMessage::backSuccess('Wallet unfrozen successfully');
    }

    /**
     * Get wallet statistics
     */
    public function statistics()
    {
        $stats = [
            'total_wallets' => VendorWallet::count(),
            'active_wallets' => VendorWallet::where('status', 'active')->count(),
            'frozen_wallets' => VendorWallet::where('status', 'frozen')->count(),
            'total_balance' => VendorWallet::sum('balance'),
            'total_available_balance' => VendorWallet::sum('available_balance'),
            'total_pending_balance' => VendorWallet::sum('pending_balance'),
            'average_balance' => VendorWallet::avg('balance'),
            'top_wallets' => VendorWallet::with('vendor')
                ->orderBy('balance', 'desc')
                ->limit(10)
                ->get()
        ];

        return view('dev.wallets.statistics', compact('stats'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\VendorWithdrawal;
use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorRevenueController extends Controller
{
    /**
     * Display vendor revenue dashboard
     */
    public function index()
    {
        // Get all vendors with their revenue data
        $vendors = Vendor::with(['wallet', 'withdrawals', 'auctionBids'])
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                    'phone' => $vendor->phone,
                    'total_earnings' => $vendor->wallet ? $vendor->wallet->total_earned : 0,
                    'current_balance' => $vendor->wallet ? $vendor->wallet->balance : 0,
                    'total_withdrawn' => $vendor->wallet ? $vendor->wallet->total_withdrawn : 0,
                    'pending_withdrawal' => $vendor->withdrawals()->where('status', 'pending')->sum('amount'),
                    'total_auctions_won' => $vendor->auctionBids()->where('status', 'accepted')->count(),
                    'total_auction_earnings' => $vendor->auctionBids()->where('status', 'accepted')->sum('bid_amount'),
                    'last_withdrawal' => $vendor->withdrawals()->latest()->first(),
                    'wallet_transactions_count' => $vendor->wallet ? $vendor->wallet->transactions()->count() : 0,
                ];
            });

        // Get summary statistics
        $summary = [
            'total_vendors' => Vendor::count(),
            'total_earnings' => VendorWallet::sum('total_earned'),
            'total_withdrawn' => VendorWallet::sum('total_withdrawn'),
            'total_pending_withdrawal' => VendorWithdrawal::where('status', 'pending')->sum('amount'),
            'total_auctions_won' => AuctionBid::where('status', 'accepted')->count(),
            'total_auction_earnings' => AuctionBid::where('status', 'accepted')->sum('bid_amount'),
        ];

        return view('dev.vendor-revenue.index', compact('vendors', 'summary'));
    }

    /**
     * Show detailed revenue data for a specific vendor
     */
    public function show(Vendor $vendor)
    {
        // Get vendor with detailed data
        $vendor->load(['wallet', 'withdrawals', 'auctionBids.auction']);

        // Get recent transactions
        $recentTransactions = $vendor->wallet
            ? $vendor->wallet->transactions()->latest()->limit(10)->get()
            : collect();

        // Get recent withdrawals
        $recentWithdrawals = $vendor->withdrawals()->latest()->limit(10)->get();

        // Get recent auction wins
        $recentAuctionWins = $vendor->auctionBids()
            ->where('status', 'accepted') ->with('auction')
            ->latest()
            ->limit(10)
            ->get();

        // Get monthly earnings data for chart
        $monthlyEarnings = $vendor->wallet
            ? $vendor->wallet->transactions()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->where('type', 'credit')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            : collect();

        return view('dev.vendor-revenue.show', compact(
            'vendor',
            'recentTransactions',
            'recentWithdrawals',
            'recentAuctionWins',
            'monthlyEarnings'
        ));
    }

    /**
     * Get vendor revenue statistics for API
     */
    public function statistics()
    {
        $stats = [
            'total_vendors' => Vendor::count(),
            'active_vendors' => Vendor::where('is_active', true)->count(),
            'total_earnings' => VendorWallet::sum('total_earned'),
            'total_withdrawn' => VendorWallet::sum('total_withdrawn'),
            'total_pending_withdrawal' => VendorWithdrawal::where('status', 'pending')->sum('amount'),
            'total_auctions_won' => AuctionBid::where('status', 'accepted')->count(),
            'total_auction_earnings' => AuctionBid::where('status', 'accepted')->sum('bid_amount'),
            'average_earnings_per_vendor' => VendorWallet::avg('total_earned'),
            'top_earning_vendor' => Vendor::with('wallet')
                ->get()
                ->sortByDesc('wallet.total_earned')
                ->first(),
        ];

        return response()->json($stats);
    }

    /**
     * Get monthly revenue data for charts
     */
    public function monthlyData()
    {
        $monthlyData = DB::table('vendor_wallet_transactions')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->where('type', 'credit')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($monthlyData);
    }

    /**
     * Get vendor revenue data for specific vendor
     */
    public function vendorData(Vendor $vendor)
    {
        $vendor->load(['wallet', 'withdrawals', 'auctionBids.auction']);

        $data = [
            'vendor' => $vendor,
            'total_earnings' => $vendor->wallet ? $vendor->wallet->total_earned : 0,
            'current_balance' => $vendor->wallet ? $vendor->wallet->balance : 0,
            'total_withdrawn' => $vendor->wallet ? $vendor->wallet->total_withdrawn : 0,
            'pending_withdrawal' => $vendor->withdrawals()->where('status', 'pending')->sum('amount'),
            'total_auctions_won' => $vendor->auctionBids()->where('status', 'accepted')->count(),
            'total_auction_earnings' => $vendor->auctionBids()->where('status', 'accepted')->sum('bid_amount'),
            'recent_transactions' => $vendor->wallet
                ? $vendor->wallet->transactions()->latest()->limit(5)->get()
                : collect(),
            'recent_withdrawals' => $vendor->withdrawals()->latest()->limit(5)->get(),
        ];

        return response()->json($data);
    }
}

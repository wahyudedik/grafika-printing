<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Produk;
use App\Services\XenditBalanceService;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserDashboardController extends Controller
{
    public function vendorDashboard()
    {
        try {
            // Check if user has vendor relationship
            $user = Auth::user();
            if (!$user->vendorUser || $user->vendorUser->isEmpty()) {
                return redirect('/login')->with('error', 'No vendor account associated with this user.');
            }

            $vendorId = $user->vendorUser->first()->vendor_id;

            // Get counts for dashboard widgets
            $userCount = User::whereHas('vendorUser', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })->count();
            $vendorCount = Vendor::where('is_active', 1)
                ->where('id', $vendorId)
                ->count();

            // Get product count if Produk model exists
            $productCount = 0;
            if (class_exists('App\Models\Vendor\Produk')) {
                $productCount = Produk::count();
            } else {
                $productCount = 256; // Placeholder
            }

            // Get bahan (materials) count
            $bahanCount = Bahan::count();

            // Get today's transactions
            $todayTransactions = Transaksi::whereDate('tanggal_dibuat', Carbon::today())->count();
            $yesterdayTransactions = Transaksi::whereDate('tanggal_dibuat', Carbon::yesterday())->count();

            // Monthly transactions
            $monthlyTransactions = Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->year)
                ->count();

            $lastMonthTransactions = Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->subMonth()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->subMonth()->year)
                ->count();

            // Monthly revenue
            $monthlyRevenue = Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->year)
                ->sum('total_harga') / 1000000; // Convert to millions

            $lastMonthRevenue = Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->subMonth()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->subMonth()->year)
                ->sum('total_harga') / 1000000;

            // Average order value
            $averageOrderValue = 0;
            $lastMonthAverageOrderValue = 0;

            if ($monthlyTransactions > 0) {
                $averageOrderValue = (Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->month)
                    ->whereYear('tanggal_dibuat', Carbon::now()->year)
                    ->sum('total_harga') / $monthlyTransactions) / 1000; // Convert to thousands
            }

            if ($lastMonthTransactions > 0) {
                $lastMonthAverageOrderValue = (Transaksi::whereMonth('tanggal_dibuat', Carbon::now()->subMonth()->month)
                    ->whereYear('tanggal_dibuat', Carbon::now()->subMonth()->year)
                    ->sum('total_harga') / $lastMonthTransactions) / 1000;
            }

            // Calculate growth percentages
            $todayGrowth = $yesterdayTransactions > 0
                ? round((($todayTransactions - $yesterdayTransactions) / $yesterdayTransactions) * 100)
                : 0;

            $monthlyGrowth = $lastMonthTransactions > 0
                ? round((($monthlyTransactions - $lastMonthTransactions) / $lastMonthTransactions) * 100)
                : 0;

            $monthlyRevenueGrowth = $lastMonthRevenue > 0
                ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
                : 0;

            $averageOrderValueGrowth = $lastMonthAverageOrderValue > 0
                ? round((($averageOrderValue - $lastMonthAverageOrderValue) / $lastMonthAverageOrderValue) * 100)
                : 0;

            // Get popular products data
            $popularProducts = ['labels' => [], 'data' => []];

            if (class_exists('App\Models\Vendor\Produk')) {
                // Get the most frequently ordered products
                $topProducts = TransaksiItem::select('produk_id')
                    ->selectRaw('COUNT(*) as order_count')
                    ->groupBy('produk_id')
                    ->orderBy('order_count', 'desc')
                    ->limit(6)
                    ->get();

                foreach ($topProducts as $item) {
                    $produk = Produk::find($item->produk_id);
                    if ($produk) {
                        $popularProducts['labels'][] = $produk->nama_produk ?? 'Product #' . $item->produk_id;
                        $popularProducts['data'][] = $item->order_count;
                    }
                }
            }

            // If no product data, use placeholder
            if (empty($popularProducts['labels'])) {
                $popularProducts = [
                    'labels' => ['Banner Printing', 'Business Cards', 'Flyers', 'Stickers', 'Posters', 'Brochures'],
                    'data' => [21, 17, 15, 12, 10, 8]
                ];
            }

            // Get monthly revenue data for the last 6 months
            $revenueData = ['labels' => [], 'data' => []];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $revenueData['labels'][] = $month->format('F');

                $revenue = Transaksi::whereMonth('tanggal_dibuat', $month->month)
                    ->whereYear('tanggal_dibuat', $month->year)
                    ->sum('total_harga') / 1000000; // Convert to millions

                $revenueData['data'][] = round($revenue, 1);
            }

            // Get low stock materials
            $lowStockMaterials = Bahan::where('stok', '<', 10)
                ->where('stok', '>', 0)
                ->orderBy('stok', 'asc')
                ->limit(5)
                ->get();

            // Get out of stock materials
            $outOfStockCount = Bahan::where('stok', '=', 0)->count();

            // Get pending orders count
            $pendingOrdersCount = Transaksi::where('status', 'pending')->count();

            // Get processing orders count
            $processingOrdersCount = Transaksi::where('status', 'processing')->count();

            // Get completed orders count for current month
            $completedOrdersCount = Transaksi::where('status', 'completed')
                ->whereMonth('tanggal_dibuat', Carbon::now()->month)
                ->whereYear('tanggal_dibuat', Carbon::now()->year)
                ->count();

            return view('dashboard', compact(
                'userCount',
                'vendorCount',
                'productCount',
                'bahanCount',
                'todayTransactions',
                'todayGrowth',
                'monthlyTransactions',
                'monthlyGrowth',
                'monthlyRevenue',
                'monthlyRevenueGrowth',
                'averageOrderValue',
                'averageOrderValueGrowth',
                'popularProducts',
                'revenueData',
                'lowStockMaterials',
                'outOfStockCount',
                'pendingOrdersCount',
                'processingOrdersCount',
                'completedOrdersCount'
            ))->with('toast_success', 'Welcome to your dashboard');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    public function devDashboard()
    {
        try {

            // Get comprehensive statistics
            $stats = $this->getDevStatistics();

            // Get recent activities
            $recentActivities = $this->getRecentActivities();

            // Get payment issues
            $paymentIssues = $this->getPaymentIssues();

            // Get monthly revenue chart data
            $revenueChartData = $this->getRevenueChartData();

            // Get auction status distribution
            $auctionStatusDistribution = $this->getAuctionStatusDistribution();

            // Get vendor performance
            $vendorPerformance = $this->getVendorPerformance();

            // Get Xendit balance
            $xenditBalance = (new XenditBalanceService())->getBalanceWithStatus();

            // Get high-risk audit logs
            $highRiskLogs = AuditLogService::getHighRiskTransactions(10);

            return view('dev.dashboard', compact(
                'stats',
                'recentActivities',
                'paymentIssues',
                'revenueChartData',
                'auctionStatusDistribution',
                'vendorPerformance',
                'xenditBalance',
                'highRiskLogs'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    public function userDashboard()
    {
        try {
            $user = Auth::user();

            // Auction statistics
            $myAuctionsCount = \App\Models\Auction::where('user_id', $user->id)->count();
            $activeAuctionsCount = \App\Models\Auction::where('user_id', $user->id)
                ->where('status', 'active')->count();
            $completedAuctionsCount = \App\Models\Auction::where('user_id', $user->id)
                ->where('status', 'completed')->count();

            // Recent auctions
            $recentAuctions = \App\Models\Auction::where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();

            // Order tracking
            $ordersCount = \App\Models\OrderTracking::where('user_id', $user->id)->count();
            $pendingOrdersCount = \App\Models\OrderTracking::where('user_id', $user->id)
                ->whereNotIn('status', ['completed', 'cancelled'])->count();

            // Recent orders
            $recentOrders = \App\Models\OrderTracking::where('user_id', $user->id)
                ->with('auction')
                ->latest()
                ->limit(5)
                ->get();

            // Total spending
            $totalSpent = \App\Models\XenditPayment::where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('amount') ?? 0;

            return view('user.dashboard', compact(
                'myAuctionsCount',
                'activeAuctionsCount',
                'completedAuctionsCount',
                'recentAuctions',
                'ordersCount',
                'pendingOrdersCount',
                'recentOrders',
                'totalSpent'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading user dashboard', ['error' => $e->getMessage()]);
            return view('user.dashboard');
        }
    }

    /**
     * Dedicated lelang user dashboard.
     * Shows profile status, auction stats, recent activity, and quick actions.
     */
    public function lelangDashboard()
    {
        try {
            $user = Auth::user();

            // Get or check lelang profile
            $profile = \App\Models\LelangUserProfile::where('user_id', $user->id)->first();

            // Auction statistics
            $auctionsQuery = \App\Models\Auction::where('user_id', $user->id);
            $myAuctionsCount = $auctionsQuery->count();
            $activeAuctionsCount = (clone $auctionsQuery)->where('status', 'active')->count();
            $completedAuctionsCount = (clone $auctionsQuery)->where('status', 'completed')->count();
            $pendingAuctionsCount = (clone $auctionsQuery)->where('status', 'pending')->count();
            $totalAuctionsValue = (clone $auctionsQuery)->where('status', 'completed')->sum('budget') ?? 0;

            // Recent auctions with bids
            $recentAuctions = \App\Models\Auction::where('user_id', $user->id)
                ->with('bids.vendor')
                ->latest()
                ->limit(5)
                ->get();

            // Order tracking
            $ordersCount = \App\Models\OrderTracking::where('user_id', $user->id)->count();
            $pendingOrdersCount = \App\Models\OrderTracking::where('user_id', $user->id)
                ->whereNotIn('status', ['completed', 'cancelled'])->count();

            // Total spending
            $totalSpent = \App\Models\XenditPayment::where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('amount') ?? 0;

            // Win rate
            $totalBidsOnMyAuctions = \App\Models\AuctionBid::whereHas('auction', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();

            return view('user.lelang-dashboard', compact(
                'profile',
                'myAuctionsCount',
                'activeAuctionsCount',
                'completedAuctionsCount',
                'pendingAuctionsCount',
                'totalAuctionsValue',
                'recentAuctions',
                'ordersCount',
                'pendingOrdersCount',
                'totalSpent',
                'totalBidsOnMyAuctions'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading lelang dashboard', ['error' => $e->getMessage()]);
            return redirect()->route('user.dashboard')->with('error', 'Gagal memuat dashboard lelang.');
        }
    }

    /**
     * Get comprehensive statistics for dev dashboard
     */
    private function getDevStatistics()
    {
        try {
            return [
                'total_users' => User::where('usertype', 'user')->count(),
                'total_vendors' => Vendor::count(),
                'total_auctions' => \App\Models\Auction::count(),
                'active_auctions' => \App\Models\Auction::where('status', 'active')->count(),
                'paid_auctions' => \App\Models\Auction::where('status', 'paid')->count(),
                'waiting_payment_auctions' => \App\Models\Auction::where('status', 'waiting_payment')->count(),
                'total_revenue' => \App\Models\VendorWallet::sum('total_earned') ?? 0,
                'pending_withdrawals' => \App\Models\VendorWithdrawal::where('status', 'pending')->sum('amount') ?? 0,
                'completed_withdrawals' => \App\Models\VendorWithdrawal::where('status', 'completed')->sum('amount') ?? 0,
                'total_bids' => \App\Models\AuctionBid::count(),
                'accepted_bids' => \App\Models\AuctionBid::where('status', 'accepted')->count(),
                'pending_bids' => \App\Models\AuctionBid::where('status', 'pending')->count(),
                'payment_issues' => \App\Models\Auction::where('status', 'waiting_payment')
                    ->where('created_at', '<', now()->subHours(24))->count(),
                'expired_payments' => \App\Models\XenditPayment::where('status', 'pending')
                    ->where('expires_at', '<', now())->count()
            ];
        } catch (\Exception $e) {
            Log::error('Error getting dev statistics', ['error' => $e->getMessage()]);
            return [
                'total_users' => 0,
                'total_vendors' => 0,
                'total_auctions' => 0,
                'active_auctions' => 0,
                'paid_auctions' => 0,
                'waiting_payment_auctions' => 0,
                'total_revenue' => 0,
                'pending_withdrawals' => 0,
                'completed_withdrawals' => 0,
                'total_bids' => 0,
                'accepted_bids' => 0,
                'pending_bids' => 0,
                'payment_issues' => 0,
                'expired_payments' => 0
            ];
        }
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        try {
            $activities = collect();

            // Recent auctions
            $recentAuctions = \App\Models\Auction::with('user')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($auction) {
                    return [
                        'type' => 'auction_created',
                        'message' => "Lelang baru: {$auction->title}",
                        'user' => $auction->user ? $auction->user->name : 'Unknown',
                        'time' => $auction->created_at,
                        'status' => $auction->status
                    ];
                });

            // Recent payments
            $recentPayments = \App\Models\XenditPayment::with('auction.user')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($payment) {
                    return [
                        'type' => 'payment_created',
                        'message' => "Pembayaran: Rp " . number_format((float) $payment->amount),
                        'user' => $payment->auction && $payment->auction->user ? $payment->auction->user->name : 'Unknown',
                        'time' => $payment->created_at,
                        'status' => $payment->status
                    ];
                });

            // Recent withdrawals
            $recentWithdrawals = \App\Models\VendorWithdrawal::with('vendor')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($withdrawal) {
                    return [
                        'type' => 'withdrawal_request',
                        'message' => "Penarikan: Rp " . number_format((float) $withdrawal->amount),
                        'user' => $withdrawal->vendor ? $withdrawal->vendor->name : 'Unknown',
                        'time' => $withdrawal->created_at,
                        'status' => $withdrawal->status
                    ];
                });

            return $activities
                ->merge($recentAuctions)
                ->merge($recentPayments)
                ->merge($recentWithdrawals)
                ->sortByDesc('time')
                ->take(10);
        } catch (\Exception $e) {
            Log::error('Error getting recent activities', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Get payment issues that need attention
     */
    private function getPaymentIssues()
    {
        try {
            return [
                'stuck_payments' => \App\Models\Auction::where('status', 'waiting_payment')
                    ->where('created_at', '<', now()->subHours(24))
                    ->with(['user', 'winnerVendor'])
                    ->get(),
                'expired_payments' => \App\Models\XenditPayment::where('status', 'pending')
                    ->where('expires_at', '<', now())
                    ->with('auction.user')
                    ->get(),
                'failed_payments' => \App\Models\XenditPayment::where('status', 'failed')
                    ->with('auction.user')
                    ->get()
            ];
        } catch (\Exception $e) {
            Log::error('Error getting payment issues', ['error' => $e->getMessage()]);
            return [
                'stuck_payments' => collect(),
                'expired_payments' => collect(),
                'failed_payments' => collect()
            ];
        }
    }

    /**
     * Get revenue chart data for the last 12 months
     */
    private function getRevenueChartData()
    {
        try {
            $months = [];
            $revenue = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[] = $date->format('M Y');

                $monthRevenue = \App\Models\VendorWalletTransaction::where('type', 'credit')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');

                $revenue[] = (float) $monthRevenue;
            }

            return [
                'months' => $months,
                'revenue' => $revenue
            ];
        } catch (\Exception $e) {
            Log::error('Error getting revenue chart data', ['error' => $e->getMessage()]);
            return [
                'months' => [],
                'revenue' => []
            ];
        }
    }

    /**
     * Get auction status distribution
     */
    private function getAuctionStatusDistribution()
    {
        try {
            return [
                'active' => \App\Models\Auction::where('status', 'active')->count(),
                'waiting_payment' => \App\Models\Auction::where('status', 'waiting_payment')->count(),
                'paid' => \App\Models\Auction::where('status', 'paid')->count(),
                'completed' => \App\Models\Auction::where('status', 'completed')->count(),
                'closed' => \App\Models\Auction::where('status', 'closed')->count(),
                'rejected' => \App\Models\Auction::where('status', 'rejected')->count()
            ];
        } catch (\Exception $e) {
            Log::error('Error getting auction status distribution', ['error' => $e->getMessage()]);
            return [
                'active' => 0,
                'waiting_payment' => 0,
                'paid' => 0,
                'completed' => 0,
                'closed' => 0,
                'rejected' => 0
            ];
        }
    }

    /**
     * Get vendor performance data
     */
    private function getVendorPerformance()
    {
        try {
            return Vendor::with(['wallet', 'auctionBids'])
                ->get()
                ->map(function ($vendor) {
                    return [
                        'id' => $vendor->id,
                        'name' => $vendor->name,
                        'total_earnings' => $vendor->wallet ? $vendor->wallet->total_earned : 0,
                        'current_balance' => $vendor->wallet ? $vendor->wallet->balance : 0,
                        'total_bids' => $vendor->auctionBids ? $vendor->auctionBids->count() : 0,
                        'accepted_bids' => $vendor->auctionBids ? $vendor->auctionBids->where('status', 'accepted')->count() : 0,
                        'success_rate' => $vendor->auctionBids && $vendor->auctionBids->count() > 0
                            ? round(($vendor->auctionBids->where('status', 'accepted')->count() / $vendor->auctionBids->count()) * 100, 2)
                            : 0
                    ];
                })
                ->sortByDesc('total_earnings')
                ->take(10);
        } catch (\Exception $e) {
            Log::error('Error getting vendor performance', ['error' => $e->getMessage()]);
            return collect();
        }
    }
}

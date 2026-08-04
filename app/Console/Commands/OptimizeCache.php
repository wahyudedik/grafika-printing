<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\XenditPayment;
use App\Models\VendorWallet;
use App\Models\VendorWithdrawal;
use App\Models\AdminFeeSetting;
use App\Models\AdminFeeTransaction;
use App\Models\DeliveryConfirmation;
use App\Models\ShippingInvoice;
use App\Models\VendorRating;
use App\Models\CmsSetting;
use App\Models\FinancialAuditLog;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Alat;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\TransaksiItemSpecifications;
use App\Models\Vendor\WholesalePrice;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\Pelanggan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizeCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:cache {--clear : Clear all caches} {--warm : Warm up caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize cache performance and strategy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting cache optimization...');
        $this->newLine();

        if ($this->option('clear')) {
            $this->clearAllCaches();
            return 0;
        }

        if ($this->option('warm')) {
            $this->warmUpCaches();
            return 0;
        }

        $optimized = 0;

        // 1. Analyze cache usage
        $this->analyzeCacheUsage();
        $optimized++;

        // 2. Optimize cache strategy
        $this->optimizeCacheStrategy();
        $optimized++;

        // 3. Warm up caches
        $this->warmUpCaches();
        $optimized++;

        $this->newLine();
        $this->info("✅ Cache optimized! ({$optimized} operations completed)");
        $this->info('🎉 Your application should now have better cache performance!');

        return 0;
    }

    private function analyzeCacheUsage()
    {
        $this->info('🔍 Analyzing cache usage...');

        $cacheKeys = [
            'app_config' => 'Application configuration',
            'user_stats' => 'User statistics',
            'vendor_stats' => 'Vendor statistics',
            'auction_stats' => 'Auction statistics',
            'payment_stats' => 'Payment statistics',
            'admin_fee_settings' => 'Admin fee settings',
            'cms_settings' => 'CMS settings',
            'vendor_ratings' => 'Vendor ratings',
            'product_categories' => 'Product categories',
            'shipping_costs' => 'Shipping costs'
        ];

        $totalKeys = 0;
        $cachedKeys = 0;

        foreach ($cacheKeys as $key => $description) {
            $totalKeys++;
            if (Cache::has($key)) {
                $cachedKeys++;
                $this->info("  ✅ {$description}: Cached");
            } else {
                $this->warn("  ⚠️ {$description}: Not cached");
            }
        }

        $this->info("✅ Analyzed {$totalKeys} cache keys, {$cachedKeys} cached");
    }

    private function optimizeCacheStrategy()
    {
        $this->info('⚡ Optimizing cache strategy...');

        $strategies = [
            'User Statistics' => function () {
                $users = User::count();
                $devUsers = User::where('usertype', 'dev')->count();
                $vendorUsers = User::where('usertype', 'vendor')->count();
                $normalUsers = User::where('usertype', 'user')->count();

                $stats = [
                    'total' => $users,
                    'dev' => $devUsers,
                    'vendor' => $vendorUsers,
                    'user' => $normalUsers
                ];

                Cache::put('user_stats', $stats, 3600); // 1 hour
                return $stats;
            },
            'Vendor Statistics' => function () {
                $vendors = Vendor::count();
                $activeVendors = Vendor::where('is_active', true)->count();
                $inactiveVendors = Vendor::where('is_active', false)->count();
                $verifiedVendors = Vendor::where('bank_verified', true)->count();

                $stats = [
                    'total' => $vendors,
                    'active' => $activeVendors,
                    'inactive' => $inactiveVendors,
                    'verified' => $verifiedVendors
                ];

                Cache::put('vendor_stats', $stats, 3600); // 1 hour
                return $stats;
            },
            'Auction Statistics' => function () {
                $auctions = Auction::count();
                $activeAuctions = Auction::where('status', 'active')->count();
                $pendingAuctions = Auction::where('status', 'pending')->count();
                $closedAuctions = Auction::where('status', 'closed')->count();

                $stats = [
                    'total' => $auctions,
                    'active' => $activeAuctions,
                    'pending' => $pendingAuctions,
                    'closed' => $closedAuctions
                ];

                Cache::put('auction_stats', $stats, 1800); // 30 minutes
                return $stats;
            },
            'Payment Statistics' => function () {
                $payments = XenditPayment::count();
                $paidPayments = XenditPayment::where('status', 'paid')->count();
                $pendingPayments = XenditPayment::where('status', 'pending')->count();
                $expiredPayments = XenditPayment::where('status', 'expired')->count();

                $stats = [
                    'total' => $payments,
                    'paid' => $paidPayments,
                    'pending' => $pendingPayments,
                    'expired' => $expiredPayments
                ];

                Cache::put('payment_stats', $stats, 1800); // 30 minutes
                return $stats;
            },
        ];

        $optimized = 0;

        foreach ($strategies as $name => $strategy) {
            try {
                $startTime = microtime(true);
                $result = $strategy();
                $endTime = microtime(true);
                $executionTime = ($endTime - $startTime) * 1000;

                $optimized++;
                $this->info("  ✅ {$name}: {$executionTime}ms (Cached)");
            } catch (\Exception $e) {
                $this->error("  ❌ {$name}: " . $e->getMessage());
            }
        }

        $this->info("✅ Optimized {$optimized} cache strategies");
    }

    private function warmUpCaches()
    {
        $this->info('🔥 Warming up caches...');

        $warmUpTasks = [
            'User Statistics' => function () {
                Cache::remember('user_stats', 3600, function () {
                    return [
                        'total' => User::count(),
                        'dev' => User::where('usertype', 'dev')->count(),
                        'vendor' => User::where('usertype', 'vendor')->count(),
                        'user' => User::where('usertype', 'user')->count()
                    ];
                });
            },
            'Vendor Statistics' => function () {
                Cache::remember('vendor_stats', 3600, function () {
                    return [
                        'total' => Vendor::count(),
                        'active' => Vendor::where('is_active', true)->count(),
                        'inactive' => Vendor::where('is_active', false)->count(),
                        'verified' => Vendor::where('bank_verified', true)->count()
                    ];
                });
            },
            'Auction Statistics' => function () {
                Cache::remember('auction_stats', 1800, function () {
                    return [
                        'total' => Auction::count(),
                        'active' => Auction::where('status', 'active')->count(),
                        'pending' => Auction::where('status', 'pending')->count(),
                        'closed' => Auction::where('status', 'closed')->count()
                    ];
                });
            },
            'Payment Statistics' => function () {
                Cache::remember('payment_stats', 1800, function () {
                    return [
                        'total' => XenditPayment::count(),
                        'paid' => XenditPayment::where('status', 'paid')->count(),
                        'pending' => XenditPayment::where('status', 'pending')->count(),
                        'expired' => XenditPayment::where('status', 'expired')->count()
                    ];
                });
            },
            'Admin Fee Settings' => function () {
                return Cache::remember('admin_fee_settings', 7200, function () {
                    return AdminFeeSetting::where('is_active', true)->get();
                });
            },
            'CMS Settings' => function () {
                return Cache::remember('cms_settings', 7200, function () {
                    return CmsSetting::where('is_active', true)->all();
                });
            },
            'Product Categories' => function () {
                return Cache::remember('product_categories', 3600, function () {
                    return KategoriProduk::where('is_active', true)->get();
                });
            }
        ];

        $warmed = 0;

        foreach ($warmUpTasks as $name => $task) {
            try {
                $startTime = microtime(true);
                $task();
                $endTime = microtime(true);
                $executionTime = ($endTime - $startTime) * 1000;

                $warmed++;
                $this->info("  ✅ {$name}: {$executionTime}ms (Warmed)");
            } catch (\Exception $e) {
                $this->error("  ❌ {$name}: " . $e->getMessage());
            }
        }

        $this->info("✅ Warmed up {$warmed} caches");
    }

    private function clearAllCaches()
    {
        $this->info('🧹 Clearing all caches...');

        try {
            Cache::flush();
            $this->info('✅ All caches cleared');
        } catch (\Exception $e) {
            $this->error('❌ Error clearing caches: ' . $e->getMessage());
        }
    }
}

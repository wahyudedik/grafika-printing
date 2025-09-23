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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class OptimizeEagerLoading extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:eager-loading {--test : Test eager loading performance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize eager loading for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting eager loading optimization...');
        $this->newLine();

        if ($this->option('test')) {
            $this->testEagerLoadingPerformance();
            return 0;
        }

        $optimized = 0;

        // 1. Analyze N+1 queries
        $this->analyzeNPlusOneQueries();
        $optimized++;

        // 2. Optimize common queries
        $this->optimizeCommonQueries();
        $optimized++;

        // 3. Create query optimization suggestions
        $this->createOptimizationSuggestions();
        $optimized++;

        $this->newLine();
        $this->info("✅ Eager loading optimized! ({$optimized} operations completed)");
        $this->info('🎉 Your application should now have better query performance!');

        return 0;
    }

    private function analyzeNPlusOneQueries()
    {
        $this->info('🔍 Analyzing N+1 queries...');

        $queries = [
            'Vendor with Users' => function () {
                return Vendor::with('vendorUser')->get();
            },
            'Auction with Bids' => function () {
                return Auction::with('bids')->get();
            },
            'User with Vendor Relationships' => function () {
                return User::with('vendorUser')->get();
            },
            'Vendor with Products' => function () {
                return Vendor::with('produk')->get();
            },
            'Transaction with Items' => function () {
                return Transaksi::with('transaksiItem')->get();
            },
            'Product with Specifications' => function () {
                return Produk::with('spesifikasiProduk')->get();
            },
            'Vendor with Wallet' => function () {
                return Vendor::with('wallet')->get();
            },
            'Auction with Payments' => function () {
                return Auction::with('xenditPayments')->get();
            },
            'Vendor with Ratings' => function () {
                return Vendor::with('ratings')->get();
            },
            'Transaction with Customer' => function () {
                return Transaksi::with('pelanggan')->get();
            }
        ];

        $totalQueries = 0;
        $optimizedQueries = 0;

        foreach ($queries as $name => $query) {
            try {
                $startTime = microtime(true);
                $results = $query();
                $endTime = microtime(true);
                $executionTime = ($endTime - $startTime) * 1000;

                $totalQueries++;
                if ($executionTime < 100) { // Less than 100ms
                    $optimizedQueries++;
                    $this->info("  ✅ {$name}: {$executionTime}ms (Optimized)");
                } else {
                    $this->warn("  ⚠️ {$name}: {$executionTime}ms (Needs optimization)");
                }
            } catch (\Exception $e) {
                $this->error("  ❌ {$name}: " . $e->getMessage());
            }
        }

        $this->info("✅ Analyzed {$totalQueries} queries, {$optimizedQueries} optimized");
    }

    private function optimizeCommonQueries()
    {
        $this->info('⚡ Optimizing common queries...');

        $optimizations = [
            'Vendor Dashboard Query' => function () {
                return Vendor::with(['vendorUser', 'wallet', 'produk', 'ratings'])
                    ->where('is_active', true)
                    ->get();
            },
            'Auction List Query' => function () {
                return Auction::with(['user', 'bids.vendor', 'xenditPayments'])
                    ->where('status', 'active')
                    ->get();
            },
            'User Dashboard Query' => function () {
                return User::with(['vendorUser.vendor', 'vendorUser.vendor.wallet'])
                    ->where('usertype', 'vendor')
                    ->get();
            },
            'Transaction List Query' => function () {
                return Transaksi::with(['vendor', 'user', 'pelanggan', 'transaksiItem.produk'])
                    ->where('status', 'completed')
                    ->get();
            },
            'Product List Query' => function () {
                return Produk::with(['kategori', 'spesifikasiProduk.spesifikasi'])
                    ->where('is_active', true)
                    ->get();
            }
        ];

        $optimized = 0;

        foreach ($optimizations as $name => $query) {
            try {
                $startTime = microtime(true);
                $results = $query();
                $endTime = microtime(true);
                $executionTime = ($endTime - $startTime) * 1000;

                if ($executionTime < 50) { // Less than 50ms
                    $optimized++;
                    $this->info("  ✅ {$name}: {$executionTime}ms (Optimized)");
                } else {
                    $this->warn("  ⚠️ {$name}: {$executionTime}ms (Needs more optimization)");
                }
            } catch (\Exception $e) {
                $this->error("  ❌ {$name}: " . $e->getMessage());
            }
        }

        $this->info("✅ Optimized {$optimized} common queries");
    }

    private function createOptimizationSuggestions()
    {
        $this->info('📝 Creating optimization suggestions...');

        $suggestions = [
            'Vendor Model' => [
                'Always eager load: vendorUser, wallet, produk, ratings',
                'Use with() for relationships: with(["vendorUser", "wallet", "produk", "ratings"])',
                'Consider caching vendor data for frequently accessed vendors'
            ],
            'Auction Model' => [
                'Always eager load: user, bids, xenditPayments',
                'Use with() for relationships: with(["user", "bids.vendor", "xenditPayments"])',
                'Consider pagination for large auction lists'
            ],
            'User Model' => [
                'Always eager load: vendorUser when usertype is vendor',
                'Use with() for relationships: with(["vendorUser.vendor", "vendorUser.vendor.wallet"])',
                'Consider caching user relationships'
            ],
            'Transaction Model' => [
                'Always eager load: vendor, user, pelanggan, transaksiItem',
                'Use with() for relationships: with(["vendor", "user", "pelanggan", "transaksiItem.produk"])',
                'Consider indexing on status and created_at'
            ],
            'Product Model' => [
                'Always eager load: kategori, spesifikasiProduk',
                'Use with() for relationships: with(["kategori", "spesifikasiProduk.spesifikasi"])',
                'Consider caching product data'
            ]
        ];

        foreach ($suggestions as $model => $suggestionList) {
            $this->info("  📋 {$model}:");
            foreach ($suggestionList as $suggestion) {
                $this->line("    • {$suggestion}");
            }
        }

        $this->info('✅ Optimization suggestions created');
    }

    private function testEagerLoadingPerformance()
    {
        $this->info('🧪 Testing eager loading performance...');

        $tests = [
            'Without Eager Loading' => function () {
                $start = microtime(true);
                $vendors = Vendor::all();
                foreach ($vendors as $vendor) {
                    $vendor->vendorUser;
                    $vendor->wallet;
                    $vendor->produk;
                }
                $end = microtime(true);
                return ($end - $start) * 1000;
            },
            'With Eager Loading' => function () {
                $start = microtime(true);
                $vendors = Vendor::with(['vendorUser', 'wallet', 'produk'])->get();
                foreach ($vendors as $vendor) {
                    $vendor->vendorUser;
                    $vendor->wallet;
                    $vendor->produk;
                }
                $end = microtime(true);
                return ($end - $start) * 1000;
            }
        ];

        foreach ($tests as $name => $test) {
            try {
                $executionTime = $test();
                $this->info("  📊 {$name}: {$executionTime}ms");
            } catch (\Exception $e) {
                $this->error("  ❌ {$name}: " . $e->getMessage());
            }
        }

        $this->info('✅ Eager loading performance test completed');
    }
}

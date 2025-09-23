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
use App\Models\Vendor\TenantModel;
use App\Services\TenantManager;
use App\Services\XenditService;
use App\Services\AdminFeeService;
use App\Services\AuditLogService;
use App\Services\EncryptionService;
use App\Services\RajaOngkirService;
use App\Services\ShippingTrackingService;
use App\Services\XenditBalanceService;
use App\Services\AuctionToPosService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class MonitorApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:application {--interval=30 : Monitoring interval in seconds} {--duration=300 : Monitoring duration in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor application health in real-time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $interval = (int) $this->option('interval');
        $duration = (int) $this->option('duration');

        $this->info("🔍 Starting application monitoring...");
        $this->info("⏱️ Interval: {$interval} seconds");
        $this->info("⏰ Duration: {$duration} seconds");
        $this->newLine();

        $startTime = time();
        $iteration = 0;

        while ((time() - $startTime) < $duration) {
            $iteration++;
            $this->info("📊 Monitoring iteration #{$iteration} - " . now()->format('H:i:s'));

            $this->checkApplicationHealth();

            if ((time() - $startTime) < $duration) {
                $this->info("⏳ Waiting {$interval} seconds...");
                sleep($interval);
            }
        }

        $this->info("✅ Monitoring completed!");
        return 0;
    }

    private function checkApplicationHealth()
    {
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'cache' => $this->checkCacheHealth(),
            'routes' => $this->checkRouteHealth(),
            'services' => $this->checkServiceHealth(),
            'data_integrity' => $this->checkDataIntegrityHealth(),
            'performance' => $this->checkPerformanceHealth()
        ];

        $this->displayHealthStatus($health);
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection OK'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    private function checkCacheHealth()
    {
        try {
            Cache::put('health_check', 'ok', 60);
            $value = Cache::get('health_check');
            Cache::forget('health_check');

            if ($value === 'ok') {
                return ['status' => 'healthy', 'message' => 'Cache system OK'];
            } else {
                return ['status' => 'unhealthy', 'message' => 'Cache system not working'];
            }
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Cache system error: ' . $e->getMessage()];
        }
    }

    private function checkRouteHealth()
    {
        try {
            $requiredRoutes = [
                'vendor.dashboard',
                'user.dashboard',
                'admin.dashboard',
                'vendor.profile',
                'vendor.public.profile',
                'auctions.close'
            ];

            $missingRoutes = [];
            foreach ($requiredRoutes as $routeName) {
                if (!Route::has($routeName)) {
                    $missingRoutes[] = $routeName;
                }
            }

            if (empty($missingRoutes)) {
                return ['status' => 'healthy', 'message' => 'All required routes available'];
            } else {
                return ['status' => 'unhealthy', 'message' => 'Missing routes: ' . implode(', ', $missingRoutes)];
            }
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Route system error: ' . $e->getMessage()];
        }
    }

    private function checkServiceHealth()
    {
        try {
            $services = [
                'TenantManager' => app(TenantManager::class),
                'XenditService' => app(XenditService::class),
                'AdminFeeService' => app(AdminFeeService::class),
                'AuditLogService' => app(AuditLogService::class),
                'EncryptionService' => app(EncryptionService::class),
                'RajaOngkirService' => app(RajaOngkirService::class),
                'ShippingTrackingService' => app(ShippingTrackingService::class),
                'XenditBalanceService' => app(XenditBalanceService::class),
                'AuctionToPosService' => app(AuctionToPosService::class)
            ];

            $failedServices = [];
            foreach ($services as $name => $service) {
                if (!$service) {
                    $failedServices[] = $name;
                }
            }

            if (empty($failedServices)) {
                return ['status' => 'healthy', 'message' => 'All services available'];
            } else {
                return ['status' => 'unhealthy', 'message' => 'Failed services: ' . implode(', ', $failedServices)];
            }
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Service system error: ' . $e->getMessage()];
        }
    }

    private function checkDataIntegrityHealth()
    {
        try {
            $issues = [];

            // Check for orphaned records
            $orphanedBids = AuctionBid::whereDoesntHave('auction')->count();
            if ($orphanedBids > 0) {
                $issues[] = "{$orphanedBids} orphaned auction bids";
            }

            $orphanedPayments = XenditPayment::whereDoesntHave('auction')->count();
            if ($orphanedPayments > 0) {
                $issues[] = "{$orphanedPayments} orphaned payments";
            }

            $orphanedTransactions = Transaksi::whereDoesntHave('vendor')->count();
            if ($orphanedTransactions > 0) {
                $issues[] = "{$orphanedTransactions} orphaned transactions";
            }

            // Check vendor-user relationships
            $vendorUsers = User::where('usertype', 'vendor')->get();
            $usersWithoutVendor = 0;
            foreach ($vendorUsers as $user) {
                if ($user->vendorUser->isEmpty()) {
                    $usersWithoutVendor++;
                }
            }

            if ($usersWithoutVendor > 0) {
                $issues[] = "{$usersWithoutVendor} vendor users without vendor relationship";
            }

            if (empty($issues)) {
                return ['status' => 'healthy', 'message' => 'Data integrity OK'];
            } else {
                return ['status' => 'unhealthy', 'message' => 'Data issues: ' . implode(', ', $issues)];
            }
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Data integrity error: ' . $e->getMessage()];
        }
    }

    private function checkPerformanceHealth()
    {
        try {
            $startTime = microtime(true);

            // Test database query performance
            $userCount = User::count();
            $vendorCount = Vendor::count();
            $auctionCount = Auction::count();

            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

            if ($executionTime < 1000) { // Less than 1 second
                return ['status' => 'healthy', 'message' => "Performance OK ({$executionTime}ms)"];
            } else {
                return ['status' => 'unhealthy', 'message' => "Performance slow ({$executionTime}ms)"];
            }
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Performance error: ' . $e->getMessage()];
        }
    }

    private function displayHealthStatus($health)
    {
        $healthyCount = 0;
        $unhealthyCount = 0;

        foreach ($health as $component => $status) {
            $icon = $status['status'] === 'healthy' ? '✅' : '❌';
            $this->line("{$icon} {$component}: {$status['message']}");

            if ($status['status'] === 'healthy') {
                $healthyCount++;
            } else {
                $unhealthyCount++;
            }
        }

        $this->newLine();
        $this->info("📊 Health Summary: {$healthyCount} healthy, {$unhealthyCount} unhealthy");
        $this->newLine();
    }
}

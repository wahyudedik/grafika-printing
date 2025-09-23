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

class DiagnoseApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:application {--fix : Fix issues automatically} {--detailed : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprehensive application diagnosis and repair';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting comprehensive application diagnosis...');
        $this->newLine();

        $issues = [];
        $fixes = [];

        // 1. Check Database Connections
        $this->checkDatabaseConnections($issues, $fixes);

        // 2. Check Model Relationships
        $this->checkModelRelationships($issues, $fixes);

        // 3. Check Tenant Context
        $this->checkTenantContext($issues, $fixes);

        // 4. Check Cache System
        $this->checkCacheSystem($issues, $fixes);

        // 5. Check Route System
        $this->checkRouteSystem($issues, $fixes);

        // 6. Check Service Dependencies
        $this->checkServiceDependencies($issues, $fixes);

        // 7. Check Data Integrity
        $this->checkDataIntegrity($issues, $fixes);

        // 8. Check Configuration
        $this->checkConfiguration($issues, $fixes);

        // 9. Check File Permissions
        $this->checkFilePermissions($issues, $fixes);

        // 10. Check External Services
        $this->checkExternalServices($issues, $fixes);

        // Display Results
        $this->displayResults($issues, $fixes);

        // Apply Fixes if requested
        if ($this->option('fix')) {
            $this->applyFixes($fixes);
        }

        return 0;
    }

    private function checkDatabaseConnections(&$issues, &$fixes)
    {
        $this->info('📊 Checking database connections...');

        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection: OK');
        } catch (\Exception $e) {
            $issues[] = "Database connection failed: " . $e->getMessage();
            $fixes[] = "Check database configuration in .env file";
        }

        // Check if all required tables exist
        $requiredTables = [
            'users',
            'vendors',
            'auctions',
            'auction_bids',
            'xendit_payments',
            'vendor_wallets',
            'vendor_withdrawals',
            'admin_fee_settings',
            'admin_fee_transactions',
            'delivery_confirmations',
            'shipping_invoices',
            'vendor_ratings',
            'cms_settings',
            'financial_audit_logs',
            'vendor_user',
            'transaksis',
            'produks',
            'bahans',
            'alats',
            'kategori_produks',
            'spesifikasis',
            'spesifikasi_produks',
            'transaksi_items',
            'transaksi_item_specifications',
            'wholesale_prices',
            'estimasi_produks',
            'pelanggans'
        ];

        foreach ($requiredTables as $table) {
            try {
                if (!DB::getSchemaBuilder()->hasTable($table)) {
                    $issues[] = "Missing table: {$table}";
                    $fixes[] = "Run migrations: php artisan migrate";
                }
            } catch (\Exception $e) {
                $issues[] = "Error checking table {$table}: " . $e->getMessage();
            }
        }
    }

    private function checkModelRelationships(&$issues, &$fixes)
    {
        $this->info('🔗 Checking model relationships...');

        // Check User-Vendor relationships
        $vendorUsers = User::where('usertype', 'vendor')->get();
        foreach ($vendorUsers as $user) {
            if ($user->vendorUser->isEmpty()) {
                $issues[] = "User {$user->email} has no vendor relationship";
                $fixes[] = "Run: php artisan fix:vendor-user-relationships";
            }
        }

        // Check Vendor-Wallet relationships
        $vendors = Vendor::all();
        foreach ($vendors as $vendor) {
            if (!$vendor->wallet) {
                $issues[] = "Vendor {$vendor->name} has no wallet";
                $fixes[] = "Create wallet for vendor: {$vendor->id}";
            }
        }
    }

    private function checkTenantContext(&$issues, &$fixes)
    {
        $this->info('🏢 Checking tenant context...');

        try {
            $tenantManager = app(TenantManager::class);
            if (!$tenantManager->hasVendorContext()) {
                $issues[] = "No tenant context set";
                $fixes[] = "Ensure SetTenantContext middleware is active";
            }
        } catch (\Exception $e) {
            $issues[] = "Tenant context error: " . $e->getMessage();
            $fixes[] = "Check TenantManager service configuration";
        }
    }

    private function checkCacheSystem(&$issues, &$fixes)
    {
        $this->info('💾 Checking cache system...');

        try {
            Cache::put('test_key', 'test_value', 60);
            if (Cache::get('test_key') !== 'test_value') {
                $issues[] = "Cache system not working properly";
                $fixes[] = "Check cache configuration and clear cache";
            }
            Cache::forget('test_key');
        } catch (\Exception $e) {
            $issues[] = "Cache system error: " . $e->getMessage();
            $fixes[] = "Check cache configuration in config/cache.php";
        }
    }

    private function checkRouteSystem(&$issues, &$fixes)
    {
        $this->info('🛣️ Checking route system...');

        try {
            $routes = Route::getRoutes();
            $requiredRoutes = [
                'vendor.dashboard',
                'user.dashboard',
                'admin.dashboard',
                'vendor.profile',
                'vendor.public.profile',
                'auctions.close'
            ];

            foreach ($requiredRoutes as $routeName) {
                if (!Route::has($routeName)) {
                    $issues[] = "Missing route: {$routeName}";
                    $fixes[] = "Check routes/web.php for missing route definitions";
                }
            }
        } catch (\Exception $e) {
            $issues[] = "Route system error: " . $e->getMessage();
            $fixes[] = "Check route configuration and clear route cache";
        }
    }

    private function checkServiceDependencies(&$issues, &$fixes)
    {
        $this->info('⚙️ Checking service dependencies...');

        $services = [
            XenditService::class,
            AdminFeeService::class,
            AuditLogService::class,
            EncryptionService::class,
            RajaOngkirService::class,
            ShippingTrackingService::class,
            XenditBalanceService::class,
            AuctionToPosService::class
        ];

        foreach ($services as $service) {
            try {
                app($service);
            } catch (\Exception $e) {
                $issues[] = "Service dependency error: {$service} - " . $e->getMessage();
                $fixes[] = "Check service provider configuration";
            }
        }
    }

    private function checkDataIntegrity(&$issues, &$fixes)
    {
        $this->info('🔍 Checking data integrity...');

        // Check for orphaned records
        $orphanedBids = AuctionBid::whereDoesntHave('auction')->count();
        if ($orphanedBids > 0) {
            $issues[] = "Found {$orphanedBids} orphaned auction bids";
            $fixes[] = "Clean up orphaned auction bids";
        }

        $orphanedPayments = XenditPayment::whereDoesntHave('auction')->count();
        if ($orphanedPayments > 0) {
            $issues[] = "Found {$orphanedPayments} orphaned payments";
            $fixes[] = "Clean up orphaned payments";
        }
    }

    private function checkConfiguration(&$issues, &$fixes)
    {
        $this->info('⚙️ Checking configuration...');

        $requiredConfigs = [
            'app.name',
            'app.env',
            'app.debug',
            'database.connections.mysql',
            'cache.default',
            'session.driver',
            'mail.default',
            'queue.default'
        ];

        foreach ($requiredConfigs as $config) {
            try {
                Config::get($config);
            } catch (\Exception $e) {
                $issues[] = "Configuration error: {$config} - " . $e->getMessage();
                $fixes[] = "Check configuration in config files";
            }
        }
    }

    private function checkFilePermissions(&$issues, &$fixes)
    {
        $this->info('📁 Checking file permissions...');

        $directories = [
            storage_path('app'),
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            public_path('storage')
        ];

        foreach ($directories as $dir) {
            if (!is_writable($dir)) {
                $issues[] = "Directory not writable: {$dir}";
                $fixes[] = "Set proper permissions: chmod -R 775 {$dir}";
            }
        }
    }

    private function checkExternalServices(&$issues, &$fixes)
    {
        $this->info('🌐 Checking external services...');

        // Check Xendit configuration
        if (!config('services.xendit.secret_key')) {
            $issues[] = "Xendit secret key not configured";
            $fixes[] = "Set XENDIT_SECRET_KEY in .env file";
        }

        // Check RajaOngkir configuration
        if (!config('services.rajaongkir.api_key')) {
            $issues[] = "RajaOngkir API key not configured";
            $fixes[] = "Set RAJAONGKIR_API_KEY in .env file";
        }
    }

    private function displayResults($issues, $fixes)
    {
        $this->newLine();
        $this->info('📋 DIAGNOSIS RESULTS');
        $this->newLine();

        if (empty($issues)) {
            $this->info('✅ No issues found! Application is healthy.');
        } else {
            $this->error('❌ Found ' . count($issues) . ' issues:');
            foreach ($issues as $index => $issue) {
                $this->line(($index + 1) . '. ' . $issue);
            }
        }

        if (!empty($fixes)) {
            $this->newLine();
            $this->info('🔧 Suggested fixes:');
            foreach ($fixes as $index => $fix) {
                $this->line(($index + 1) . '. ' . $fix);
            }
        }
    }

    private function applyFixes($fixes)
    {
        $this->newLine();
        $this->info('🔧 Applying fixes...');

        // Clear all caches
        $this->info('Clearing caches...');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        // Fix vendor-user relationships
        $this->info('Fixing vendor-user relationships...');
        Artisan::call('fix:vendor-user-relationships', ['--force' => true]);

        // Run migrations if needed
        $this->info('Running migrations...');
        Artisan::call('migrate', ['--force' => true]);

        $this->info('✅ Fixes applied successfully!');
    }
}

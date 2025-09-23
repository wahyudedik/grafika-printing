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
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class OptimizeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:database {--force : Force optimization without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize database performance and add UUIDs to existing records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting database optimization...');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('This will optimize your database and add UUIDs to existing records. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $optimized = 0;

        // 1. Run migrations
        $this->runMigrations();
        $optimized++;

        // 2. Add UUIDs to existing records
        $this->addUuidsToExistingRecords();
        $optimized++;

        // 3. Optimize database tables
        $this->optimizeDatabaseTables();
        $optimized++;

        // 4. Clear caches
        $this->clearCaches();
        $optimized++;

        // 5. Analyze tables
        $this->analyzeTables();
        $optimized++;

        $this->newLine();
        $this->info("✅ Database optimized! ({$optimized} operations completed)");
        $this->info('🎉 Your application should now be faster and more secure!');

        return 0;
    }

    private function runMigrations()
    {
        $this->info('📊 Running migrations...');

        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->info('✅ Migrations completed');
        } catch (\Exception $e) {
            $this->error('❌ Error running migrations: ' . $e->getMessage());
        }
    }

    private function addUuidsToExistingRecords()
    {
        $this->info('🔑 Adding UUIDs to existing records...');

        $models = [
            'User' => User::class,
            'Vendor' => Vendor::class,
            'Auction' => Auction::class,
            'AuctionBid' => AuctionBid::class,
            'XenditPayment' => XenditPayment::class,
            'VendorWallet' => VendorWallet::class,
            'VendorWithdrawal' => VendorWithdrawal::class,
            'AdminFeeSetting' => AdminFeeSetting::class,
            'AdminFeeTransaction' => AdminFeeTransaction::class,
            'DeliveryConfirmation' => DeliveryConfirmation::class,
            'ShippingInvoice' => ShippingInvoice::class,
            'VendorRating' => VendorRating::class,
            'CmsSetting' => CmsSetting::class,
            'FinancialAuditLog' => FinancialAuditLog::class,
            'Transaksi' => Transaksi::class,
            'Produk' => Produk::class,
            'Bahan' => Bahan::class,
            'Alat' => Alat::class,
            'KategoriProduk' => KategoriProduk::class,
            'Spesifikasi' => Spesifikasi::class,
            'SpesifikasiProduk' => SpesifikasiProduk::class,
            'TransaksiItem' => TransaksiItem::class,
            'TransaksiItemSpecifications' => TransaksiItemSpecifications::class,
            'WholesalePrice' => WholesalePrice::class,
            'EstimasiProduk' => EstimasiProduk::class,
            'Pelanggan' => Pelanggan::class,
        ];

        $totalUpdated = 0;

        foreach ($models as $name => $model) {
            try {
                $records = $model::whereNull('uuid')->get();
                $updated = 0;

                foreach ($records as $record) {
                    $record->update(['uuid' => Str::uuid()->toString()]);
                    $updated++;
                }

                if ($updated > 0) {
                    $this->info("  ✅ {$name}: {$updated} records updated");
                    $totalUpdated += $updated;
                }
            } catch (\Exception $e) {
                $this->warn("  ⚠️ {$name}: " . $e->getMessage());
            }
        }

        $this->info("✅ Total records updated with UUIDs: {$totalUpdated}");
    }

    private function optimizeDatabaseTables()
    {
        $this->info('⚡ Optimizing database tables...');

        try {
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableKey = 'Tables_in_' . $databaseName;

            $optimized = 0;

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                try {
                    DB::statement("OPTIMIZE TABLE `{$tableName}`");
                    $optimized++;
                } catch (\Exception $e) {
                    $this->warn("  ⚠️ Could not optimize table {$tableName}: " . $e->getMessage());
                }
            }

            $this->info("✅ Optimized {$optimized} tables");
        } catch (\Exception $e) {
            $this->error('❌ Error optimizing tables: ' . $e->getMessage());
        }
    }

    private function clearCaches()
    {
        $this->info('🧹 Clearing caches...');

        try {
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('event:clear');
            Artisan::call('queue:clear');

            $this->info('✅ All caches cleared');
        } catch (\Exception $e) {
            $this->error('❌ Error clearing caches: ' . $e->getMessage());
        }
    }

    private function analyzeTables()
    {
        $this->info('📈 Analyzing tables...');

        try {
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableKey = 'Tables_in_' . $databaseName;

            $analyzed = 0;

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                try {
                    DB::statement("ANALYZE TABLE `{$tableName}`");
                    $analyzed++;
                } catch (\Exception $e) {
                    $this->warn("  ⚠️ Could not analyze table {$tableName}: " . $e->getMessage());
                }
            }

            $this->info("✅ Analyzed {$analyzed} tables");
        } catch (\Exception $e) {
            $this->error('❌ Error analyzing tables: ' . $e->getMessage());
        }
    }
}

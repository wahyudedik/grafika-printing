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

class FixCommonIssues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:common-issues {--force : Force fix without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix common application issues automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing common application issues...');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('This will make changes to your application. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $fixed = 0;

        // 1. Clear all caches
        $this->clearAllCaches();
        $fixed++;

        // 2. Fix vendor-user relationships
        $this->fixVendorUserRelationships();
        $fixed++;

        // 3. Create missing wallets
        $this->createMissingWallets();
        $fixed++;

        // 4. Fix orphaned records
        $this->fixOrphanedRecords();
        $fixed++;

        // 5. Recreate storage symlink
        $this->recreateStorageSymlink();
        $fixed++;

        // 6. Fix file permissions
        $this->fixFilePermissions();
        $fixed++;

        // 7. Update admin fee settings
        $this->updateAdminFeeSettings();
        $fixed++;

        // 8. Fix route cache
        $this->fixRouteCache();
        $fixed++;

        // 9. Fix configuration cache
        $this->fixConfigurationCache();
        $fixed++;

        // 10. Fix view cache
        $this->fixViewCache();
        $fixed++;

        $this->newLine();
        $this->info("✅ Fixed {$fixed} common issues!");
        $this->info('🎉 Application should now work more reliably!');

        return 0;
    }

    private function clearAllCaches()
    {
        $this->info('🧹 Clearing all caches...');

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

    private function fixVendorUserRelationships()
    {
        $this->info('🔗 Fixing vendor-user relationships...');

        try {
            $vendorUsers = User::where('usertype', 'vendor')->get();
            $fixed = 0;

            foreach ($vendorUsers as $user) {
                if ($user->vendorUser->isEmpty()) {
                    // Try to find existing vendor by name or email
                    $vendor = Vendor::where('name', $user->name)
                        ->orWhere('email', $user->email)
                        ->first();

                    if (!$vendor) {
                        // Create new vendor
                        $vendor = Vendor::create([
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone ?? null,
                            'address' => 'Address not set',
                            'is_active' => true,
                            'bank_verified' => false
                        ]);
                    }

                    // Create vendor-user relationship
                    DB::table('vendor_user')->insert([
                        'user_id' => $user->id,
                        'vendor_id' => $vendor->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $fixed++;
                }
            }

            $this->info("✅ Fixed {$fixed} vendor-user relationships");
        } catch (\Exception $e) {
            $this->error('❌ Error fixing vendor-user relationships: ' . $e->getMessage());
        }
    }

    private function createMissingWallets()
    {
        $this->info('💰 Creating missing wallets...');

        try {
            $vendors = Vendor::all();
            $created = 0;

            foreach ($vendors as $vendor) {
                if (!$vendor->wallet) {
                    VendorWallet::create([
                        'vendor_id' => $vendor->id,
                        'balance' => 0,
                        'is_frozen' => false
                    ]);
                    $created++;
                }
            }

            $this->info("✅ Created {$created} missing wallets");
        } catch (\Exception $e) {
            $this->error('❌ Error creating wallets: ' . $e->getMessage());
        }
    }

    private function fixOrphanedRecords()
    {
        $this->info('🧹 Fixing orphaned records...');

        try {
            // Fix orphaned auction bids
            $orphanedBids = AuctionBid::whereDoesntHave('auction')->count();
            if ($orphanedBids > 0) {
                AuctionBid::whereDoesntHave('auction')->delete();
                $this->info("✅ Removed {$orphanedBids} orphaned auction bids");
            }

            // Fix orphaned payments
            $orphanedPayments = XenditPayment::whereDoesntHave('auction')->count();
            if ($orphanedPayments > 0) {
                XenditPayment::whereDoesntHave('auction')->delete();
                $this->info("✅ Removed {$orphanedPayments} orphaned payments");
            }

            // Fix orphaned transactions
            $orphanedTransactions = Transaksi::whereDoesntHave('vendor')->count();
            if ($orphanedTransactions > 0) {
                Transaksi::whereDoesntHave('vendor')->delete();
                $this->info("✅ Removed {$orphanedTransactions} orphaned transactions");
            }
        } catch (\Exception $e) {
            $this->error('❌ Error fixing orphaned records: ' . $e->getMessage());
        }
    }

    private function recreateStorageSymlink()
    {
        $this->info('🔗 Recreating storage symlink...');

        try {
            // Remove existing symlink if it exists
            if (file_exists(public_path('storage'))) {
                unlink(public_path('storage'));
            }

            // Create new symlink
            Artisan::call('storage:link');

            $this->info('✅ Storage symlink recreated');
        } catch (\Exception $e) {
            $this->error('❌ Error recreating storage symlink: ' . $e->getMessage());
        }
    }

    private function fixFilePermissions()
    {
        $this->info('📁 Fixing file permissions...');

        try {
            $directories = [
                storage_path('app'),
                storage_path('logs'),
                storage_path('framework/cache'),
                storage_path('framework/sessions'),
                storage_path('framework/views'),
                public_path('storage')
            ];

            foreach ($directories as $dir) {
                if (is_dir($dir)) {
                    chmod($dir, 0755);
                }
            }

            $this->info('✅ File permissions fixed');
        } catch (\Exception $e) {
            $this->error('❌ Error fixing file permissions: ' . $e->getMessage());
        }
    }

    private function updateAdminFeeSettings()
    {
        $this->info('⚙️ Updating admin fee settings...');

        try {
            // Check if admin fee settings exist
            if (AdminFeeSetting::count() === 0) {
                // Create default admin fee settings
                AdminFeeSetting::create([
                    'name' => 'Biaya Admin 10%',
                    'description' => 'Biaya admin aplikasi 10%',
                    'type' => 'percentage',
                    'value' => 10.00,
                    'minimum_amount' => 10000,
                    'maximum_amount' => 10000000,
                    'category' => 'auction',
                    'is_active' => true,
                    'created_by' => 1
                ]);

                $this->info('✅ Default admin fee settings created');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error updating admin fee settings: ' . $e->getMessage());
        }
    }

    private function fixRouteCache()
    {
        $this->info('🛣️ Fixing route cache...');

        try {
            Artisan::call('route:clear');
            Artisan::call('route:cache');

            $this->info('✅ Route cache fixed');
        } catch (\Exception $e) {
            $this->error('❌ Error fixing route cache: ' . $e->getMessage());
        }
    }

    private function fixConfigurationCache()
    {
        $this->info('⚙️ Fixing configuration cache...');

        try {
            Artisan::call('config:clear');
            Artisan::call('config:cache');

            $this->info('✅ Configuration cache fixed');
        } catch (\Exception $e) {
            $this->error('❌ Error fixing configuration cache: ' . $e->getMessage());
        }
    }

    private function fixViewCache()
    {
        $this->info('👁️ Fixing view cache...');

        try {
            Artisan::call('view:clear');
            Artisan::call('view:cache');

            $this->info('✅ View cache fixed');
        } catch (\Exception $e) {
            $this->error('❌ Error fixing view cache: ' . $e->getMessage());
        }
    }
}

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

class QuickFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quick:fix {--issue= : Specific issue to fix} {--all : Fix all common issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quick fix for common application issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Quick Fix - Solving common issues...');
        $this->newLine();

        $issue = $this->option('issue');
        $all = $this->option('all');

        if ($all) {
            $this->fixAllIssues();
        } elseif ($issue) {
            $this->fixSpecificIssue($issue);
        } else {
            $this->showAvailableFixes();
        }

        return 0;
    }

    private function showAvailableFixes()
    {
        $this->info('🔧 Available quick fixes:');
        $this->newLine();

        $fixes = [
            'cache' => 'Clear all caches',
            'routes' => 'Fix route issues',
            'vendor-relationships' => 'Fix vendor-user relationships',
            'wallets' => 'Create missing wallets',
            'orphaned' => 'Clean up orphaned records',
            'permissions' => 'Fix file permissions',
            'storage' => 'Recreate storage symlink',
            'config' => 'Fix configuration issues',
            'migrations' => 'Run pending migrations',
            'seeds' => 'Run database seeders'
        ];

        foreach ($fixes as $key => $description) {
            $this->line("• {$key}: {$description}");
        }

        $this->newLine();
        $this->info('Usage: php artisan quick:fix --issue=cache');
        $this->info('Usage: php artisan quick:fix --all');
    }

    private function fixAllIssues()
    {
        $this->info('🔧 Fixing all common issues...');
        $this->newLine();

        $issues = [
            'cache',
            'routes',
            'vendor-relationships',
            'wallets',
            'orphaned',
            'permissions',
            'storage',
            'config',
            'migrations'
        ];

        foreach ($issues as $issue) {
            $this->fixSpecificIssue($issue);
        }

        $this->newLine();
        $this->info('✅ All issues fixed!');
    }

    private function fixSpecificIssue($issue)
    {
        switch ($issue) {
            case 'cache':
                $this->fixCacheIssues();
                break;
            case 'routes':
                $this->fixRouteIssues();
                break;
            case 'vendor-relationships':
                $this->fixVendorRelationshipIssues();
                break;
            case 'wallets':
                $this->fixWalletIssues();
                break;
            case 'orphaned':
                $this->fixOrphanedIssues();
                break;
            case 'permissions':
                $this->fixPermissionIssues();
                break;
            case 'storage':
                $this->fixStorageIssues();
                break;
            case 'config':
                $this->fixConfigIssues();
                break;
            case 'migrations':
                $this->fixMigrationIssues();
                break;
            case 'seeds':
                $this->fixSeedIssues();
                break;
            default:
                $this->error("Unknown issue: {$issue}");
                $this->showAvailableFixes();
        }
    }

    private function fixCacheIssues()
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

    private function fixRouteIssues()
    {
        $this->info('🛣️ Fixing route issues...');

        try {
            Artisan::call('route:clear');
            Artisan::call('route:cache');

            $this->info('✅ Routes fixed');
        } catch (\Exception $e) {
            $this->error('❌ Error fixing routes: ' . $e->getMessage());
        }
    }

    private function fixVendorRelationshipIssues()
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
            $this->error('❌ Error fixing vendor relationships: ' . $e->getMessage());
        }
    }

    private function fixWalletIssues()
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

    private function fixOrphanedIssues()
    {
        $this->info('🧹 Cleaning up orphaned records...');

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

            $this->info('✅ Orphaned records cleaned up');
        } catch (\Exception $e) {
            $this->error('❌ Error cleaning orphaned records: ' . $e->getMessage());
        }
    }

    private function fixPermissionIssues()
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
            $this->error('❌ Error fixing permissions: ' . $e->getMessage());
        }
    }

    private function fixStorageIssues()
    {
        $this->info('🔗 Fixing storage symlink...');

        try {
            // Remove existing symlink if it exists
            if (file_exists(public_path('storage'))) {
                if (is_link(public_path('storage'))) {
                    unlink(public_path('storage'));
                } else {
                    rmdir(public_path('storage'));
                }
            }

            // Create new symlink
            Artisan::call('storage:link');

            $this->info('✅ Storage symlink fixed');
        } catch (\Exception $e) {
            $this->error('❌ Error fixing storage: ' . $e->getMessage());
        }
    }

    private function fixConfigIssues()
    {
        $this->info('⚙️ Fixing configuration issues...');

        try {
            Artisan::call('config:clear');
            Artisan::call('config:cache');

            $this->info('✅ Configuration fixed');
        } catch (\Exception $e) {
            $this->error('❌ Error fixing configuration: ' . $e->getMessage());
        }
    }

    private function fixMigrationIssues()
    {
        $this->info('📊 Running pending migrations...');

        try {
            Artisan::call('migrate', ['--force' => true]);

            $this->info('✅ Migrations completed');
        } catch (\Exception $e) {
            $this->error('❌ Error running migrations: ' . $e->getMessage());
        }
    }

    private function fixSeedIssues()
    {
        $this->info('🌱 Running database seeders...');

        try {
            Artisan::call('db:seed', ['--force' => true]);

            $this->info('✅ Seeders completed');
        } catch (\Exception $e) {
            $this->error('❌ Error running seeders: ' . $e->getMessage());
        }
    }
}

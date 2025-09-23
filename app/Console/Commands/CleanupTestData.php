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

class CleanupTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:test-data {--force : Force cleanup without confirmation} {--dry-run : Show what would be cleaned without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup duplicate test data and fix data integrity issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting test data cleanup...');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('This will cleanup test data and fix duplicates. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $cleaned = 0;

        // 1. Cleanup duplicate users
        $this->cleanupDuplicateUsers();
        $cleaned++;

        // 2. Cleanup duplicate vendor ratings
        $this->cleanupDuplicateVendorRatings();
        $cleaned++;

        // 3. Cleanup orphaned records
        $this->cleanupOrphanedRecords();
        $cleaned++;

        // 4. Fix data truncation issues
        $this->fixDataTruncationIssues();
        $cleaned++;

        // 5. Cleanup test data
        $this->cleanupTestData();
        $cleaned++;

        $this->newLine();
        $this->info("✅ Test data cleanup completed! ({$cleaned} operations completed)");
        $this->info('🎉 Your database is now clean and optimized!');

        return 0;
    }

    private function cleanupDuplicateUsers()
    {
        $this->info('👥 Cleaning up duplicate users...');

        try {
            // Find duplicate emails
            $duplicates = DB::table('users')
                ->select('email', DB::raw('COUNT(*) as count'))
                ->groupBy('email')
                ->having('count', '>', 1)
                ->get();

            $cleaned = 0;

            foreach ($duplicates as $duplicate) {
                $users = User::where('email', $duplicate->email)->orderBy('created_at')->get();

                // Keep the first user, delete the rest
                for ($i = 1; $i < $users->count(); $i++) {
                    if (!$this->option('dry-run')) {
                        $users[$i]->delete();
                    }
                    $cleaned++;
                }
            }

            $this->info("  ✅ Cleaned up {$cleaned} duplicate users");
        } catch (\Exception $e) {
            $this->error("  ❌ Error cleaning duplicate users: " . $e->getMessage());
        }
    }

    private function cleanupDuplicateVendorRatings()
    {
        $this->info('⭐ Cleaning up duplicate vendor ratings...');

        try {
            // Find duplicate ratings (same user and auction)
            $duplicates = DB::table('vendor_ratings')
                ->select('user_id', 'auction_id', DB::raw('COUNT(*) as count'))
                ->groupBy('user_id', 'auction_id')
                ->having('count', '>', 1)
                ->get();

            $cleaned = 0;

            foreach ($duplicates as $duplicate) {
                $ratings = VendorRating::where('user_id', $duplicate->user_id)
                    ->where('auction_id', $duplicate->auction_id)
                    ->orderBy('created_at')
                    ->get();

                // Keep the first rating, delete the rest
                for ($i = 1; $i < $ratings->count(); $i++) {
                    if (!$this->option('dry-run')) {
                        $ratings[$i]->delete();
                    }
                    $cleaned++;
                }
            }

            $this->info("  ✅ Cleaned up {$cleaned} duplicate vendor ratings");
        } catch (\Exception $e) {
            $this->error("  ❌ Error cleaning duplicate vendor ratings: " . $e->getMessage());
        }
    }

    private function cleanupOrphanedRecords()
    {
        $this->info('🧹 Cleaning up orphaned records...');

        try {
            $cleaned = 0;

            // Cleanup orphaned auction bids
            $orphanedBids = AuctionBid::whereDoesntHave('auction')->count();
            if ($orphanedBids > 0) {
                if (!$this->option('dry-run')) {
                    AuctionBid::whereDoesntHave('auction')->delete();
                }
                $cleaned += $orphanedBids;
                $this->info("  ✅ Cleaned up {$orphanedBids} orphaned auction bids");
            }

            // Cleanup orphaned payments
            $orphanedPayments = XenditPayment::whereDoesntHave('auction')->count();
            if ($orphanedPayments > 0) {
                if (!$this->option('dry-run')) {
                    XenditPayment::whereDoesntHave('auction')->delete();
                }
                $cleaned += $orphanedPayments;
                $this->info("  ✅ Cleaned up {$orphanedPayments} orphaned payments");
            }

            // Cleanup orphaned transactions
            $orphanedTransactions = Transaksi::whereDoesntHave('vendor')->count();
            if ($orphanedTransactions > 0) {
                if (!$this->option('dry-run')) {
                    Transaksi::whereDoesntHave('vendor')->delete();
                }
                $cleaned += $orphanedTransactions;
                $this->info("  ✅ Cleaned up {$orphanedTransactions} orphaned transactions");
            }

            // Cleanup orphaned wallet transactions
            $orphanedWalletTransactions = \App\Models\VendorWalletTransaction::whereDoesntHave('vendorWallet')->count();
            if ($orphanedWalletTransactions > 0) {
                if (!$this->option('dry-run')) {
                    \App\Models\VendorWalletTransaction::whereDoesntHave('vendorWallet')->delete();
                }
                $cleaned += $orphanedWalletTransactions;
                $this->info("  ✅ Cleaned up {$orphanedWalletTransactions} orphaned wallet transactions");
            }

            $this->info("  ✅ Total orphaned records cleaned: {$cleaned}");
        } catch (\Exception $e) {
            $this->error("  ❌ Error cleaning orphaned records: " . $e->getMessage());
        }
    }

    private function fixDataTruncationIssues()
    {
        $this->info('🔧 Fixing data truncation issues...');

        try {
            $fixed = 0;

            // Fix vendor wallet transaction categories
            $longCategories = \App\Models\VendorWalletTransaction::whereRaw('LENGTH(category) > 50')->get();
            foreach ($longCategories as $transaction) {
                if (!$this->option('dry-run')) {
                    $transaction->update(['category' => substr($transaction->category, 0, 50)]);
                }
                $fixed++;
            }

            // Fix financial audit log action types
            $longActionTypes = FinancialAuditLog::whereRaw('LENGTH(action_type) > 100')->get();
            foreach ($longActionTypes as $log) {
                if (!$this->option('dry-run')) {
                    $log->update(['action_type' => substr($log->action_type, 0, 100)]);
                }
                $fixed++;
            }

            // Fix vendor withdrawal account numbers
            $longAccountNumbers = VendorWithdrawal::whereRaw('LENGTH(account_number) > 50')->get();
            foreach ($longAccountNumbers as $withdrawal) {
                if (!$this->option('dry-run')) {
                    $withdrawal->update(['account_number' => substr($withdrawal->account_number, 0, 50)]);
                }
                $fixed++;
            }

            $this->info("  ✅ Fixed {$fixed} data truncation issues");
        } catch (\Exception $e) {
            $this->error("  ❌ Error fixing data truncation: " . $e->getMessage());
        }
    }

    private function cleanupTestData()
    {
        $this->info('🧪 Cleaning up test data...');

        try {
            $cleaned = 0;

            // Cleanup test users
            $testUsers = User::where('email', 'like', '%test%')
                ->orWhere('email', 'like', '%example%')
                ->orWhere('name', 'like', '%Test%')
                ->get();

            foreach ($testUsers as $user) {
                if (!$this->option('dry-run')) {
                    $user->delete();
                }
                $cleaned++;
            }

            // Cleanup test vendors
            $testVendors = Vendor::where('email', 'like', '%test%')
                ->orWhere('email', 'like', '%example%')
                ->orWhere('name', 'like', '%Test%')
                ->get();

            foreach ($testVendors as $vendor) {
                if (!$this->option('dry-run')) {
                    $vendor->delete();
                }
                $cleaned++;
            }

            // Cleanup test auctions
            $testAuctions = Auction::where('title', 'like', '%Test%')
                ->orWhere('description', 'like', '%test%')
                ->get();

            foreach ($testAuctions as $auction) {
                if (!$this->option('dry-run')) {
                    $auction->delete();
                }
                $cleaned++;
            }

            $this->info("  ✅ Cleaned up {$cleaned} test records");
        } catch (\Exception $e) {
            $this->error("  ❌ Error cleaning test data: " . $e->getMessage());
        }
    }
}

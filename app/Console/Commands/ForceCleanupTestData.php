<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ForceCleanupTestData extends Command
{
    protected $signature = 'cleanup:force-test-data {--force : Force cleanup without confirmation}';
    protected $description = 'Forcefully cleans up test data by disabling foreign key checks.';

    public function handle()
    {
        $this->info('🧹 Starting force test data cleanup...');

        if (!$this->option('force')) {
            if (!$this->confirm('This will forcefully clean up ALL test data. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tablesToTruncate = [
            'vendor_ratings',
            'shipping_invoices',
            'delivery_confirmations',
            'xendit_payments',
            'auction_bids',
            'auctions',
            'vendor_wallet_transactions',
            'vendor_withdrawals',
            'admin_fee_transactions',
            'financial_audit_logs',
            'vendor_wallets',
            'vendor_user',
            'vendors',
            'users',
            'transaksis',
            'transaksi_items',
            'transaksi_item_specifications',
            'spesifikasi_produks',
            'harga_grosir',
            'estimasi_produks',
            'produks',
            'bahans',
            'alats',
            'kategori_produks',
            'spesifikasis',
            'pelanggans',
            'admin_fee_settings',
            'cms_settings',
        ];

        foreach ($tablesToTruncate as $table) {
            try {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->info("  ✅ Table `{$table}` truncated.");
                }
            } catch (\Exception $e) {
                $this->warn("  ⚠️ Could not truncate table `{$table}`: " . $e->getMessage());
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('✅ Force test data cleanup completed');
        return 0;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->info('Adding smart indexes for performance...');

        // Add indexes only if they don't exist
        $this->addIndexIfNotExists('users', 'email');
        $this->addIndexIfNotExists('users', 'usertype');
        $this->addIndexIfNotExists('users', 'email_verified_at');

        $this->addIndexIfNotExists('vendors', 'email');
        $this->addIndexIfNotExists('vendors', 'is_active');
        $this->addIndexIfNotExists('vendors', 'bank_verified');

        $this->addIndexIfNotExists('auctions', 'user_id');
        $this->addIndexIfNotExists('auctions', 'status');
        $this->addIndexIfNotExists('auctions', 'deadline');

        $this->addIndexIfNotExists('auction_bids', 'auction_id');
        $this->addIndexIfNotExists('auction_bids', 'vendor_id');
        $this->addIndexIfNotExists('auction_bids', 'status');

        $this->addIndexIfNotExists('xendit_payments', 'auction_id');
        $this->addIndexIfNotExists('xendit_payments', 'external_id');
        $this->addIndexIfNotExists('xendit_payments', 'status');

        $this->addIndexIfNotExists('vendor_wallets', 'vendor_id');
        $this->addIndexIfNotExists('vendor_wallets', 'is_frozen');

        $this->addIndexIfNotExists('vendor_withdrawals', 'vendor_id');
        $this->addIndexIfNotExists('vendor_withdrawals', 'status');
        $this->addIndexIfNotExists('vendor_withdrawals', 'method');

        $this->addIndexIfNotExists('admin_fee_settings', 'is_active');
        $this->addIndexIfNotExists('admin_fee_settings', 'category');

        $this->addIndexIfNotExists('admin_fee_transactions', 'auction_id');
        $this->addIndexIfNotExists('admin_fee_transactions', 'vendor_id');
        $this->addIndexIfNotExists('admin_fee_transactions', 'status');

        $this->addIndexIfNotExists('delivery_confirmations', 'auction_id');
        $this->addIndexIfNotExists('delivery_confirmations', 'delivery_status');

        $this->addIndexIfNotExists('shipping_invoices', 'auction_id');
        $this->addIndexIfNotExists('shipping_invoices', 'shipping_status');
        $this->addIndexIfNotExists('shipping_invoices', 'payment_status');

        $this->addIndexIfNotExists('vendor_ratings', 'vendor_id');
        $this->addIndexIfNotExists('vendor_ratings', 'user_id');
        $this->addIndexIfNotExists('vendor_ratings', 'auction_id');
        $this->addIndexIfNotExists('vendor_ratings', 'is_verified');

        $this->addIndexIfNotExists('cms_settings', 'is_active');
        $this->addIndexIfNotExists('cms_settings', 'category');

        $this->addIndexIfNotExists('financial_audit_logs', 'user_id');
        $this->addIndexIfNotExists('financial_audit_logs', 'vendor_id');
        $this->addIndexIfNotExists('financial_audit_logs', 'action_type');
        $this->addIndexIfNotExists('financial_audit_logs', 'risk_level');

        $this->addIndexIfNotExists('transaksis', 'vendor_id');
        $this->addIndexIfNotExists('transaksis', 'user_id');
        $this->addIndexIfNotExists('transaksis', 'status');

        $this->addIndexIfNotExists('produks', 'vendor_id');
        $this->addIndexIfNotExists('produks', 'kategori_id');
        $this->addIndexIfNotExists('produks', 'is_active');

        $this->addIndexIfNotExists('bahans', 'vendor_id');
        $this->addIndexIfNotExists('bahans', 'is_active');

        $this->addIndexIfNotExists('alats', 'vendor_id');
        $this->addIndexIfNotExists('alats', 'status');

        $this->addIndexIfNotExists('kategori_produks', 'vendor_id');
        $this->addIndexIfNotExists('kategori_produks', 'is_active');

        $this->addIndexIfNotExists('spesifikasis', 'vendor_id');
        $this->addIndexIfNotExists('spesifikasis', 'is_active');

        $this->addIndexIfNotExists('spesifikasi_produks', 'vendor_id');
        $this->addIndexIfNotExists('spesifikasi_produks', 'produk_id');
        $this->addIndexIfNotExists('spesifikasi_produks', 'spesifikasi_id');

        $this->addIndexIfNotExists('transaksi_items', 'vendor_id');
        $this->addIndexIfNotExists('transaksi_items', 'transaksi_id');
        $this->addIndexIfNotExists('transaksi_items', 'produk_id');

        $this->addIndexIfNotExists('transaksi_item_specifications', 'vendor_id');
        $this->addIndexIfNotExists('transaksi_item_specifications', 'transaksi_item_id');
        $this->addIndexIfNotExists('transaksi_item_specifications', 'spesifikasi_produk_id');

        $this->addIndexIfNotExists('wholesale_prices', 'vendor_id');
        $this->addIndexIfNotExists('wholesale_prices', 'bahan_id');

        $this->addIndexIfNotExists('estimasi_produks', 'vendor_id');
        $this->addIndexIfNotExists('estimasi_produks', 'produk_id');
        $this->addIndexIfNotExists('estimasi_produks', 'alat_id');

        $this->addIndexIfNotExists('pelanggans', 'vendor_id');
        $this->addIndexIfNotExists('pelanggans', 'email');
        $this->addIndexIfNotExists('pelanggans', 'phone');

        $this->addIndexIfNotExists('vendor_wallet_transactions', 'vendor_wallet_id');
        $this->addIndexIfNotExists('vendor_wallet_transactions', 'vendor_id');
        $this->addIndexIfNotExists('vendor_wallet_transactions', 'type');
        $this->addIndexIfNotExists('vendor_wallet_transactions', 'category');
        $this->addIndexIfNotExists('vendor_wallet_transactions', 'status');

        $this->addIndexIfNotExists('vendor_user', 'user_id');
        $this->addIndexIfNotExists('vendor_user', 'vendor_id');

        $this->info('Smart indexes added successfully!');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        $tables = [
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
            'pelanggans',
            'vendor_wallet_transactions',
            'vendor_user'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    // Drop indexes
                    $indexes = [
                        'email',
                        'usertype',
                        'email_verified_at',
                        'is_active',
                        'bank_verified',
                        'status',
                        'deadline',
                        'auction_id',
                        'vendor_id',
                        'user_id',
                        'external_id',
                        'is_frozen',
                        'method',
                        'category',
                        'action_type',
                        'entity_type',
                        'risk_level',
                        'kategori_id',
                        'produk_id',
                        'spesifikasi_id',
                        'transaksi_id',
                        'bahan_id',
                        'alat_id',
                        'pelanggan_id',
                        'vendor_wallet_id',
                        'type',
                        'phone',
                        'created_at'
                    ];

                    foreach ($indexes as $index) {
                        try {
                            $table->dropIndex([$index]);
                        } catch (\Exception $e) {
                            // Index might not exist
                        }
                    }
                });
            }
        }
    }

    private function addIndexIfNotExists($table, $column)
    {
        try {
            if (Schema::hasTable($table)) {
                $indexName = $table . '_' . $column . '_index';

                // Check if index already exists
                $indexes = DB::select("SHOW INDEX FROM {$table}");
                $indexExists = false;

                foreach ($indexes as $index) {
                    if ($index->Key_name === $indexName) {
                        $indexExists = true;
                        break;
                    }
                }

                if (!$indexExists) {
                    DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` (`{$column}`)");
                    $this->info("  ✅ Added index: {$indexName}");
                } else {
                    $this->info("  ⚠️ Index already exists: {$indexName}");
                }
            }
        } catch (\Exception $e) {
            echo "  ⚠️ Could not add index {$table}.{$column}: " . $e->getMessage() . "\n";
        }
    }

    private function info($message)
    {
        echo $message . "\n";
    }
};

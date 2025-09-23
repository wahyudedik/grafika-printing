<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add UUIDs and indexes to users table
        if (Schema::hasTable('users')) {
            if (!Schema::hasColumn('users', 'uuid')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to vendors table
        if (Schema::hasTable('vendors')) {
            if (!Schema::hasColumn('vendors', 'uuid')) {
                Schema::table('vendors', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to auctions table
        if (Schema::hasTable('auctions')) {
            if (!Schema::hasColumn('auctions', 'uuid')) {
                Schema::table('auctions', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to xendit_payments table
        if (Schema::hasTable('xendit_payments')) {
            if (!Schema::hasColumn('xendit_payments', 'uuid')) {
                Schema::table('xendit_payments', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to vendor_wallets table
        if (Schema::hasTable('vendor_wallets')) {
            if (!Schema::hasColumn('vendor_wallets', 'uuid')) {
                Schema::table('vendor_wallets', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to vendor_withdrawals table
        if (Schema::hasTable('vendor_withdrawals')) {
            if (!Schema::hasColumn('vendor_withdrawals', 'uuid')) {
                Schema::table('vendor_withdrawals', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to admin_fee_settings table
        if (Schema::hasTable('admin_fee_settings')) {
            if (!Schema::hasColumn('admin_fee_settings', 'uuid')) {
                Schema::table('admin_fee_settings', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to admin_fee_transactions table
        if (Schema::hasTable('admin_fee_transactions')) {
            if (!Schema::hasColumn('admin_fee_transactions', 'uuid')) {
                Schema::table('admin_fee_transactions', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to delivery_confirmations table
        if (Schema::hasTable('delivery_confirmations')) {
            if (!Schema::hasColumn('delivery_confirmations', 'uuid')) {
                Schema::table('delivery_confirmations', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to shipping_invoices table
        if (Schema::hasTable('shipping_invoices')) {
            if (!Schema::hasColumn('shipping_invoices', 'uuid')) {
                Schema::table('shipping_invoices', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to vendor_ratings table
        if (Schema::hasTable('vendor_ratings')) {
            if (!Schema::hasColumn('vendor_ratings', 'uuid')) {
                Schema::table('vendor_ratings', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to cms_settings table
        if (Schema::hasTable('cms_settings')) {
            if (!Schema::hasColumn('cms_settings', 'uuid')) {
                Schema::table('cms_settings', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to financial_audit_logs table
        if (Schema::hasTable('financial_audit_logs')) {
            if (!Schema::hasColumn('financial_audit_logs', 'uuid')) {
                Schema::table('financial_audit_logs', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to transaksis table
        if (Schema::hasTable('transaksis')) {
            if (!Schema::hasColumn('transaksis', 'uuid')) {
                Schema::table('transaksis', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to produks table
        if (Schema::hasTable('produks')) {
            if (!Schema::hasColumn('produks', 'uuid')) {
                Schema::table('produks', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to bahans table
        if (Schema::hasTable('bahans')) {
            if (!Schema::hasColumn('bahans', 'uuid')) {
                Schema::table('bahans', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to alats table
        if (Schema::hasTable('alats')) {
            if (!Schema::hasColumn('alats', 'uuid')) {
                Schema::table('alats', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to kategori_produks table
        if (Schema::hasTable('kategori_produks')) {
            if (!Schema::hasColumn('kategori_produks', 'uuid')) {
                Schema::table('kategori_produks', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to spesifikasis table
        if (Schema::hasTable('spesifikasis')) {
            if (!Schema::hasColumn('spesifikasis', 'uuid')) {
                Schema::table('spesifikasis', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to spesifikasi_produks table
        if (Schema::hasTable('spesifikasi_produks')) {
            if (!Schema::hasColumn('spesifikasi_produks', 'uuid')) {
                Schema::table('spesifikasi_produks', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to transaksi_items table
        if (Schema::hasTable('transaksi_items')) {
            if (!Schema::hasColumn('transaksi_items', 'uuid')) {
                Schema::table('transaksi_items', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to transaksi_item_specifications table
        if (Schema::hasTable('transaksi_item_specifications')) {
            if (!Schema::hasColumn('transaksi_item_specifications', 'uuid')) {
                Schema::table('transaksi_item_specifications', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to wholesale_prices table
        if (Schema::hasTable('wholesale_prices')) {
            if (!Schema::hasColumn('wholesale_prices', 'uuid')) {
                Schema::table('wholesale_prices', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to estimasi_produks table
        if (Schema::hasTable('estimasi_produks')) {
            if (!Schema::hasColumn('estimasi_produks', 'uuid')) {
                Schema::table('estimasi_produks', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to pelanggans table
        if (Schema::hasTable('pelanggans')) {
            if (!Schema::hasColumn('pelanggans', 'uuid')) {
                Schema::table('pelanggans', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to auction_bids table
        if (Schema::hasTable('auction_bids')) {
            if (!Schema::hasColumn('auction_bids', 'uuid')) {
                Schema::table('auction_bids', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }

        // Add UUIDs and indexes to vendor_wallet_transactions table
        if (Schema::hasTable('vendor_wallet_transactions')) {
            if (!Schema::hasColumn('vendor_wallet_transactions', 'uuid')) {
                Schema::table('vendor_wallet_transactions', function (Blueprint $table) {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove UUID columns
        $tables = [
            'users',
            'vendors',
            'auctions',
            'xendit_payments',
            'vendor_wallets',
            'vendor_withdrawals',
            'admin_fee_settings',
            'admin_fee_transactions',
            'delivery_confirmations',
            'shipping_invoices',
            'vendor_ratings',
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
            'auction_bids',
            'vendor_wallet_transactions',
            'cms_settings'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('uuid');
                });
            }
        }
    }
};

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
        // Vendor withdrawals encryption fields
        Schema::table('vendor_withdrawals', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_withdrawals', 'encrypted_account_number')) {
                $table->text('encrypted_account_number')->nullable();
            }
            if (!Schema::hasColumn('vendor_withdrawals', 'encrypted_account_name')) {
                $table->text('encrypted_account_name')->nullable();
            }
            if (!Schema::hasColumn('vendor_withdrawals', 'encrypted_bank_name')) {
                $table->text('encrypted_bank_name')->nullable();
            }
            if (!Schema::hasColumn('vendor_withdrawals', 'encrypted_amount')) {
                $table->text('encrypted_amount')->nullable();
            }
            if (!Schema::hasColumn('vendor_withdrawals', 'encrypted_net_amount')) {
                $table->text('encrypted_net_amount')->nullable();
            }
        });

        // Vendor wallets encryption fields
        Schema::table('vendor_wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_wallets', 'encrypted_balance')) {
                $table->text('encrypted_balance')->nullable();
            }
            if (!Schema::hasColumn('vendor_wallets', 'encrypted_frozen_amount')) {
                $table->text('encrypted_frozen_amount')->nullable();
            }
            if (!Schema::hasColumn('vendor_wallets', 'encrypted_pending_amount')) {
                $table->text('encrypted_pending_amount')->nullable();
            }
        });

        // Xendit payments encryption fields
        Schema::table('xendit_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('xendit_payments', 'encrypted_amount')) {
                $table->text('encrypted_amount')->nullable();
            }
            if (!Schema::hasColumn('xendit_payments', 'encrypted_customer')) {
                $table->text('encrypted_customer')->nullable();
            }
            if (!Schema::hasColumn('xendit_payments', 'encrypted_items')) {
                $table->text('encrypted_items')->nullable();
            }
            if (!Schema::hasColumn('xendit_payments', 'encrypted_account_number')) {
                $table->text('encrypted_account_number')->nullable();
            }
        });

        // Users encryption fields
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'encrypted_phone')) {
                $table->text('encrypted_phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'encrypted_address')) {
                $table->text('encrypted_address')->nullable();
            }
            if (!Schema::hasColumn('users', 'encrypted_bank_account')) {
                $table->text('encrypted_bank_account')->nullable();
            }
        });

        // Vendors encryption fields
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'encrypted_phone')) {
                $table->text('encrypted_phone')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'encrypted_address')) {
                $table->text('encrypted_address')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'encrypted_bank_account_number')) {
                $table->text('encrypted_bank_account_number')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'encrypted_bank_name')) {
                $table->text('encrypted_bank_name')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'encrypted_bank_account_name')) {
                $table->text('encrypted_bank_account_name')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'encrypted_tax_number')) {
                $table->text('encrypted_tax_number')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'encrypted_business_license')) {
                $table->text('encrypted_business_license')->nullable();
            }
        });

        // Financial audit logs encryption fields
        Schema::table('financial_audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('financial_audit_logs', 'encrypted_old_data')) {
                $table->text('encrypted_old_data')->nullable();
            }
            if (!Schema::hasColumn('financial_audit_logs', 'encrypted_new_data')) {
                $table->text('encrypted_new_data')->nullable();
            }
            if (!Schema::hasColumn('financial_audit_logs', 'encrypted_ip_address')) {
                $table->text('encrypted_ip_address')->nullable();
            }
            if (!Schema::hasColumn('financial_audit_logs', 'encrypted_user_agent')) {
                $table->text('encrypted_user_agent')->nullable();
            }
        });

        // Admin fee transactions encryption fields
        Schema::table('admin_fee_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_fee_transactions', 'encrypted_auction_amount')) {
                $table->text('encrypted_auction_amount')->nullable();
            }
            if (!Schema::hasColumn('admin_fee_transactions', 'encrypted_admin_fee_amount')) {
                $table->text('encrypted_admin_fee_amount')->nullable();
            }
            if (!Schema::hasColumn('admin_fee_transactions', 'encrypted_total_amount')) {
                $table->text('encrypted_total_amount')->nullable();
            }
            if (!Schema::hasColumn('admin_fee_transactions', 'encrypted_vendor_receives')) {
                $table->text('encrypted_vendor_receives')->nullable();
            }
            if (!Schema::hasColumn('admin_fee_transactions', 'encrypted_admin_receives')) {
                $table->text('encrypted_admin_receives')->nullable();
            }
        });

        // Escrow payments encryption fields
        Schema::table('escrow_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('escrow_payments', 'encrypted_amount')) {
                $table->text('encrypted_amount')->nullable();
            }
            if (!Schema::hasColumn('escrow_payments', 'encrypted_admin_fee')) {
                $table->text('encrypted_admin_fee')->nullable();
            }
            if (!Schema::hasColumn('escrow_payments', 'encrypted_vendor_amount')) {
                $table->text('encrypted_vendor_amount')->nullable();
            }
        });

        // Mediation requests encryption fields
        Schema::table('mediation_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('mediation_requests', 'encrypted_compensation_amount')) {
                $table->text('encrypted_compensation_amount')->nullable();
            }
            if (!Schema::hasColumn('mediation_requests', 'encrypted_penalty_amount')) {
                $table->text('encrypted_penalty_amount')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop encryption fields
        Schema::table('mediation_requests', function (Blueprint $table) {
            $table->dropColumn(['encrypted_penalty_amount', 'encrypted_compensation_amount']);
        });

        Schema::table('escrow_payments', function (Blueprint $table) {
            $table->dropColumn(['encrypted_vendor_amount', 'encrypted_admin_fee', 'encrypted_amount']);
        });

        Schema::table('admin_fee_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'encrypted_admin_receives',
                'encrypted_vendor_receives',
                'encrypted_total_amount',
                'encrypted_admin_fee_amount',
                'encrypted_auction_amount'
            ]);
        });

        Schema::table('financial_audit_logs', function (Blueprint $table) {
            $table->dropColumn([
                'encrypted_user_agent',
                'encrypted_ip_address',
                'encrypted_new_data',
                'encrypted_old_data'
            ]);
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'encrypted_business_license',
                'encrypted_tax_number',
                'encrypted_bank_account_name',
                'encrypted_bank_name',
                'encrypted_bank_account_number',
                'encrypted_address',
                'encrypted_phone'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['encrypted_bank_account', 'encrypted_address', 'encrypted_phone']);
        });

        Schema::table('xendit_payments', function (Blueprint $table) {
            $table->dropColumn([
                'encrypted_account_number',
                'encrypted_items',
                'encrypted_customer',
                'encrypted_amount'
            ]);
        });

        Schema::table('vendor_wallets', function (Blueprint $table) {
            $table->dropColumn([
                'encrypted_pending_amount',
                'encrypted_frozen_amount',
                'encrypted_balance'
            ]);
        });

        Schema::table('vendor_withdrawals', function (Blueprint $table) {
            $table->dropColumn([
                'encrypted_net_amount',
                'encrypted_amount',
                'encrypted_bank_name',
                'encrypted_account_name',
                'encrypted_account_number'
            ]);
        });
    }
};

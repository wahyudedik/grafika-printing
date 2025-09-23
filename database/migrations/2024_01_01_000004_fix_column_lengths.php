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
        echo "Fixing column lengths to prevent data truncation...\n";

        // Fix vendor_wallet_transactions table
        if (Schema::hasTable('vendor_wallet_transactions')) {
            Schema::table('vendor_wallet_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_wallet_transactions', 'category')) {
                    $table->string('category', 50)->change();
                }
            });
        }

        // Fix financial_audit_logs table
        if (Schema::hasTable('financial_audit_logs')) {
            Schema::table('financial_audit_logs', function (Blueprint $table) {
                if (Schema::hasColumn('financial_audit_logs', 'action_type')) {
                    $table->string('action_type', 50)->change();
                }
                if (Schema::hasColumn('financial_audit_logs', 'entity_type')) {
                    $table->string('entity_type', 50)->change();
                }
            });
        }

        // Fix vendor_withdrawals table
        if (Schema::hasTable('vendor_withdrawals')) {
            Schema::table('vendor_withdrawals', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_withdrawals', 'account_number')) {
                    $table->text('account_number')->change();
                }
                if (Schema::hasColumn('vendor_withdrawals', 'account_name')) {
                    $table->text('account_name')->change();
                }
                if (Schema::hasColumn('vendor_withdrawals', 'bank_name')) {
                    $table->text('bank_name')->change();
                }
            });
        }

        echo "Column lengths fixed successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert column lengths
        if (Schema::hasTable('vendor_wallet_transactions')) {
            Schema::table('vendor_wallet_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_wallet_transactions', 'category')) {
                    $table->string('category', 20)->change();
                }
            });
        }

        if (Schema::hasTable('financial_audit_logs')) {
            Schema::table('financial_audit_logs', function (Blueprint $table) {
                if (Schema::hasColumn('financial_audit_logs', 'action_type')) {
                    $table->string('action_type', 20)->change();
                }
                if (Schema::hasColumn('financial_audit_logs', 'entity_type')) {
                    $table->string('entity_type', 20)->change();
                }
            });
        }

        if (Schema::hasTable('vendor_withdrawals')) {
            Schema::table('vendor_withdrawals', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_withdrawals', 'account_number')) {
                    $table->string('account_number', 50)->change();
                }
                if (Schema::hasColumn('vendor_withdrawals', 'account_name')) {
                    $table->string('account_name', 100)->change();
                }
                if (Schema::hasColumn('vendor_withdrawals', 'bank_name')) {
                    $table->string('bank_name', 50)->change();
                }
            });
        }
    }
};

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
        echo "Fixing admin fee transaction status column length...\n";

        // Fix admin_fee_transactions table
        if (Schema::hasTable('admin_fee_transactions')) {
            Schema::table('admin_fee_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('admin_fee_transactions', 'status')) {
                    $table->string('status', 50)->change();
                }
            });
        }

        echo "Admin fee transaction status column length fixed successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status column length
        if (Schema::hasTable('admin_fee_transactions')) {
            Schema::table('admin_fee_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('admin_fee_transactions', 'status')) {
                    $table->string('status', 20)->change();
                }
            });
        }
    }
};

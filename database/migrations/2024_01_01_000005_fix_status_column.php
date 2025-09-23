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
        echo "Fixing status column length...\n";

        // Fix financial_audit_logs table
        if (Schema::hasTable('financial_audit_logs')) {
            Schema::table('financial_audit_logs', function (Blueprint $table) {
                if (Schema::hasColumn('financial_audit_logs', 'status')) {
                    $table->string('status', 50)->change();
                }
            });
        }

        echo "Status column length fixed successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status column length
        if (Schema::hasTable('financial_audit_logs')) {
            Schema::table('financial_audit_logs', function (Blueprint $table) {
                if (Schema::hasColumn('financial_audit_logs', 'status')) {
                    $table->string('status', 20)->change();
                }
            });
        }
    }
};

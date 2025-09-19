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
        // This migration is no longer needed as the shipping payment fields
        // are already defined in the earlier migration: 2025_09_17_133734_add_tracking_fields_to_transaksis_table.php
        // The fields were added in that migration to avoid duplicate column errors.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is no longer needed as the shipping payment fields
        // are already defined in the earlier migration: 2025_09_17_133734_add_tracking_fields_to_transaksis_table.php
        // No rollback needed since no changes were made in this migration.
    }
};

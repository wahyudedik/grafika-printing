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
        Schema::table('xendit_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('xendit_payments', 'transaksi_id')) {
                $table->foreignId('transaksi_id')->nullable()->constrained('transaksis')->nullOnDelete()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xendit_payments', function (Blueprint $table) {
            if (Schema::hasColumn('xendit_payments', 'transaksi_id')) {
                $table->dropForeign(['transaksi_id']);
                $table->dropIndex(['transaksi_id']);
                $table->dropColumn('transaksi_id');
            }
        });
    }
};

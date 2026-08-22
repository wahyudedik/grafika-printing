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
        Schema::table('transaksi_items', function (Blueprint $table) {
            $table->decimal('hpp_satuan', 15, 2)->default(0)->after('harga_satuan');
            $table->decimal('hpp_total', 15, 2)->default(0)->after('hpp_satuan');
            $table->decimal('laba', 15, 2)->default(0)->after('hpp_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_items', function (Blueprint $table) {
            $table->dropColumn(['hpp_satuan', 'hpp_total', 'laba']);
        });
    }
};

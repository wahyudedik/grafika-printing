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
        Schema::table('transaksi_item_specifications', function (Blueprint $table) {
            $table->decimal('hpp_price', 15, 2)->default(0)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_item_specifications', function (Blueprint $table) {
            $table->dropColumn('hpp_price');
        });
    }
};

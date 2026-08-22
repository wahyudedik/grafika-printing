<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->decimal('diskon_total', 15, 2)->default(0)->after('total_harga');
            $table->decimal('total_sebelum_diskon', 15, 2)->nullable()->after('diskon_total');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['diskon_total', 'total_sebelum_diskon']);
        });
    }
};

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
        Schema::table('transaksis', function (Blueprint $table) {
            // Tracking status untuk lelang
            $table->enum('tracking_status', ['menunggu', 'diproses', 'dicetak', 'dikirim', 'selesai'])->default('menunggu')->after('status');

            // COD dan ongkir
            $table->boolean('is_cod')->default(false)->after('tracking_status');
            $table->decimal('ongkir', 10, 2)->default(0)->after('is_cod');
            $table->string('kurir')->nullable()->after('ongkir');
            $table->string('no_resi')->nullable()->after('kurir');
            $table->text('alamat_pengiriman')->nullable()->after('no_resi');

            // Timestamps untuk tracking
            $table->timestamp('diproses_at')->nullable()->after('alamat_pengiriman');
            $table->timestamp('dicetak_at')->nullable()->after('diproses_at');
            $table->timestamp('dikirim_at')->nullable()->after('dicetak_at');
            $table->timestamp('selesai_at')->nullable()->after('dikirim_at');

            // Index untuk performa
            $table->index('tracking_status');
            $table->index('is_cod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex(['tracking_status']);
            $table->dropIndex(['is_cod']);

            $table->dropColumn([
                'tracking_status',
                'is_cod',
                'ongkir',
                'kurir',
                'no_resi',
                'alamat_pengiriman',
                'diproses_at',
                'dicetak_at',
                'dikirim_at',
                'selesai_at'
            ]);
        });
    }
};

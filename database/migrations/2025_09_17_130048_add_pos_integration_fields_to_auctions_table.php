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
        Schema::table('auctions', function (Blueprint $table) {
            // Kolom untuk integrasi dengan POS
            $table->string('kode')->nullable()->after('id'); // Kode lelang untuk referensi
            $table->text('alamat_pengiriman')->nullable()->after('specifications'); // Alamat pengiriman dari user
            $table->string('no_telp')->nullable()->after('alamat_pengiriman'); // No telp user
            $table->string('email_pengiriman')->nullable()->after('no_telp'); // Email untuk notifikasi
            $table->text('catatan_khusus')->nullable()->after('email_pengiriman'); // Catatan khusus dari user
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'qris', 'auction_win'])->default('auction_win')->after('catatan_khusus');
            $table->timestamp('estimasi_selesai')->nullable()->after('metode_pembayaran'); // Estimasi selesai dari vendor
            $table->integer('progress_percentage')->default(0)->after('estimasi_selesai'); // Progress lelang
            $table->text('catatan_vendor')->nullable()->after('progress_percentage'); // Catatan dari vendor pemenang
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksis')->after('catatan_vendor'); // Link ke transaksi POS
            $table->boolean('pos_integrated')->default(false)->after('transaksi_id'); // Flag apakah sudah terintegrasi ke POS

            // Index untuk performa
            $table->index('kode');
            $table->index('pos_integrated');
            $table->index('transaksi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropIndex(['kode']);
            $table->dropIndex(['pos_integrated']);
            $table->dropIndex(['transaksi_id']);

            $table->dropColumn([
                'kode',
                'alamat_pengiriman',
                'no_telp',
                'email_pengiriman',
                'catatan_khusus',
                'metode_pembayaran',
                'estimasi_selesai',
                'progress_percentage',
                'catatan_vendor',
                'transaksi_id',
                'pos_integrated'
            ]);
        });
    }
};

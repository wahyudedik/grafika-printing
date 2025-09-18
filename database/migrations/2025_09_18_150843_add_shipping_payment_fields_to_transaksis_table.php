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
            // Shipping payment fields
            $table->string('shipping_payment_link')->nullable()->after('alamat_pengiriman');
            $table->string('shipping_payment_id')->nullable()->after('shipping_payment_link');
            $table->enum('shipping_payment_status', ['pending', 'paid_cash', 'paid_app', 'expired'])->default('pending')->after('shipping_payment_id');
            $table->timestamp('shipping_payment_date')->nullable()->after('shipping_payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_payment_link',
                'shipping_payment_id',
                'shipping_payment_status',
                'shipping_payment_date'
            ]);
        });
    }
};

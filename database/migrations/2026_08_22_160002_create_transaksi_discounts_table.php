<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->foreignId('transaksi_id')->constrained('transaksis');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons');
            $table->string('discount_code')->nullable();
            $table->string('discount_type'); // 'coupon', 'manual'
            $table->decimal('discount_amount', 15, 2);
            $table->string('description')->nullable();
            $table->foreignId('applied_by_user_id')->constrained('users');
            $table->timestamps();

            $table->index('transaksi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_discounts');
    }
};

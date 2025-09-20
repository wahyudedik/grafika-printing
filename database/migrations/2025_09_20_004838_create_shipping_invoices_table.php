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
        Schema::create('shipping_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('auction_id')->constrained('auctions');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->string('courier');
            $table->string('service');
            $table->string('waybill_number')->nullable();
            $table->decimal('weight', 8, 2);
            $table->decimal('shipping_cost', 10, 2);
            $table->string('origin_city');
            $table->string('destination_city');
            $table->text('origin_address');
            $table->text('destination_address');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->enum('shipping_status', ['pending', 'processing', 'shipped', 'delivered', 'failed'])->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('tracking_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_invoices');
    }
};

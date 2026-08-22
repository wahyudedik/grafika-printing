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
        Schema::create('transaction_void_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->foreignId('transaksi_id')->constrained('transaksis');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('action', ['void', 'refund', 'restock']);
            $table->text('reason');
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->boolean('stock_restored')->default(false);
            $table->boolean('refund_processed')->default(false);
            $table->timestamps();

            $table->index(['vendor_id', 'transaksi_id']);
            $table->index(['transaksi_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_void_logs');
    }
};

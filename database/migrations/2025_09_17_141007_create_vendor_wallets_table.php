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
        Schema::create('vendor_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0); // Saldo wallet
            $table->decimal('pending_balance', 15, 2)->default(0); // Saldo pending (belum dikonfirmasi)
            $table->decimal('total_earned', 15, 2)->default(0); // Total pendapatan
            $table->decimal('total_withdrawn', 15, 2)->default(0); // Total yang sudah ditarik
            $table->boolean('is_active')->default(true); // Status wallet
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_wallets');
    }
};

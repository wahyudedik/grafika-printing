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
        Schema::create('admin_fee_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('auctions')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_code')->unique(); // Kode transaksi
            $table->decimal('auction_amount', 15, 2); // Jumlah lelang asli
            $table->decimal('admin_fee_amount', 15, 2); // Jumlah biaya admin
            $table->decimal('payment_gateway_fee', 15, 2)->default(0); // Biaya payment gateway
            $table->decimal('total_amount', 15, 2); // Total yang dibayar user
            $table->decimal('vendor_receives', 15, 2); // Yang diterima vendor
            $table->decimal('admin_receives', 15, 2); // Yang diterima admin
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // Metode pembayaran
            $table->string('payment_reference')->nullable(); // Referensi pembayaran
            $table->timestamp('paid_at')->nullable(); // Waktu dibayar
            $table->json('fee_breakdown')->nullable(); // Detail breakdown biaya
            $table->text('notes')->nullable(); // Catatan
            $table->timestamps();

            $table->index(['auction_id', 'vendor_id']);
            $table->index(['status', 'created_at']);
            $table->index('transaction_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_fee_transactions');
    }
};

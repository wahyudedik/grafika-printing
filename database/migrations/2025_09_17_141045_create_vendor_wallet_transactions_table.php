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
        Schema::create('vendor_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_wallet_id')->constrained('vendor_wallets')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('transaction_code')->unique(); // Kode transaksi unik
            $table->enum('type', ['credit', 'debit']); // Jenis transaksi
            $table->enum('category', [
                'auction_payment', // Pembayaran dari lelang
                'withdrawal', // Penarikan dana
                'refund', // Pengembalian dana
                'bonus', // Bonus dari admin
                'adjustment' // Penyesuaian saldo
            ]);
            $table->decimal('amount', 15, 2); // Jumlah transaksi
            $table->decimal('balance_before', 15, 2); // Saldo sebelum transaksi
            $table->decimal('balance_after', 15, 2); // Saldo setelah transaksi
            $table->text('description')->nullable(); // Deskripsi transaksi
            $table->string('reference_id')->nullable(); // ID referensi (auction_id, transaksi_id, dll)
            $table->string('reference_type')->nullable(); // Tipe referensi (auction, transaction, dll)
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->json('metadata')->nullable(); // Data tambahan (payment info, dll)
            $table->timestamps();

            $table->index(['vendor_id', 'type']);
            $table->index(['vendor_id', 'category']);
            $table->index(['vendor_id', 'status']);
            $table->index('transaction_code');
            $table->index(['reference_id', 'reference_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_wallet_transactions');
    }
};

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
        Schema::create('vendor_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('vendor_wallet_id')->constrained('vendor_wallets')->onDelete('cascade');
            $table->string('withdrawal_code')->unique(); // Kode penarikan
            $table->decimal('amount', 15, 2); // Jumlah yang ditarik
            $table->decimal('fee', 15, 2)->default(0); // Biaya penarikan
            $table->decimal('net_amount', 15, 2); // Jumlah bersih yang diterima
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'completed', 'failed'])->default('pending');
            $table->enum('method', ['bank_transfer', 'e_wallet', 'cash']); // Metode penarikan
            $table->string('account_number')->nullable(); // Nomor rekening/e-wallet
            $table->string('account_name')->nullable(); // Nama pemilik rekening
            $table->string('bank_name')->nullable(); // Nama bank
            $table->text('notes')->nullable(); // Catatan
            $table->text('admin_notes')->nullable(); // Catatan admin
            $table->foreignId('processed_by')->nullable()->constrained('users'); // Admin yang memproses
            $table->timestamp('processed_at')->nullable(); // Waktu diproses
            $table->timestamp('completed_at')->nullable(); // Waktu selesai
            $table->json('payment_proof')->nullable(); // Bukti pembayaran
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('withdrawal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_withdrawals');
    }
};

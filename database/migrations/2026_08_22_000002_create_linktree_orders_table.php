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
        Schema::create('linktree_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('linktree_id')->constrained('linktrees')->onDelete('cascade');
            $table->foreignId('linktree_product_id')->constrained('linktree_products')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Order details
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->json('selected_specs')->nullable(); // Pilihan spesifikasi: [{"spesifikasi_id": 1, "nama": "Ukuran", "value": "A4"}, ...]
            $table->text('notes')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 15, 2)->nullable();

            // Status
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'proof_sent', 'confirmed', 'rejected'])->default('unpaid');
            $table->text('payment_proof')->nullable(); // Path bukti pembayaran
            $table->text('vendor_notes')->nullable(); // Catatan vendor

            // WhatsApp
            $table->text('whatsapp_message')->nullable(); // Pesan WhatsApp yang di-generate
            $table->boolean('whatsapp_sent')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('vendor_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linktree_orders');
    }
};

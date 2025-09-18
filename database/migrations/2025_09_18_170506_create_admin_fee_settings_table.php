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
        Schema::create('admin_fee_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nama pengaturan
            $table->string('description')->nullable(); // Deskripsi
            $table->enum('type', ['fixed', 'percentage']); // Jenis biaya: fixed atau percentage
            $table->decimal('value', 15, 2); // Nilai biaya (fixed amount atau percentage)
            $table->decimal('minimum_amount', 15, 2)->default(0); // Minimum amount untuk percentage
            $table->decimal('maximum_amount', 15, 2)->nullable(); // Maximum amount untuk percentage
            $table->boolean('is_active')->default(true); // Status aktif
            $table->string('category')->default('auction'); // Kategori: auction, payment, etc.
            $table->json('conditions')->nullable(); // Kondisi khusus (JSON)
            $table->timestamp('effective_from')->nullable(); // Berlaku dari tanggal
            $table->timestamp('effective_until')->nullable(); // Berlaku sampai tanggal
            $table->foreignId('created_by')->constrained('users'); // Admin yang membuat
            $table->foreignId('updated_by')->nullable()->constrained('users'); // Admin yang update
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['effective_from', 'effective_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_fee_settings');
    }
};

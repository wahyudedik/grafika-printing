<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linktree_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linktree_id')->constrained('linktrees')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('custom_price', 50)->nullable(); // Harga khusus untuk linktree (opsional)
            $table->text('custom_description')->nullable(); // Deskripsi khusus untuk linktree (opsional)
            $table->timestamps();

            // Unique constraint: satu produk hanya bisa sekali per linktree
            $table->unique(['linktree_id', 'produk_id']);

            // Indexes untuk performa
            $table->index('linktree_id');
            $table->index('produk_id');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linktree_products');
    }
};

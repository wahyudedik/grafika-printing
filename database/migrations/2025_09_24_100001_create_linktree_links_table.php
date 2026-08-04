<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linktree_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('linktree_id')->constrained('linktrees')->onDelete('cascade');
            $table->string('title', 100);
            $table->string('url', 500);
            $table->string('icon', 100)->nullable(); // icon class or emoji
            $table->string('type', 50)->default('link'); // link, qris, whatsapp, phone, email
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('linktree_id');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linktree_links');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linktree_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('linktree_id')->constrained('linktrees')->onDelete('cascade');
            $table->string('platform', 50); // instagram, facebook, twitter, tiktok, youtube, whatsapp
            $table->string('url', 500);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('linktree_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linktree_socials');
    }
};

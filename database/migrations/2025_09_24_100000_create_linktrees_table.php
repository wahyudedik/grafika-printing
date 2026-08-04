<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linktrees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('title', 100)->default('My Links');
            $table->string('custom_url', 50)->unique();
            $table->string('bio', 255)->nullable();
            $table->string('avatar', 500)->nullable();
            $table->string('banner', 500)->nullable();
            $table->string('template', 50)->default('minimal'); // minimal, colorful, dark, professional
            $table->string('primary_color', 7)->default('#6366f1');
            $table->string('secondary_color', 7)->default('#ec4899');
            $table->string('bg_color', 7)->default('#ffffff');
            $table->string('text_color', 7)->default('#1f2937');
            $table->string('button_style', 50)->default('rounded'); // rounded, square, pill
            $table->boolean('is_active')->default(true);
            $table->boolean('show_qris')->default(false);
            $table->string('qris_image', 500)->nullable();
            $table->string('meta_title', 100)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->timestamps();

            $table->index('custom_url');
            $table->index('vendor_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linktrees');
    }
};

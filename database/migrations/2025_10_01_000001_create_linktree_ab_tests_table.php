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
        Schema::create('linktree_ab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linktree_id')->constrained('linktrees')->cascadeOnDelete();
            $table->string('name'); // Nama test, misal: "Template Color Test"
            $table->string('variant_a'); // Template variant A (e.g., 'minimal')
            $table->string('variant_b'); // Template variant B (e.g., 'colorful')
            $table->json('variant_a_config')->nullable(); // Custom config untuk variant A
            $table->json('variant_b_config')->nullable(); // Custom config untuk variant B
            $table->string('status')->default('draft'); // draft, running, paused, completed
            $table->integer('traffic_split')->default(50); // Persentase traffic ke variant A (0-100)
            $table->integer('min_samples')->default(100); // Minimum impressions sebelum evaluasi
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('winner')->nullable(); // 'variant_a', 'variant_b', atau null
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('linktree_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linktree_ab_tests');
    }
};

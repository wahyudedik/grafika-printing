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
        Schema::create('linktree_ab_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ab_test_id')->constrained('linktree_ab_tests')->cascadeOnDelete();
            $table->string('variant'); // 'variant_a' atau 'variant_b'
            $table->string('visitor_id', 64); // Unique visitor identifier (cookie-based)
            $table->boolean('is_click')->default(false);
            $table->timestamp('shown_at');
            $table->timestamps();

            $table->index('ab_test_id');
            $table->index('variant');
            $table->index('visitor_id');
            $table->index(['ab_test_id', 'variant']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linktree_ab_test_results');
    }
};

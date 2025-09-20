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
        Schema::create('delivery_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->enum('delivery_status', ['pending', 'delivered', 'confirmed', 'disputed', 'resolved'])->default('pending');
            $table->timestamp('delivery_date')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->integer('user_rating')->nullable(); // 1-5 stars
            $table->text('user_feedback')->nullable();
            $table->json('photos')->nullable(); // Array of photo URLs
            $table->timestamp('confirmed_at')->nullable();
            $table->text('dispute_reason')->nullable();
            $table->timestamp('dispute_resolved_at')->nullable();
            $table->timestamps();

            $table->index(['auction_id', 'delivery_status']);
            $table->index(['vendor_id', 'delivery_status']);
            $table->index(['user_id', 'delivery_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_confirmations');
    }
};

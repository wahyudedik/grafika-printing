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
        Schema::create('vendor_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('auction_id')->constrained('auctions')->onDelete('cascade');
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksis')->onDelete('cascade');
            $table->integer('rating')->unsigned(); // 1-5 stars
            $table->text('comment')->nullable();
            $table->json('rating_details')->nullable(); // For detailed ratings (quality, speed, service, etc.)
            $table->boolean('is_verified')->default(false); // Verified by admin
            $table->timestamps();

            // Ensure one rating per user per auction
            $table->unique(['user_id', 'auction_id']);
            $table->index(['vendor_id', 'rating']);
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_ratings');
    }
};

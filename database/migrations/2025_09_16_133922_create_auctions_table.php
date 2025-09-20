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
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->integer('quantity');
            $table->decimal('budget', 15, 2);
            $table->date('deadline');
            $table->string('file_path')->nullable();
            $table->string('status')->default('pending'); // pending, active, closed, completed, rejected
            $table->foreignId('winner_vendor_id')->nullable()->constrained('vendors');
            $table->decimal('winning_bid', 15, 2)->nullable();
            $table->text('specifications')->nullable();
            $table->timestamps();

            $table->index(['status', 'deadline']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};

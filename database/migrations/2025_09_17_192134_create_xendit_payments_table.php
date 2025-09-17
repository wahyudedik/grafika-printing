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
        Schema::create('xendit_payments', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('xendit_id')->nullable(); // Payment link ID or XenPayment ID
            $table->string('type')->default('payment_link'); // payment_link or xenpayment
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, paid, expired, failed
            $table->string('payment_method')->nullable();
            $table->json('customer')->nullable();
            $table->json('items')->nullable();
            $table->json('fees')->nullable();
            $table->string('checkout_url')->nullable();
            $table->string('success_redirect_url')->nullable();
            $table->string('failure_redirect_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('webhook_data')->nullable();
            $table->timestamps();

            $table->index(['external_id', 'status']);
            $table->index('xendit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xendit_payments');
    }
};

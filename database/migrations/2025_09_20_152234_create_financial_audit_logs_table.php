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
        Schema::create('financial_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('action_type', [
                'create',
                'update',
                'delete',
                'approve',
                'reject',
                'withdraw',
                'deposit',
                'transfer'
            ]);
            $table->enum('entity_type', [
                'withdrawal',
                'wallet',
                'payment',
                'auction',
                'admin_fee'
            ]);
            $table->unsignedBigInteger('entity_id');
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index(['vendor_id', 'created_at']);
            $table->index(['action_type', 'entity_type']);
            $table->index(['risk_level', 'created_at']);
            $table->index('transaction_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_audit_logs');
    }
};

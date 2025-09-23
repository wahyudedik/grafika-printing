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
        Schema::create('mediation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->string('reason');
            $table->text('description');
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('evidence_files')->nullable();
            $table->string('admin_decision')->nullable();
            $table->decimal('compensation_amount', 15, 2)->default(0);
            $table->decimal('penalty_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['auction_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['requested_by', 'status']);
            $table->index('status');
            $table->index('admin_decision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mediation_requests');
    }
};

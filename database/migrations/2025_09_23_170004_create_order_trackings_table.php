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
        Schema::create('order_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('payment_received');
            $table->text('status_description')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamp('estimated_delivery')->nullable();
            $table->timestamp('actual_delivery')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('is_mediation_requested')->default(false);
            $table->string('mediation_reason')->nullable();
            $table->string('mediation_status')->nullable();
            $table->text('mediation_resolution')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['auction_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('is_mediation_requested');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_trackings');
    }
};

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
        Schema::table('transaksis', function (Blueprint $table) {
            // Void fields
            $table->boolean('is_voided')->default(false)->after('status');
            $table->enum('void_status', ['none', 'voided', 'refund_pending', 'refunded'])
                ->default('none')->after('is_voided');
            $table->text('void_reason')->nullable()->after('void_status');
            $table->string('voided_by')->nullable()->after('void_reason');
            $table->timestamp('voided_at')->nullable()->after('voided_by');
            $table->decimal('refund_amount', 15, 2)->nullable()->after('voided_at');
            $table->string('refund_id')->nullable()->after('refund_amount');

            $table->index('void_status');
            $table->index('is_voided');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex(['void_status']);
            $table->dropIndex(['is_voided']);
            $table->dropColumn([
                'is_voided',
                'void_status',
                'void_reason',
                'voided_by',
                'voided_at',
                'refund_amount',
                'refund_id',
            ]);
        });
    }
};

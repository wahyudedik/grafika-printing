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
        Schema::table('auctions', function (Blueprint $table) {
            $table->decimal('admin_fee_amount', 15, 2)->default(0)->after('budget');
            $table->decimal('payment_gateway_fee', 15, 2)->default(0)->after('admin_fee_amount');
            $table->decimal('total_amount_with_fees', 15, 2)->nullable()->after('payment_gateway_fee');
            $table->decimal('vendor_receives', 15, 2)->nullable()->after('total_amount_with_fees');
            $table->decimal('admin_receives', 15, 2)->nullable()->after('vendor_receives');
            $table->json('fee_breakdown')->nullable()->after('admin_receives');
            $table->boolean('fees_calculated')->default(false)->after('fee_breakdown');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn([
                'admin_fee_amount',
                'payment_gateway_fee',
                'total_amount_with_fees',
                'vendor_receives',
                'admin_receives',
                'fee_breakdown',
                'fees_calculated'
            ]);
        });
    }
};

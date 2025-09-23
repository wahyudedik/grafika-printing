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
            // Check if columns exist before adding
            if (!Schema::hasColumn('transaksis', 'payment_amount')) {
                $table->decimal('payment_amount', 15, 2)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('transaksis', 'change_amount')) {
                $table->decimal('change_amount', 15, 2)->nullable()->after('payment_amount');
            }
            if (!Schema::hasColumn('transaksis', 'admin_fee')) {
                $table->decimal('admin_fee', 15, 2)->nullable()->after('change_amount');
            }
            if (!Schema::hasColumn('transaksis', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('admin_fee');
            }
            if (!Schema::hasColumn('transaksis', 'xendit_payment_id')) {
                $table->string('xendit_payment_id')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('transaksis', 'xendit_external_id')) {
                $table->string('xendit_external_id')->nullable()->after('xendit_payment_id');
            }
            if (!Schema::hasColumn('transaksis', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('xendit_external_id');
            }
            if (!Schema::hasColumn('transaksis', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_email');
            }
            if (!Schema::hasColumn('transaksis', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending')->after('customer_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_amount',
                'change_amount',
                'admin_fee',
                'paid_at',
                'xendit_payment_id',
                'xendit_external_id',
                'customer_email',
                'customer_phone',
                'payment_status'
            ]);
        });
    }
};

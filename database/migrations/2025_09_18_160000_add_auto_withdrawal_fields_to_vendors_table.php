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
        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean('auto_withdrawal_enabled')->default(false)->after('is_active');
            $table->integer('auto_withdrawal_date')->nullable()->after('auto_withdrawal_enabled'); // Day of month (1-31)
            $table->decimal('auto_withdrawal_amount', 15, 2)->nullable()->after('auto_withdrawal_date');
            $table->enum('auto_withdrawal_method', ['bank_transfer', 'e_wallet', 'cash'])->nullable()->after('auto_withdrawal_amount');
            $table->string('auto_withdrawal_account_number')->nullable()->after('auto_withdrawal_method');
            $table->string('auto_withdrawal_account_name')->nullable()->after('auto_withdrawal_account_number');
            $table->string('auto_withdrawal_bank_name')->nullable()->after('auto_withdrawal_account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'auto_withdrawal_enabled',
                'auto_withdrawal_date',
                'auto_withdrawal_amount',
                'auto_withdrawal_method',
                'auto_withdrawal_account_number',
                'auto_withdrawal_account_name',
                'auto_withdrawal_bank_name'
            ]);
        });
    }
};

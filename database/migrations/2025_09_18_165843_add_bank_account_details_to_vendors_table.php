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
            // Primary Bank Account Details
            $table->string('primary_bank_name')->nullable()->after('auto_withdrawal_bank_name');
            $table->string('primary_account_number')->nullable()->after('primary_bank_name');
            $table->string('primary_account_name')->nullable()->after('primary_account_number');
            $table->string('primary_bank_code')->nullable()->after('primary_account_name'); // Kode bank (BCA, BRI, etc.)

            // Secondary Bank Account Details (Optional)
            $table->string('secondary_bank_name')->nullable()->after('primary_bank_code');
            $table->string('secondary_account_number')->nullable()->after('secondary_bank_name');
            $table->string('secondary_account_name')->nullable()->after('secondary_account_number');
            $table->string('secondary_bank_code')->nullable()->after('secondary_account_name');

            // E-Wallet Details
            $table->string('ewallet_provider')->nullable()->after('secondary_bank_code'); // OVO, DANA, GoPay, etc.
            $table->string('ewallet_number')->nullable()->after('ewallet_provider');
            $table->string('ewallet_name')->nullable()->after('ewallet_number');

            // Additional Details
            $table->text('bank_notes')->nullable()->after('ewallet_name'); // Catatan tambahan
            $table->boolean('bank_verified')->default(false)->after('bank_notes'); // Status verifikasi rekening
            $table->timestamp('bank_verified_at')->nullable()->after('bank_verified'); // Tanggal verifikasi
            $table->string('bank_verified_by')->nullable()->after('bank_verified_at'); // Admin yang verifikasi

            // Indexes for better performance
            $table->index(['primary_bank_name', 'primary_account_number']);
            $table->index(['secondary_bank_name', 'secondary_account_number']);
            $table->index('bank_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Check if indexes exist before dropping
            if (Schema::hasIndex('vendors', ['primary_bank_name', 'primary_account_number'])) {
                $table->dropIndex(['primary_bank_name', 'primary_account_number']);
            }
            if (Schema::hasIndex('vendors', ['secondary_bank_name', 'secondary_account_number'])) {
                $table->dropIndex(['secondary_bank_name', 'secondary_account_number']);
            }
            if (Schema::hasIndex('vendors', 'bank_verified')) {
                $table->dropIndex('bank_verified');
            }

            $table->dropColumn([
                'primary_bank_name',
                'primary_account_number',
                'primary_account_name',
                'primary_bank_code',
                'secondary_bank_name',
                'secondary_account_number',
                'secondary_account_name',
                'secondary_bank_code',
                'ewallet_provider',
                'ewallet_number',
                'ewallet_name',
                'bank_notes',
                'bank_verified',
                'bank_verified_at',
                'bank_verified_by'
            ]);
        });
    }
};

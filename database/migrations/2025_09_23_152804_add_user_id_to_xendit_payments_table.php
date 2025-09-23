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
        Schema::table('xendit_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('xendit_payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('auction_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xendit_payments', function (Blueprint $table) {
            if (Schema::hasColumn('xendit_payments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};

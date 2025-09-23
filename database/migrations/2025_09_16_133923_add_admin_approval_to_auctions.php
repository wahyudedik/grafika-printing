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
            if (!Schema::hasColumn('auctions', 'admin_approval_status')) {
                $table->enum('admin_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            }

            if (!Schema::hasColumn('auctions', 'admin_approval_date')) {
                $table->timestamp('admin_approval_date')->nullable()->after('admin_approval_status');
            }

            if (!Schema::hasColumn('auctions', 'admin_approval_notes')) {
                $table->text('admin_approval_notes')->nullable()->after('admin_approval_date');
            }

            if (!Schema::hasColumn('auctions', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('admin_approval_notes');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('auctions', 'delivery_status')) {
                $table->enum('delivery_status', ['pending', 'shipped', 'delivered', 'completed'])->default('pending')->after('approved_by');
            }

            if (!Schema::hasColumn('auctions', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('delivery_status');
            }

            if (!Schema::hasColumn('auctions', 'shipping_cost')) {
                $table->decimal('shipping_cost', 10, 2)->nullable()->after('tracking_number');
            }

            if (!Schema::hasColumn('auctions', 'user_rating')) {
                $table->integer('user_rating')->nullable()->after('shipping_cost');
            }

            if (!Schema::hasColumn('auctions', 'user_feedback')) {
                $table->text('user_feedback')->nullable()->after('user_rating');
            }

            if (!Schema::hasColumn('auctions', 'completion_date')) {
                $table->timestamp('completion_date')->nullable()->after('user_feedback');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            if (Schema::hasColumn('auctions', 'completion_date')) {
                $table->dropColumn('completion_date');
            }

            if (Schema::hasColumn('auctions', 'user_feedback')) {
                $table->dropColumn('user_feedback');
            }

            if (Schema::hasColumn('auctions', 'user_rating')) {
                $table->dropColumn('user_rating');
            }

            if (Schema::hasColumn('auctions', 'shipping_cost')) {
                $table->dropColumn('shipping_cost');
            }

            if (Schema::hasColumn('auctions', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }

            if (Schema::hasColumn('auctions', 'delivery_status')) {
                $table->dropColumn('delivery_status');
            }

            if (Schema::hasColumn('auctions', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }

            if (Schema::hasColumn('auctions', 'admin_approval_notes')) {
                $table->dropColumn('admin_approval_notes');
            }

            if (Schema::hasColumn('auctions', 'admin_approval_date')) {
                $table->dropColumn('admin_approval_date');
            }

            if (Schema::hasColumn('auctions', 'admin_approval_status')) {
                $table->dropColumn('admin_approval_status');
            }
        });
    }
};

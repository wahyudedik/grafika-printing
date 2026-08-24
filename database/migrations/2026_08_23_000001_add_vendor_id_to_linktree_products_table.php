<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('linktree_products', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('id')->constrained('vendors')->onDelete('cascade');
            $table->index('vendor_id');
        });

        // Backfill vendor_id from linktrees table (linktrees has vendor_id)
        DB::statement('
            UPDATE linktree_products lp
            JOIN linktrees l ON lp.linktree_id = l.id
            SET lp.vendor_id = l.vendor_id
            WHERE lp.vendor_id IS NULL
        ');

        // Now make vendor_id NOT NULL after backfill
        Schema::table('linktree_products', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('linktree_products', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropIndex(['vendor_id']);
            $table->dropColumn('vendor_id');
        });
    }
};

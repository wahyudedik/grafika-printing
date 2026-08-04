<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printer_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('paper_width', 10)->default('80mm'); // 58mm or 80mm
            $table->integer('font_size')->default(12); // px
            $table->string('margin', 10)->default('0mm');
            $table->boolean('auto_print')->default(true);
            $table->boolean('auto_cut')->default(true);
            $table->boolean('auto_close_window')->default(true);
            $table->integer('print_delay')->default(500); // ms delay before printing
            $table->string('printer_name')->nullable(); // optional printer name
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('vendor_id');
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printer_settings');
    }
};

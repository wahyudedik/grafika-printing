<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_configs', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->index(); // xendit, rajaongkir, etc
            $table->string('key'); // api_key, webhook_token, etc
            $table->text('value')->nullable(); // encrypted value
            $table->string('label'); // Display label
            $table->text('description')->nullable(); // Help text
            $table->boolean('is_active')->default(true);
            $table->boolean('is_encrypted')->default(false); // Whether value should be encrypted
            $table->boolean('is_masked')->default(true); // Whether to mask in UI (show ****)
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['service_name', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_configs');
    }
};

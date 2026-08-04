<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel terpisah untuk profil User Lelang.
     * Tidak memodifikasi tabel users yang sudah ada (production constraint).
     */
    public function up(): void
    {
        Schema::create('lelang_user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name')->nullable()->after('user_id');
            $table->string('phone_number')->nullable()->after('company_name');
            $table->text('address')->nullable()->after('phone_number');
            $table->string('city')->nullable()->after('address');
            $table->string('province')->nullable()->after('city');
            $table->string('postal_code', 10)->nullable()->after('province');
            $table->enum('status', ['active', 'suspended', 'pending'])->default('active');
            $table->text('notes')->nullable()->after('status');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->integer('total_auctions')->default(0);
            $table->integer('total_won')->default(0);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->timestamps();

            // Indexes
            $table->unique('user_id');
            $table->index('status');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lelang_user_profiles');
    }
};

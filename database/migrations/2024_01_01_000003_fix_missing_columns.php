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
        $this->info('Adding missing columns to fix database schema...');

        // Add missing columns to users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 20)->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'address')) {
                    $table->text('address')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('users', 'avatar')) {
                    $table->string('avatar')->nullable()->after('address');
                }
            });
        }

        // Add missing columns to vendors table
        if (Schema::hasTable('vendors')) {
            Schema::table('vendors', function (Blueprint $table) {
                if (!Schema::hasColumn('vendors', 'bank_account_number')) {
                    $table->string('bank_account_number', 50)->nullable()->after('phone');
                }
                if (!Schema::hasColumn('vendors', 'bank_name')) {
                    $table->string('bank_name', 100)->nullable()->after('bank_account_number');
                }
                if (!Schema::hasColumn('vendors', 'bank_account_name')) {
                    $table->string('bank_account_name', 100)->nullable()->after('bank_name');
                }
                if (!Schema::hasColumn('vendors', 'auto_withdrawal_enabled')) {
                    $table->boolean('auto_withdrawal_enabled')->default(false)->after('bank_account_name');
                }
                if (!Schema::hasColumn('vendors', 'minimum_withdrawal')) {
                    $table->decimal('minimum_withdrawal', 15, 2)->default(50000)->after('auto_withdrawal_enabled');
                }
            });
        }

        // Add missing columns to vendor_wallets table
        if (Schema::hasTable('vendor_wallets')) {
            Schema::table('vendor_wallets', function (Blueprint $table) {
                if (!Schema::hasColumn('vendor_wallets', 'is_frozen')) {
                    $table->boolean('is_frozen')->default(false)->after('balance');
                }
                if (!Schema::hasColumn('vendor_wallets', 'frozen_reason')) {
                    $table->text('frozen_reason')->nullable()->after('is_frozen');
                }
                if (!Schema::hasColumn('vendor_wallets', 'frozen_at')) {
                    $table->timestamp('frozen_at')->nullable()->after('frozen_reason');
                }
            });
        }

        // Add missing columns to produks table
        if (Schema::hasTable('produks')) {
            Schema::table('produks', function (Blueprint $table) {
                if (!Schema::hasColumn('produks', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('updated_at');
                }
                if (!Schema::hasColumn('produks', 'stock')) {
                    $table->integer('stock')->default(0)->after('is_active');
                }
                if (!Schema::hasColumn('produks', 'min_order')) {
                    $table->integer('min_order')->default(1)->after('stock');
                }
            });
        }

        // Add missing columns to bahans table
        if (Schema::hasTable('bahans')) {
            Schema::table('bahans', function (Blueprint $table) {
                if (!Schema::hasColumn('bahans', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('updated_at');
                }
                if (!Schema::hasColumn('bahans', 'unit')) {
                    $table->string('unit', 20)->default('pcs')->after('is_active');
                }
            });
        }

        // Add missing columns to alats table
        if (Schema::hasTable('alats')) {
            Schema::table('alats', function (Blueprint $table) {
                if (!Schema::hasColumn('alats', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('status');
                }
                if (!Schema::hasColumn('alats', 'maintenance_schedule')) {
                    $table->date('maintenance_schedule')->nullable()->after('is_active');
                }
            });
        }

        // Add missing columns to kategori_produks table
        if (Schema::hasTable('kategori_produks')) {
            Schema::table('kategori_produks', function (Blueprint $table) {
                if (!Schema::hasColumn('kategori_produks', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('slug');
                }
                if (!Schema::hasColumn('kategori_produks', 'description')) {
                    $table->text('description')->nullable()->after('is_active');
                }
            });
        }

        // Add missing columns to spesifikasis table
        if (Schema::hasTable('spesifikasis')) {
            Schema::table('spesifikasis', function (Blueprint $table) {
                if (!Schema::hasColumn('spesifikasis', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('tipe_input');
                }
                if (!Schema::hasColumn('spesifikasis', 'is_required')) {
                    $table->boolean('is_required')->default(false)->after('is_active');
                }
            });
        }

        $this->info('Missing columns added successfully!');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove added columns
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['phone', 'address', 'avatar']);
            });
        }

        if (Schema::hasTable('vendors')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->dropColumn(['bank_account_number', 'bank_name', 'bank_account_name', 'auto_withdrawal_enabled', 'minimum_withdrawal']);
            });
        }

        if (Schema::hasTable('vendor_wallets')) {
            Schema::table('vendor_wallets', function (Blueprint $table) {
                $table->dropColumn(['is_frozen', 'frozen_reason', 'frozen_at']);
            });
        }

        if (Schema::hasTable('produks')) {
            Schema::table('produks', function (Blueprint $table) {
                $table->dropColumn(['is_active', 'stock', 'min_order']);
            });
        }

        if (Schema::hasTable('bahans')) {
            Schema::table('bahans', function (Blueprint $table) {
                $table->dropColumn(['is_active', 'unit']);
            });
        }

        if (Schema::hasTable('alats')) {
            Schema::table('alats', function (Blueprint $table) {
                $table->dropColumn(['is_active', 'maintenance_schedule']);
            });
        }

        if (Schema::hasTable('kategori_produks')) {
            Schema::table('kategori_produks', function (Blueprint $table) {
                $table->dropColumn(['is_active', 'description']);
            });
        }

        if (Schema::hasTable('spesifikasis')) {
            Schema::table('spesifikasis', function (Blueprint $table) {
                $table->dropColumn(['is_active', 'is_required']);
            });
        }
    }

    private function info($message)
    {
        echo $message . "\n";
    }
};

<?php

namespace Database\Seeders;

use App\Models\AdminFeeSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminFeeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding Admin Fee Settings...');

        $admin = User::where('usertype', 'dev')->first();
        if (!$admin) {
            $this->command->warn('⚠️ No admin user found. Skipping admin fee settings.');
            return;
        }

        $settings = [
            [
                'name' => 'auction_admin_fee',
                'description' => 'Biaya admin untuk transaksi lelang',
                'type' => 'percentage',
                'value' => 5.00,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'is_active' => true,
                'category' => 'auction',
                'conditions' => null,
                'effective_from' => now(),
                'effective_until' => null,
                'created_by' => $admin->id,
                'updated_by' => null,
            ],
            [
                'name' => 'auction_flat_fee',
                'description' => 'Biaya tetap admin untuk lelang kecil',
                'type' => 'fixed',
                'value' => 10000.00,
                'minimum_amount' => 0,
                'maximum_amount' => 100000,
                'is_active' => true,
                'category' => 'auction',
                'conditions' => json_encode(['max_budget' => 100000]),
                'effective_from' => now(),
                'effective_until' => null,
                'created_by' => $admin->id,
                'updated_by' => null,
            ],
            [
                'name' => 'payment_gateway_fee',
                'description' => 'Biaya payment gateway Xendit',
                'type' => 'percentage',
                'value' => 2.50,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'is_active' => true,
                'category' => 'payment',
                'conditions' => json_encode(['provider' => 'xendit']),
                'effective_from' => now(),
                'effective_until' => null,
                'created_by' => $admin->id,
                'updated_by' => null,
            ],
            [
                'name' => 'pos_transaction_fee',
                'description' => 'Biaya admin untuk transaksi POS',
                'type' => 'percentage',
                'value' => 2.00,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'is_active' => true,
                'category' => 'pos_transaction',
                'conditions' => null,
                'effective_from' => now(),
                'effective_until' => null,
                'created_by' => $admin->id,
                'updated_by' => null,
            ],
        ];

        foreach ($settings as $setting) {
            AdminFeeSetting::updateOrCreate(
                ['name' => $setting['name']],
                $setting
            );
        }

        $this->command->info("✅ Created " . count($settings) . " admin fee settings");
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminFeeSetting;
use App\Models\User;

class AdminFeeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('usertype', 'dev')->first();

        if (!$admin) {
            $admin = User::first();
        }

        $settings = [
            [
                'name' => 'Biaya Admin Aplikasi - 10%',
                'description' => 'Biaya admin aplikasi sebesar 10% dari nilai lelang',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_amount' => 10000,
                'maximum_amount' => 10000000,
                'category' => 'auction',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'name' => 'Biaya Admin Aplikasi - Fixed 5000',
                'description' => 'Biaya admin aplikasi tetap sebesar Rp 5.000',
                'type' => 'fixed',
                'value' => 5000.00,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'category' => 'auction',
                'is_active' => false,
                'created_by' => $admin->id
            ],
            [
                'name' => 'Biaya Admin Aplikasi - 5% untuk Lelang Besar',
                'description' => 'Biaya admin aplikasi 5% untuk lelang di atas Rp 1 juta',
                'type' => 'percentage',
                'value' => 5.00,
                'minimum_amount' => 1000000,
                'maximum_amount' => null,
                'category' => 'auction',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'name' => 'Biaya Payment Gateway - Bank Transfer',
                'description' => 'Biaya payment gateway untuk bank transfer',
                'type' => 'percentage',
                'value' => 1.5,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'category' => 'payment_gateway',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'name' => 'Biaya Payment Gateway - Credit Card',
                'description' => 'Biaya payment gateway untuk credit card',
                'type' => 'percentage',
                'value' => 2.9,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'category' => 'payment_gateway',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'name' => 'Biaya Payment Gateway - E-Wallet',
                'description' => 'Biaya payment gateway untuk e-wallet',
                'type' => 'percentage',
                'value' => 2.0,
                'minimum_amount' => 0,
                'maximum_amount' => null,
                'category' => 'payment_gateway',
                'is_active' => true,
                'created_by' => $admin->id
            ]
        ];

        foreach ($settings as $setting) {
            AdminFeeSetting::create($setting);
        }
    }
}

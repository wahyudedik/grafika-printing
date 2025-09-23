<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminFeeSetting;
use App\Models\User;
use Illuminate\Support\Str;

class AdminFeeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('💰 Creating admin fee settings...');

        $adminUser = User::where('usertype', 'dev')->first();

        $feeSettings = [
            [
                'name' => 'Biaya Admin Aplikasi 10%',
                'description' => 'Biaya admin aplikasi 10% untuk lelang normal',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_amount' => 10000,
                'maximum_amount' => 10000000,
                'category' => 'auction',
                'is_active' => true,
                'effective_from' => now(),
                'effective_until' => now()->addYear(),
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Biaya Admin Aplikasi 5%',
                'description' => 'Biaya admin aplikasi 5% untuk lelang besar',
                'type' => 'percentage',
                'value' => 5.00,
                'minimum_amount' => 1000000,
                'maximum_amount' => 100000000,
                'category' => 'auction',
                'is_active' => true,
                'effective_from' => now(),
                'effective_until' => now()->addYear(),
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Biaya Admin Tetap',
                'description' => 'Biaya admin tetap Rp 5.000',
                'type' => 'fixed',
                'value' => 5000.00,
                'minimum_amount' => 0,
                'maximum_amount' => 1000000,
                'category' => 'auction',
                'is_active' => false, // Nonaktif by default
                'effective_from' => now(),
                'effective_until' => now()->addYear(),
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Biaya Payment Gateway Bank Transfer',
                'description' => 'Biaya payment gateway untuk bank transfer',
                'type' => 'percentage',
                'value' => 1.5,
                'minimum_amount' => 0,
                'maximum_amount' => 100000000,
                'category' => 'payment_gateway',
                'is_active' => true,
                'effective_from' => now(),
                'effective_until' => now()->addYear(),
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Biaya Payment Gateway Credit Card',
                'description' => 'Biaya payment gateway untuk credit card',
                'type' => 'percentage',
                'value' => 2.9,
                'minimum_amount' => 0,
                'maximum_amount' => 100000000,
                'category' => 'payment_gateway',
                'is_active' => true,
                'effective_from' => now(),
                'effective_until' => now()->addYear(),
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Biaya Payment Gateway E-Wallet',
                'description' => 'Biaya payment gateway untuk e-wallet',
                'type' => 'percentage',
                'value' => 2.0,
                'minimum_amount' => 0,
                'maximum_amount' => 100000000,
                'category' => 'payment_gateway',
                'is_active' => true,
                'effective_from' => now(),
                'effective_until' => now()->addYear(),
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Biaya Payment Gateway Retail Outlet',
                'description' => 'Biaya payment gateway untuk retail outlet',
                'type' => 'percentage',
                'value' => 1.0,
                'minimum_amount' => 0,
                'maximum_amount' => 100000000,
                'category' => 'payment_gateway',
                'is_active' => true,
                'effective_from' => now(),
                'effective_until' => now()->addYear(),
                'created_by' => $adminUser->id,
            ]
        ];

        foreach ($feeSettings as $feeData) {
            AdminFeeSetting::create([
                'name' => $feeData['name'],
                'description' => $feeData['description'],
                'type' => $feeData['type'],
                'value' => $feeData['value'],
                'minimum_amount' => $feeData['minimum_amount'],
                'maximum_amount' => $feeData['maximum_amount'],
                'category' => $feeData['category'],
                'is_active' => $feeData['is_active'],
                'effective_from' => $feeData['effective_from'],
                'effective_until' => $feeData['effective_until'],
                'created_by' => $feeData['created_by'],
            ]);
        }

        $this->command->info('✅ Admin fee settings created successfully!');
    }
}

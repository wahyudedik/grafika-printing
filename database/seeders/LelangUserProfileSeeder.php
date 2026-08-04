<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LelangUserProfile;
use Illuminate\Database\Seeder;

class LelangUserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding Lelang User Profiles...');

        // Create profiles for existing regular users
        $users = User::where('usertype', 'user')->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ No regular users found. Skipping lelang user profiles.');
            return;
        }

        $profiles = [
            [
                'company_name' => 'PT Printing Solution',
                'phone_number' => '081234567891',
                'address' => 'Jl. Sudirman No. 45, Jakarta Selatan',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
                'status' => 'active',
                'notes' => 'Pelanggan setia, sering cetak nota dan kartu nama',
                'is_verified' => true,
                'verified_at' => now()->subDays(5),
                'total_auctions' => 12,
                'total_won' => 8,
                'total_spent' => 2450000.00,
            ],
            [
                'company_name' => null,
                'phone_number' => '085678901234',
                'address' => 'Jl. Merdeka No. 12, Surabaya',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60123',
                'status' => 'active',
                'notes' => null,
                'is_verified' => false,
                'verified_at' => null,
                'total_auctions' => 3,
                'total_won' => 1,
                'total_spent' => 350000.00,
            ],
        ];

        $created = 0;
        foreach ($users as $index => $user) {
            $profileData = $profiles[$index % count($profiles)];

            LelangUserProfile::firstOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
            $created++;
        }

        $this->command->info("✅ Created {$created} lelang user profiles");
    }
}

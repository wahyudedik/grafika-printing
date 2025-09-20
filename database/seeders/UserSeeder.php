<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating additional users...');

        // Create 20 additional users
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => bcrypt('password'),
                'usertype' => 'user',
                'email_verified_at' => now()
            ]);
        }

        $this->command->info('✅ Successfully created 20 additional users!');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ResetAndSeedCommand extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Resetting database...');
        
        // Reset database
        Artisan::call('migrate:fresh');
        
        $this->command->info('🌱 Seeding comprehensive dummy data...');
        
        // Run comprehensive seeder
        $this->call([
            ComprehensiveDummyDataSeeder::class,
        ]);
        
        $this->command->info('✅ Database reset and seeded successfully!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SimpleTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Creating simple test users...'); 

        // Clear existing data (optional - be careful in production!)
        // User::truncate();
        // Vendor::truncate();

        // Create Dev User (Super Admin)
        $devUser = User::firstOrCreate(
            ['email' => 'dev@grafika-printing.com'],
            [
                'name' => 'Developer Admin',
                'password' => Hash::make('password'),
                'usertype' => 'dev',
                'email_verified_at' => now(),
                'uuid' => Str::uuid(),
            ]
        );

        $this->command->info("✅ Created DEV user: {$devUser->email} (password: password)");

        // Create Regular User
        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'usertype' => 'user',
                'email_verified_at' => now(),
                'uuid' => Str::uuid(),
            ]
        );

        $this->command->info("✅ Created USER: {$regularUser->email} (password: password)");

        // Create Vendor
        $vendorUser = User::firstOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'name' => 'Jane Vendor',
                'password' => Hash::make('password'),
                'usertype' => 'vendor',
                'email_verified_at' => now(),
                'uuid' => Str::uuid(),
            ]
        );

        $this->command->info("✅ Created VENDOR user: {$vendorUser->email} (password: password)");

        // Create Vendor Company
        $vendor = Vendor::firstOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'name' => 'Grafika Printing Vendor',
                'phone' => '081234567890',
                'address' => 'Jl. Vendor No. 123, Jakarta',
                'is_active' => true,
                'bank_verified' => true,
                'bank_verified_at' => now(),
                'bank_verified_by' => $devUser->id,
            ]
        );

        // Link vendor user to vendor company
        DB::table('vendor_user')->insert([
            'user_id' => $vendorUser->id,
            'vendor_id' => $vendor->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ Created VENDOR company: {$vendor->name}");

        $this->command->newLine();
        $this->command->info('🎉 Simple test data created successfully!');
        $this->command->newLine();
        $this->command->info('📋 LOGIN CREDENTIALS:');
        $this->command->info('┌─────────────────────────────────────────────────────────────┐');
        $this->command->info('│ DEV (Super Admin)                                           │');
        $this->command->info('│ Email: dev@grafika-printing.com                            │');
        $this->command->info('│ Password: password                                          │');
        $this->command->info('│ URL: /admin                                                 │');
        $this->command->info('├─────────────────────────────────────────────────────────────┤');
        $this->command->info('│ USER (Regular User)                                         │');
        $this->command->info('│ Email: user@example.com                                     │');
        $this->command->info('│ Password: password                                          │');
        $this->command->info('│ URL: /user                                                  │');
        $this->command->info('├─────────────────────────────────────────────────────────────┤');
        $this->command->info('│ VENDOR (Vendor Owner)                                       │');
        $this->command->info('│ Email: vendor@example.com                                   │');
        $this->command->info('│ Password: password                                          │');
        $this->command->info('│ URL: /vendor                                                │');
        $this->command->info('└─────────────────────────────────────────────────────────────┘');
        $this->command->newLine();
        $this->command->info('🚀 Ready for manual testing!');
    }
}

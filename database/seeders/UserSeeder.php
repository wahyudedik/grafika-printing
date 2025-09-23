<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Creating comprehensive user data...');

        // Create Dev/Admin Users
        $this->createDevUsers();

        // Create Regular Users
        $this->createRegularUsers();

        // Create Vendor Users
        $this->createVendorUsers();

        // Create Vendor-User Relationships
        $this->createVendorUserRelationships();

        $this->command->info('✅ User seeding completed successfully!');
    }

    private function createDevUsers()
    {
        $this->command->info('  🔧 Creating dev/admin users...');

        $devUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@grafika-printing.com',
                'password' => Hash::make('admin123'),
                'usertype' => 'dev',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'System Administrator',
                'email' => 'system@grafika-printing.com', 
                'password' => Hash::make('system123'),
                'usertype' => 'dev',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'Technical Support',
                'email' => 'support@grafika-printing.com',
                'password' => Hash::make('support123'),
                'usertype' => 'dev',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ]
        ];

        foreach ($devUsers as $userData) {
            User::create($userData);
        }

        $this->command->info('  ✅ Created ' . count($devUsers) . ' dev users');
    }

    private function createRegularUsers()
    {
        $this->command->info('  👤 Creating regular users...');

        $regularUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'user',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'user',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'user',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'user',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'user',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ]
        ];

        foreach ($regularUsers as $userData) {
            User::create($userData);
        }

        $this->command->info('  ✅ Created ' . count($regularUsers) . ' regular users');
    }

    private function createVendorUsers()
    {
        $this->command->info('  🏢 Creating vendor users...');

        $vendorUsers = [
            [
                'name' => 'Print Master',
                'email' => 'printmaster@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'vendor',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'Digital Print Pro',
                'email' => 'digitalprint@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'vendor',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'Creative Print Studio',
                'email' => 'creative@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'vendor',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'Express Print Services',
                'email' => 'express@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'vendor',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ],
            [
                'name' => 'Premium Print House',
                'email' => 'premium@example.com',
                'password' => Hash::make('password'),
                'usertype' => 'vendor',
                'email_verified_at' => now(),
                'uuid' => Str::uuid()->toString(),
            ]
        ];

        foreach ($vendorUsers as $userData) {
            User::create($userData);
        }

        $this->command->info('  ✅ Created ' . count($vendorUsers) . ' vendor users');
    }

    private function createVendorUserRelationships()
    {
        $this->command->info('  🔗 Creating vendor-user relationships...');

        // Get vendor users
        $vendorUsers = User::where('usertype', 'vendor')->get();

        // Create vendor records and link them
        foreach ($vendorUsers as $index => $user) {
            $vendor = Vendor::create([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '0812345678' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'address' => 'Jl. Vendor ' . ($index + 1) . ', Jakarta',
                'is_active' => true,
            ]);

            // Create vendor-user relationship
            $user->vendorUser()->attach($vendor->id);
        }

        $this->command->info('  ✅ Created vendor records and relationships');
    }
}

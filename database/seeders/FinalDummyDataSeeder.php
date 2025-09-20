<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\AdminFeeSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FinalDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->createUsers();
            $this->createVendors();
            $this->createAdminFeeSettings();
            $this->createVendorWallets();

            DB::commit();
            $this->command->info('✅ Final dummy data created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating final dummy data: ' . $e->getMessage());
        }
    }

    private function createUsers()
    {
        $this->command->info('Creating users...');

        // Create dev user
        User::firstOrCreate(
            ['email' => 'admin@grafika.com'],
            [
                'name' => 'Admin Grafika',
                'email' => 'admin@grafika.com',
                'password' => Hash::make('password'),
                'usertype' => 'dev',
                'email_verified_at' => now(),
                'last_login_at' => now()->subDays(rand(1, 30))
            ]
        );

        // Create vendor users
        $vendorUsers = [
            ['name' => 'Ahmad Print Shop', 'email' => 'ahmad@printshop.com'],
            ['name' => 'Budi Digital Printing', 'email' => 'budi@digitalprint.com'],
            ['name' => 'Citra Offset Printing', 'email' => 'citra@offset.com'],
            ['name' => 'Dedi Screen Printing', 'email' => 'dedi@screen.com'],
            ['name' => 'Eka Large Format', 'email' => 'eka@largeformat.com']
        ];

        foreach ($vendorUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'usertype' => 'vendor',
                    'email_verified_at' => now(),
                    'last_login_at' => now()->subDays(rand(1, 15))
                ]
            );
        }

        // Create regular users
        $regularUsers = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['name' => 'Bob Johnson', 'email' => 'bob@example.com'],
            ['name' => 'Alice Brown', 'email' => 'alice@example.com'],
            ['name' => 'Charlie Wilson', 'email' => 'charlie@example.com']
        ];

        foreach ($regularUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'usertype' => 'user',
                    'email_verified_at' => now(),
                    'last_login_at' => now()->subDays(rand(1, 10))
                ]
            );
        }
    }

    private function createVendors()
    {
        $this->command->info('Creating vendors...');

        $vendors = [
            [
                'name' => 'Ahmad Print Shop',
                'email' => 'ahmad@printshop.com',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 123, Jakarta',
                'website' => 'https://ahmadprintshop.com',
                'is_active' => true
            ],
            [
                'name' => 'Budi Digital Printing',
                'email' => 'budi@digitalprint.com',
                'phone' => '081234567891',
                'address' => 'Jl. Sudirman No. 456, Jakarta',
                'website' => 'https://budidigital.com',
                'is_active' => true
            ],
            [
                'name' => 'Citra Offset Printing',
                'email' => 'citra@offset.com',
                'phone' => '081234567892',
                'address' => 'Jl. Thamrin No. 789, Jakarta',
                'website' => 'https://citraoffset.com',
                'is_active' => true
            ],
            [
                'name' => 'Dedi Screen Printing',
                'email' => 'dedi@screen.com',
                'phone' => '081234567893',
                'address' => 'Jl. Gatot Subroto No. 321, Jakarta',
                'website' => 'https://dediscreen.com',
                'is_active' => true
            ],
            [
                'name' => 'Eka Large Format',
                'email' => 'eka@largeformat.com',
                'phone' => '081234567894',
                'address' => 'Jl. HR Rasuna Said No. 654, Jakarta',
                'website' => 'https://ekalargeformat.com',
                'is_active' => true
            ]
        ];

        foreach ($vendors as $vendorData) {
            Vendor::firstOrCreate(
                ['email' => $vendorData['email']],
                $vendorData
            );
        }
    }

    private function createAdminFeeSettings()
    {
        $this->command->info('Creating admin fee settings...');

        $settings = [
            [
                'name' => 'Biaya Admin Aplikasi - 10%',
                'description' => 'Biaya admin aplikasi 10% untuk lelang normal',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_amount' => 10000,
                'maximum_amount' => 10000000,
                'category' => 'auction',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'effective_until' => null,
                'conditions' => json_encode(['min_amount' => 10000, 'max_amount' => 1000000]),
                'created_by' => 1
            ],
            [
                'name' => 'Biaya Admin Aplikasi - 5%',
                'description' => 'Biaya admin aplikasi 5% untuk lelang besar',
                'type' => 'percentage',
                'value' => 5.00,
                'minimum_amount' => 1000000,
                'maximum_amount' => 100000000,
                'category' => 'auction',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'effective_until' => null,
                'conditions' => json_encode(['min_amount' => 1000000, 'max_amount' => 100000000]),
                'created_by' => 1
            ]
        ];

        foreach ($settings as $settingData) {
            AdminFeeSetting::firstOrCreate(
                ['name' => $settingData['name']],
                $settingData
            );
        }
    }

    private function createVendorWallets()
    {
        $this->command->info('Creating vendor wallets...');

        $vendors = Vendor::all();

        foreach ($vendors as $vendor) {
            VendorWallet::firstOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'balance' => rand(500000, 5000000),
                    'total_earned' => rand(1000000, 10000000),
                    'total_withdrawn' => rand(500000, 5000000)
                ]
            );
        }
    }
}

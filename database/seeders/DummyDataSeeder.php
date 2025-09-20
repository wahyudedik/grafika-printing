<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\AdminFeeSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
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
            $this->command->info('✅ Dummy data created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating dummy data: ' . $e->getMessage());
        }
    }

    private function createUsers()
    {
        $this->command->info('Creating dummy users...');
        
        // Create dev user
        User::updateOrCreate(
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
            [
                'name' => 'Ahmad Print Shop',
                'email' => 'ahmad@printshop.com',
                'usertype' => 'vendor'
            ],
            [
                'name' => 'Budi Digital Printing',
                'email' => 'budi@digitalprint.com',
                'usertype' => 'vendor'
            ],
            [
                'name' => 'Citra Offset Printing',
                'email' => 'citra@offset.com',
                'usertype' => 'vendor'
            ],
            [
                'name' => 'Dedi Screen Printing',
                'email' => 'dedi@screen.com',
                'usertype' => 'vendor'
            ],
            [
                'name' => 'Eka Large Format',
                'email' => 'eka@largeformat.com',
                'usertype' => 'vendor'
            ]
        ];

        foreach ($vendorUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'usertype' => $userData['usertype'],
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
            ['name' => 'Charlie Wilson', 'email' => 'charlie@example.com'],
            ['name' => 'Diana Lee', 'email' => 'diana@example.com'],
            ['name' => 'Eva Garcia', 'email' => 'eva@example.com'],
            ['name' => 'Frank Miller', 'email' => 'frank@example.com']
        ];

        foreach ($regularUsers as $userData) {
            User::updateOrCreate(
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
        $this->command->info('Creating dummy vendors...');
        
        $vendors = [
            [
                'name' => 'Ahmad Print Shop',
                'email' => 'ahmad@printshop.com',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 123, Jakarta',
                'website' => 'https://ahmadprintshop.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => true,
                'auto_withdrawal_date' => 15,
                'auto_withdrawal_amount' => 500000,
                'auto_withdrawal_method' => 'bank_transfer',
                'auto_withdrawal_account_number' => '1234567890',
                'auto_withdrawal_account_name' => 'Ahmad Print Shop',
                'auto_withdrawal_bank_name' => 'BCA'
            ],
            [
                'name' => 'Budi Digital Printing',
                'email' => 'budi@digitalprint.com',
                'phone' => '081234567891',
                'address' => 'Jl. Sudirman No. 456, Jakarta',
                'website' => 'https://budidigital.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => false,
                'auto_withdrawal_date' => 20,
                'auto_withdrawal_amount' => 1000000,
                'auto_withdrawal_method' => 'bank_transfer',
                'auto_withdrawal_account_number' => '2345678901',
                'auto_withdrawal_account_name' => 'Budi Digital Printing',
                'auto_withdrawal_bank_name' => 'BNI'
            ],
            [
                'name' => 'Citra Offset Printing',
                'email' => 'citra@offset.com',
                'phone' => '081234567892',
                'address' => 'Jl. Thamrin No. 789, Jakarta',
                'website' => 'https://citraoffset.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => true,
                'auto_withdrawal_date' => 25,
                'auto_withdrawal_amount' => 2000000,
                'auto_withdrawal_method' => 'bank_transfer',
                'auto_withdrawal_account_number' => '3456789012',
                'auto_withdrawal_account_name' => 'Citra Offset Printing',
                'auto_withdrawal_bank_name' => 'BRI'
            ],
            [
                'name' => 'Dedi Screen Printing',
                'email' => 'dedi@screen.com',
                'phone' => '081234567893',
                'address' => 'Jl. Gatot Subroto No. 321, Jakarta',
                'website' => 'https://dediscreen.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => false,
                'auto_withdrawal_date' => 30,
                'auto_withdrawal_amount' => 750000,
                'auto_withdrawal_method' => 'bank_transfer',
                'auto_withdrawal_account_number' => '4567890123',
                'auto_withdrawal_account_name' => 'Dedi Screen Printing',
                'auto_withdrawal_bank_name' => 'MANDIRI'
            ],
            [
                'name' => 'Eka Large Format',
                'email' => 'eka@largeformat.com',
                'phone' => '081234567894',
                'address' => 'Jl. HR Rasuna Said No. 654, Jakarta',
                'website' => 'https://ekalargeformat.com',
                'is_active' => true,
                'auto_withdrawal_enabled' => true,
                'auto_withdrawal_date' => 10,
                'auto_withdrawal_amount' => 1500000,
                'auto_withdrawal_method' => 'bank_transfer',
                'auto_withdrawal_account_number' => '5678901234',
                'auto_withdrawal_account_name' => 'Eka Large Format',
                'auto_withdrawal_bank_name' => 'BSI'
            ]
        ];

        foreach ($vendors as $vendorData) {
            Vendor::updateOrCreate(
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
            ],
            [
                'name' => 'Biaya Admin Tetap - Rp 5.000',
                'description' => 'Biaya admin tetap Rp 5.000 untuk lelang kecil',
                'type' => 'fixed',
                'value' => 5000.00,
                'minimum_amount' => 1000,
                'maximum_amount' => 10000,
                'category' => 'auction',
                'is_active' => false,
                'effective_from' => now()->subDays(30),
                'effective_until' => null,
                'conditions' => json_encode(['min_amount' => 1000, 'max_amount' => 10000]),
                'created_by' => 1
            ]
        ];

        foreach ($settings as $settingData) {
            AdminFeeSetting::updateOrCreate(
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
            VendorWallet::updateOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'balance' => rand(500000, 5000000),
                    'total_earned' => rand(1000000, 10000000),
                    'total_withdrawn' => rand(500000, 5000000),
                    'last_transaction_at' => now()->subDays(rand(1, 30))
                ]
            );
        }
    }
}

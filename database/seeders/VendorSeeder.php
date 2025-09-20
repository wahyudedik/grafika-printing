<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Support\Str;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating additional vendors...');

        $vendors = [
            [
                'name' => 'PrintPro Solutions',
                'email' => 'info@printpro.com',
                'phone' => '081234567891',
                'address' => 'Jl. PrintPro No. 10, Jakarta',
                'website' => 'printpro.com',
                'is_active' => true
            ],
            [
                'name' => 'Digital Print Center',
                'email' => 'contact@digitalprint.com',
                'phone' => '081234567892',
                'address' => 'Jl. Digital No. 20, Bandung',
                'website' => 'digitalprint.com',
                'is_active' => true
            ],
            [
                'name' => 'Creative Print Studio',
                'email' => 'hello@creativeprint.com',
                'phone' => '081234567893',
                'address' => 'Jl. Creative No. 30, Surabaya',
                'website' => 'creativeprint.com',
                'is_active' => true
            ],
            [
                'name' => 'Express Print Services',
                'email' => 'service@expressprint.com',
                'phone' => '081234567894',
                'address' => 'Jl. Express No. 40, Yogyakarta',
                'website' => 'expressprint.com',
                'is_active' => true
            ],
            [
                'name' => 'Premium Print House',
                'email' => 'info@premiumprint.com',
                'phone' => '081234567895',
                'address' => 'Jl. Premium No. 50, Medan',
                'website' => 'premiumprint.com',
                'is_active' => true
            ],
            [
                'name' => 'Quick Print Solutions',
                'email' => 'contact@quickprint.com',
                'phone' => '081234567896',
                'address' => 'Jl. Quick No. 60, Semarang',
                'website' => 'quickprint.com',
                'is_active' => true
            ],
            [
                'name' => 'Professional Print Co',
                'email' => 'info@proprint.com',
                'phone' => '081234567897',
                'address' => 'Jl. Professional No. 70, Makassar',
                'website' => 'proprint.com',
                'is_active' => true
            ],
            [
                'name' => 'Modern Print Works',
                'email' => 'hello@modernprint.com',
                'phone' => '081234567898',
                'address' => 'Jl. Modern No. 80, Palembang',
                'website' => 'modernprint.com',
                'is_active' => true
            ],
            [
                'name' => 'Elite Print Services',
                'email' => 'service@eliteprint.com',
                'phone' => '081234567899',
                'address' => 'Jl. Elite No. 90, Denpasar',
                'website' => 'eliteprint.com',
                'is_active' => true
            ],
            [
                'name' => 'Advanced Print Tech',
                'email' => 'info@advancedprint.com',
                'phone' => '081234567900',
                'address' => 'Jl. Advanced No. 100, Balikpapan',
                'website' => 'advancedprint.com',
                'is_active' => true
            ]
        ];

        foreach ($vendors as $vendorData) {
            $vendor = Vendor::create($vendorData);

            // Create vendor user
            $vendorUser = User::create([
                'name' => $vendorData['name'] . ' User',
                'email' => $vendorData['email'],
                'password' => bcrypt('password'),
                'usertype' => 'vendor',
                'email_verified_at' => now()
            ]);

            // Attach user to vendor
            $vendor->vendorUser()->attach($vendorUser->id);
        }

        $this->command->info('✅ Successfully created 10 additional vendors!');
    }
}

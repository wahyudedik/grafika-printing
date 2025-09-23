<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixVendorUserRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:vendor-user-relationships {--force : Force fix without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix vendor user relationships by creating missing vendor records and relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing vendor user relationships...');

        $vendorUsers = User::where('usertype', 'vendor')->get();
        $fixed = 0;
        $created = 0;

        foreach ($vendorUsers as $user) {
            $this->info("Processing user: {$user->name} ({$user->email})");

            // Check if user has vendor relationships
            if ($user->vendorUser->isEmpty()) {
                // Try to find existing vendor by name or email
                $vendor = Vendor::where('name', $user->name)
                    ->orWhere('email', $user->email)
                    ->first();

                if (!$vendor) {
                    // Create new vendor
                    $vendor = Vendor::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?? null,
                        'address' => 'Address not set',
                        'is_active' => true,
                        'bank_verified' => false
                    ]);
                    $created++;
                    $this->info("  ✅ Created vendor: {$vendor->name}");
                } else {
                    $this->info("  ✅ Found existing vendor: {$vendor->name}");
                }

                // Create vendor-user relationship
                DB::table('vendor_user')->insert([
                    'user_id' => $user->id,
                    'vendor_id' => $vendor->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $fixed++;
                $this->info("  ✅ Created relationship between user and vendor");
            } else {
                $this->info("  ✅ User already has vendor relationships");
            }
        }

        $this->info("🎉 Fix completed!");
        $this->info("Fixed relationships: {$fixed}");
        $this->info("Created vendors: {$created}");

        // Verify the fix
        $this->info("\n🔍 Verifying fix...");
        $this->call('debug:vendor-user');
    }
}

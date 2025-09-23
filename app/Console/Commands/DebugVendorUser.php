<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Console\Command;

class DebugVendorUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:vendor-user {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug vendor user relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return;
            }

            $this->debugUser($user);
        } else {
            $this->info('All vendor users:');
            $vendorUsers = User::where('usertype', 'vendor')->get();

            foreach ($vendorUsers as $user) {
                $this->debugUser($user);
                $this->line('---');
            }
        }
    }

    private function debugUser($user)
    {
        $this->info("User: {$user->name} ({$user->email})");
        $this->info("User Type: {$user->usertype}");

        if ($user->vendorUser && $user->vendorUser->count() > 0) {
            $this->info("Vendor Relationships: " . $user->vendorUser->count());
            foreach ($user->vendorUser as $vendorUser) {
                $vendorId = $vendorUser->vendor_id ?? $vendorUser->pivot->vendor_id ?? 'NULL';
                $vendor = Vendor::find($vendorId);
                $this->info("  - Vendor ID: {$vendorId}");
                $this->info("  - Vendor Name: " . ($vendor ? $vendor->name : 'NOT FOUND'));
                $this->info("  - Vendor Active: " . ($vendor ? ($vendor->is_active ? 'Yes' : 'No') : 'N/A'));
            }
        } else {
            $this->error("  No vendor relationships found!");
        }

        $this->info("Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No'));
    }
}

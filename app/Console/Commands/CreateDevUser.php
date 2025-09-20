<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateDevUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-dev {email} {password} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a dev user for accessing administrator dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name') ?? 'Dev User';

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // Update existing user to dev
            $existingUser->update([
                'usertype' => 'dev',
                'password' => Hash::make($password)
            ]);

            $this->info("User {$email} updated to dev user successfully!");
            $this->info("Email: {$email}");
            $this->info("Password: {$password}");
            $this->info("UserType: dev");
        } else {
            // Create new dev user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'usertype' => 'dev',
                'email_verified_at' => now()
            ]);

            $this->info("Dev user created successfully!");
            $this->info("ID: {$user->id}");
            $this->info("Name: {$user->name}");
            $this->info("Email: {$user->email}");
            $this->info("Password: {$password}");
            $this->info("UserType: {$user->usertype}");
        }

        // Test route access
        $this->info("\n--- Testing Route Access ---");
        try {
            $url = route('admin.dashboard');
            $this->info("✅ Dev dashboard URL: {$url}");
        } catch (\Exception $e) {
            $this->error("❌ Error generating dev dashboard URL: " . $e->getMessage());
        }

        return 0;
    }
}

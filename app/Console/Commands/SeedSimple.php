<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedSimple extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:simple';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed database with simple test data (3 users only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Seeding database with simple test data...');

        $this->call('db:seed', [
            '--class' => 'SimpleTestSeeder'
        ]);

        $this->newLine();
        $this->info('✅ Simple seeding completed!');
        $this->info('💡 You can now test features manually with clean data.');
    }
}

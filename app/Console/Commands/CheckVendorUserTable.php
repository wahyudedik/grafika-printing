<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckVendorUserTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:vendor-user-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check vendor_user table contents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking vendor_user table...');

        $relationships = DB::table('vendor_user')->get();

        $this->info("Total relationships: " . $relationships->count());

        foreach ($relationships as $rel) {
            $this->info("User ID: {$rel->user_id}, Vendor ID: {$rel->vendor_id}");
        }

        // Check if table exists
        if (!DB::getSchemaBuilder()->hasTable('vendor_user')) {
            $this->error('vendor_user table does not exist!');
            $this->info('Creating vendor_user table...');

            DB::statement('CREATE TABLE vendor_user (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                vendor_id BIGINT UNSIGNED NOT NULL,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY vendor_user_user_id_vendor_id_unique (user_id, vendor_id),
                KEY vendor_user_user_id_foreign (user_id),
                KEY vendor_user_vendor_id_foreign (vendor_id)
            )');

            $this->info('vendor_user table created!');
        }
    }
}

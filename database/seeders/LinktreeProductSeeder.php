<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * LinktreeProductSeeder - Seeder untuk menambahkan produk ke Linktree
 *
 * Seeder ini membuat LinktreeProduct records untuk menampilkan produk POS
 * di halaman publik linktree. Menggunakan DB::table() untuk menghindari
 * dependency TenantModel.
 *
 * Dependency: ProductionSeeder → PosCompleteSeeder → LinktreeSeeder
 *
 * Gunakan `updateOrCreate` untuk mencegah duplicate data.
 * Aman dijalankan berulang kali (idempotent).
 *
 * Usage:
 *   php artisan db:seed --class=LinktreeProductSeeder
 *   php artisan db:seed --class=LinktreeProductSeeder --force
 */
class LinktreeProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 LinktreeProductSeeder: Memulai seeding linktree products...');
        $this->command->newLine();

        // Cari vendor yang sudah ada dari ProductionSeeder
        $vendor = DB::table('vendors')->where('email', 'vendor@grafika-printing.com')->first();

        if (!$vendor) {
            $this->command->error('❌ Vendor tidak ditemukan! Jalankan ProductionSeeder terlebih dahulu.');
            $this->command->info('   php artisan db:seed --class=ProductionSeeder');
            return;
        }

        // Cari linktree yang sudah ada dari LinktreeSeeder
        $linktree = DB::table('linktrees')->where('vendor_id', $vendor->id)->first();

        if (!$linktree) {
            $this->command->error('❌ Linktree tidak ditemukan! Jalankan LinktreeSeeder terlebih dahulu.');
            $this->command->info('   php artisan db:seed --class=LinktreeSeeder');
            return;
        }

        // Cari produk yang sudah ada dari PosCompleteSeeder
        $produks = DB::table('produks')
            ->where('vendor_id', $vendor->id)
            ->orderBy('id')
            ->take(5)
            ->get();

        if ($produks->isEmpty()) {
            $this->command->error('❌ Produk tidak ditemukan! Jalankan PosCompleteSeeder terlebih dahulu.');
            $this->command->info('   php artisan db:seed --class=PosCompleteSeeder');
            return;
        }

        $this->command->info("📦 Vendor: {$vendor->name} (ID: {$vendor->id})");
        $this->command->info("🔗 Linktree: {$linktree->custom_url} (ID: {$linktree->id})");
        $this->command->info("📋 Produk ditemukan: {$produks->count()}");
        $this->command->newLine();

        DB::beginTransaction();

        try {
            $sortOrder = 1;

            foreach ($produks as $produk) {
                // Custom price: markup sedikit untuk linktree (lebih tinggi dari harga dasar)
                $customPrice = $produk->harga_jual
                    ? 'Rp ' . number_format((float) $produk->harga_jual + 5000, 0, ',', '.')
                    : null;

                // Custom description: deskripsi menarik untuk linktree
                $customDescription = $produk->deskripsi
                    ? "{$produk->deskripsi} - Kualitas terbaik untuk kebutuhan Anda! 🖨️"
                    : "Produk {$produk->nama_produk} - Kualitas terbaik untuk kebutuhan Anda! 🖨️";

                DB::table('linktree_products')->updateOrInsert(
                    [
                        'linktree_id' => $linktree->id,
                        'produk_id' => $produk->id,
                    ],
                    [
                        'vendor_id' => $vendor->id,
                        'sort_order' => $sortOrder++,
                        'is_active' => true,
                        'custom_price' => $customPrice,
                        'custom_description' => $customDescription,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $this->command->info("  ✅ {$produk->nama_produk} → sort_order: {$sortOrder}, price: {$customPrice}");
            }

            DB::commit();

            $this->command->newLine();
            $this->command->info("✅ LinktreeProductSeeder: {$produks->count()} produk berhasil ditambahkan ke linktree!");
            $this->command->info("   Linktree URL: /l/{$linktree->custom_url}");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ LinktreeProductSeeder: Gagal - ' . $e->getMessage());
            throw $e;
        }
    }
}

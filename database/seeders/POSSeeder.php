<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\TransaksiItemSpecifications;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Bahan;
use App\Models\Vendor;
use Illuminate\Support\Str;

class POSSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🖨️ Creating POS transaction data with thermal printing...');

        $vendors = Vendor::all();

        foreach ($vendors as $vendor) {
            $this->createPOSTransactions($vendor);
        }

        $this->command->info('✅ POS seeding completed successfully!');
    }

    private function createPOSTransactions($vendor)
    {
        $customers = $vendor->pelanggan;
        $products = $vendor->produk;
        $materials = $vendor->bahan;

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Create 5-10 POS transactions per vendor
        $transactionCount = rand(5, 10);

        for ($i = 0; $i < $transactionCount; $i++) {
            $customer = $customers->random();
            $product = $products->random();
            $material = $materials->random();

            $quantity = rand(1, 10);
            $unitPrice = $product->harga_dasar + rand(100, 1000);
            $subtotal = $quantity * $unitPrice;
            $discount = rand(0, 10) * $subtotal / 100;
            $total = $subtotal - $discount;

            $transaction = Transaksi::create([
                'vendor_id' => $vendor->id,
                'user_id' => $vendor->vendorUser->first()->id,
                'pelanggan_id' => $customer->id,
                'kode' => 'POS-' . strtoupper(Str::random(8)),
                'tanggal' => now()->subDays(rand(0, 30)),
                'total_harga' => $total,
                'diskon' => $discount,
                'status' => ['pending', 'completed', 'cancelled'][rand(0, 2)],
                'payment_method' => ['cash', 'xendit'][rand(0, 1)],
                'payment_status' => ['pending', 'paid', 'failed'][rand(0, 2)],
                'uuid' => Str::uuid()->toString(),
            ]);

            // Create transaction items
            $this->createTransactionItems($transaction, $product, $material, $quantity, $unitPrice);
        }
    }

    private function createTransactionItems($transaction, $product, $material, $quantity, $unitPrice)
    {
        $transactionItem = TransaksiItem::create([
            'vendor_id' => $transaction->vendor_id,
            'transaksi_id' => $transaction->id,
            'produk_id' => $product->id,
            'kuantitas' => $quantity,
            'harga_satuan' => $unitPrice,
            'uuid' => Str::uuid()->toString(),
        ]);

        // Create item specifications
        TransaksiItemSpecifications::create([
            'vendor_id' => $transaction->vendor_id,
            'transaksi_item_id' => $transactionItem->id,
            'spesifikasi_produk_id' => $product->spesifikasiProduk->first()->id,
            'nilai' => 'A4', // Default specification value
            'uuid' => Str::uuid()->toString(),
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\TransaksiItemSpecifications;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Produk;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Alat;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            $this->createCustomers();
            $this->createTransactions();
            $this->createTransactionItems();
            $this->createTransactionItemSpecifications();
            
            DB::commit();
            $this->command->info('✅ Transaction dummy data created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating transaction dummy data: ' . $e->getMessage());
        }
    }

    private function createCustomers()
    {
        $this->command->info('Creating customers...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $customers = [
                [
                    'nama' => 'PT ABC Corporation',
                    'email' => 'contact@abc.com',
                    'telepon' => '021-12345678',
                    'alamat' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                    'kategori' => 'Perusahaan',
                    'catatan' => 'Klien tetap, sering order'
                ],
                [
                    'nama' => 'CV XYZ Printing',
                    'email' => 'info@xyz.com',
                    'telepon' => '021-87654321',
                    'alamat' => 'Jl. Thamrin No. 456, Jakarta Selatan',
                    'kategori' => 'Perusahaan',
                    'catatan' => 'Reseller, order dalam jumlah besar'
                ],
                [
                    'nama' => 'John Doe',
                    'email' => 'john@example.com',
                    'telepon' => '081234567890',
                    'alamat' => 'Jl. Gatot Subroto No. 789, Jakarta Barat',
                    'kategori' => 'Perorangan',
                    'catatan' => 'Customer baru, order kecil'
                ],
                [
                    'nama' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'telepon' => '081234567891',
                    'alamat' => 'Jl. HR Rasuna Said No. 321, Jakarta Selatan',
                    'kategori' => 'Perorangan',
                    'catatan' => 'Customer VIP, order premium'
                ],
                [
                    'nama' => 'Event Organizer Pro',
                    'email' => 'contact@eventpro.com',
                    'telepon' => '021-98765432',
                    'alamat' => 'Jl. Senayan No. 654, Jakarta Pusat',
                    'kategori' => 'Event Organizer',
                    'catatan' => 'Event organizer, order untuk event'
                ]
            ];

            foreach ($customers as $customerData) {
                Pelanggan::updateOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'email' => $customerData['email']
                    ],
                    array_merge($customerData, ['vendor_id' => $vendor->id])
                );
            }
        }
    }

    private function createTransactions()
    {
        $this->command->info('Creating transactions...');
        
        $vendors = Vendor::all();
        
        foreach ($vendors as $vendor) {
            $customers = $vendor->pelanggan;
            $products = $vendor->produk;
            
            // Create 10-20 transactions per vendor
            $transactionCount = rand(10, 20);
            
            for ($i = 0; $i < $transactionCount; $i++) {
                $customer = $customers->random();
                $product = $products->random();
                
                $transaction = Transaksi::create([
                    'vendor_id' => $vendor->id,
                    'pelanggan_id' => $customer->id,
                    'user_id' => null, // POS transaction, not from auction
                    'kode_transaksi' => 'POS-' . date('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'tanggal_transaksi' => now()->subDays(rand(1, 30)),
                    'status' => $this->getRandomStatus(),
                    'metode_pembayaran' => $this->getRandomPaymentMethod(),
                    'subtotal' => 0, // Will be calculated from items
                    'diskon' => rand(0, 10),
                    'pajak' => 11, // 11% PPN
                    'total' => 0, // Will be calculated
                    'catatan' => $this->getRandomNotes(),
                    'tanggal_jatuh_tempo' => now()->addDays(rand(7, 30)),
                    'progress_percentage' => $this->getProgressPercentage(),
                    'estimasi_selesai' => now()->addDays(rand(1, 14)),
                    'created_at' => now()->subDays(rand(1, 30))
                ]);

                // Create transaction items
                $this->createTransactionItemsForTransaction($transaction, $vendor);
                
                // Update transaction totals
                $this->updateTransactionTotals($transaction);
            }
        }
    }

    private function createTransactionItems()
    {
        $this->command->info('Creating transaction items...');
        
        // This is handled in createTransactions method
    }

    private function createTransactionItemSpecifications()
    {
        $this->command->info('Creating transaction item specifications...');
        
        $transactionItems = TransaksiItem::all();
        
        foreach ($transactionItems as $item) {
            $product = $item->produk;
            $specifications = $product->spesifikasiProduk;
            
            foreach ($specifications as $spec) {
                $value = $this->getRandomSpecValue($spec);
                
                TransaksiItemSpecifications::create([
                    'vendor_id' => $item->vendor_id,
                    'transaksi_item_id' => $item->id,
                    'spesifikasi_produk_id' => $spec->id,
                    'value' => $value,
                    'created_at' => $item->created_at
                ]);
            }
        }
    }

    private function createTransactionItemsForTransaction($transaction, $vendor)
    {
        $products = $vendor->produk;
        $itemCount = rand(1, 3); // 1-3 items per transaction
        
        for ($i = 0; $i < $itemCount; $i++) {
            $product = $products->random();
            $quantity = rand(1, 10);
            $unitPrice = $product->harga_dasar * (1 + rand(0, 50) / 100); // 0-50% markup
            
            TransaksiItem::create([
                'vendor_id' => $vendor->id,
                'transaksi_id' => $transaction->id,
                'produk_id' => $product->id,
                'kuantitas' => $quantity,
                'harga_satuan' => $unitPrice,
                'subtotal' => $quantity * $unitPrice,
                'catatan' => $this->getRandomItemNotes(),
                'created_at' => $transaction->created_at
            ]);
        }
    }

    private function updateTransactionTotals($transaction)
    {
        $items = $transaction->transaksiItem;
        $subtotal = $items->sum('subtotal');
        $discount = $subtotal * ($transaction->diskon / 100);
        $subtotalAfterDiscount = $subtotal - $discount;
        $tax = $subtotalAfterDiscount * ($transaction->pajak / 100);
        $total = $subtotalAfterDiscount + $tax;
        
        $transaction->update([
            'subtotal' => $subtotal,
            'total' => $total
        ]);
    }

    private function getRandomStatus()
    {
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        $weights = [20, 30, 25, 20, 5]; // 20% pending, 30% confirmed, 25% in_progress, 20% completed, 5% cancelled
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'pending';
    }

    private function getRandomPaymentMethod()
    {
        $methods = ['cash', 'transfer', 'credit_card', 'debit_card'];
        return $methods[array_rand($methods)];
    }

    private function getProgressPercentage()
    {
        $percentages = [0, 25, 50, 75, 100];
        return $percentages[array_rand($percentages)];
    }

    private function getRandomNotes()
    {
        $notes = [
            'Order untuk event launching produk baru',
            'Dibutuhkan segera untuk presentasi',
            'Kualitas premium, budget tidak terbatas',
            'Order rutin bulanan',
            'Untuk keperluan promosi',
            'Order untuk acara pernikahan',
            'Dibutuhkan untuk event corporate',
            'Order untuk kebutuhan internal perusahaan'
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getRandomItemNotes()
    {
        $notes = [
            'Warna sesuai dengan brand guideline',
            'Kualitas premium, bahan terbaik',
            'Dibutuhkan segera, prioritas tinggi',
            'Desain sudah disetujui, tinggal cetak',
            'Untuk keperluan promosi produk',
            'Order untuk event launching',
            'Kualitas standar, budget terbatas',
            'Untuk kebutuhan internal'
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getRandomSpecValue($spec)
    {
        $options = json_decode($spec->opsi, true);
        
        if ($spec->tipe_input === 'select') {
            if (is_array($options)) {
                return $options[array_rand($options)];
            }
        } elseif ($spec->tipe_input === 'number') {
            if (isset($options['min']) && isset($options['max'])) {
                return rand($options['min'], $options['max']);
            }
        } elseif ($spec->tipe_input === 'text') {
            $textValues = [
                'Custom text value',
                'Special request',
                'As per specification',
                'Standard quality',
                'Premium quality'
            ];
            return $textValues[array_rand($textValues)];
        }
        
        return 'Default value';
    }
}

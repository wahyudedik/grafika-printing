<?php

namespace App\Actions\Transaksi;

use App\Actions\BaseAction;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\TransaksiItemSpecifications;
use Illuminate\Support\Str;

class CreateTransaksi extends BaseAction
{
    /**
     * Create a new transaction with items and specifications.
     *
     * Expected data:
     * - vendor_id (int)
     * - user_id (int)
     * - pelanggan_id (int)
     * - payment_method (string)
     * - estimasi_selesai (string, nullable)
     * - catatan (string, nullable)
     * - terbayar (float, optional — defaults to total)
     * - items (array)
     *   - produk_id (int)
     *   - kuantitas (int)
     *   - harga_satuan (float)
     *   - specifications (array, optional)
     *     - specId => ['bahan_id' => ..., 'value' => ..., 'input_type' => ..., 'price' => ...]
     */
    public function handle(array $data): Transaksi
    {
        $vendorId = $data['vendor_id'];

        // Generate transaction code
        $transactionCode = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // Calculate total price
        $totalPrice = 0;
        foreach ($data['items'] as $item) {
            $totalPrice += $item['kuantitas'] * $item['harga_satuan'];
        }

        // Calculate payment
        $terbayar = $data['terbayar'] ?? $totalPrice;
        $kembali = max(0, $terbayar - $totalPrice);

        // Create transaction
        $transaksi = Transaksi::create([
            'vendor_id' => $vendorId,
            'kode' => $transactionCode,
            'user_id' => $data['user_id'],
            'pelanggan_id' => $data['pelanggan_id'],
            'total_harga' => $totalPrice,
            'status' => 'pending',
            'payment_method' => $data['payment_method'],
            'estimasi_selesai' => $data['estimasi_selesai'] ?? null,
            'tanggal_dibuat' => now(),
            'progress_percentage' => 0,
            'catatan' => $data['catatan'] ?? null,
            'terbayar' => $terbayar,
            'kembali' => $kembali,
        ]);

        // Process transaction items
        foreach ($data['items'] as $itemData) {
            $item = TransaksiItem::create([
                'vendor_id' => $vendorId,
                'transaksi_id' => $transaksi->id,
                'produk_id' => $itemData['produk_id'],
                'kuantitas' => $itemData['kuantitas'],
                'harga_satuan' => $itemData['harga_satuan'],
            ]);

            // Process specifications if provided
            if (isset($itemData['specifications']) && is_array($itemData['specifications'])) {
                foreach ($itemData['specifications'] as $specId => $specData) {
                    if (empty($specData['value']) && !isset($specData['bahan_id'])) {
                        continue;
                    }

                    TransaksiItemSpecifications::create([
                        'vendor_id' => $vendorId,
                        'transaksi_item_id' => $item->id,
                        'spesifikasi_produk_id' => $specId,
                        'bahan_id' => $specData['bahan_id'] ?? null,
                        'value' => $specData['value'] ?? null,
                        'input_type' => $specData['input_type'] ?? 'text',
                        'price' => $specData['price'] ?? 0,
                    ]);
                }
            }
        }

        return $transaksi;
    }
}

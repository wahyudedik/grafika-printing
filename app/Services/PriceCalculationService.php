<?php

namespace App\Services;

use App\Models\Vendor\Bahan;
use App\Models\Vendor\Produk;
use App\Models\Vendor\WholesalePrice;
use App\Models\Vendor\SpesifikasiProduk;
use Illuminate\Support\Facades\Log;

class PriceCalculationService
{
    /**
     * Hitung harga final untuk satu bahan berdasarkan quantity (wholesale tier).
     * Consolidation dari Bahan::getPriceForQuantity() dan WholesalePrice::calculateFinalPrice()
     *
     * @param Bahan $bahan
     * @param int $quantity
     * @return float Harga per unit
     */
    public function getPriceForQuantity(Bahan $bahan, int $quantity): float
    {
        $wholesalePrice = $bahan->wholesalePrices()
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->where('max_quantity', '>=', $quantity)
                    ->orWhereNull('max_quantity');
            })
            ->orderBy('min_quantity', 'desc')
            ->first();

        return $wholesalePrice ? (float) $wholesalePrice->harga : (float) $bahan->hpp;
    }

    /**
     * Hitung harga final untuk satu spesifikasi (bahan tertentu).
     * Menggabungkan logic dari WholesalePrice::calculateFinalPrice() dan Bahan::getPriceForQuantity()
     *
     * @param Bahan $bahan
     * @param float $value Nilai input (qty untuk select, dimensi untuk number)
     * @param int $quantity Jumlah item
     * @return array{price_per_unit: float, total_price: float, wholesale_applied: bool}
     */
    public function calculateSpecificationPrice(Bahan $bahan, float $value, int $quantity): array
    {
        $pricePerUnit = $this->getPriceForQuantity($bahan, (int) $value);

        // Cek apakah ada wholesale pricing yang berlaku
        $wholesaleApplied = $bahan->wholesalePrices()
            ->where('min_quantity', '<=', $value)
            ->where(function ($query) use ($value) {
                $query->where('max_quantity', '>=', $value)
                    ->orWhereNull('max_quantity');
            })
            ->exists();

        $totalPrice = $pricePerUnit * $value * $quantity;

        return [
            'price_per_unit' => $pricePerUnit,
            'total_price' => $totalPrice,
            'wholesale_applied' => $wholesaleApplied,
        ];
    }

    /**
     * Hitung total harga untuk satu cart item / transaksi item.
     * Menggabungkan semua spesifikasi menjadi total.
     *
     * @param array $specifications [specId => value]
     * @param int $quantity
     * @return array{total_price: float, specifications: array, hpp_total: float}
     */
    public function calculateItemTotal(array $specifications, int $quantity): array
    {
        $specDetails = [];
        $totalPrice = 0;
        $hppTotal = 0;

        foreach ($specifications as $specId => $value) {
            $spesifikasiProduk = SpesifikasiProduk::with(['spesifikasi', 'bahans'])
                ->find($specId);

            if (!$spesifikasiProduk) {
                continue;
            }

            $inputType = $spesifikasiProduk->spesifikasi->tipe_input;

            if ($inputType === 'select') {
                $bahan = Bahan::with('wholesalePrice')->find($value);
                if ($bahan) {
                    $finalPrice = $this->getPriceForQuantity($bahan, $quantity);
                    $specPrice = $finalPrice * $quantity;

                    $specDetails[$specId] = [
                        'value' => $value,
                        'bahan_id' => $bahan->id,
                        'input_type' => 'select',
                        'price' => $specPrice,
                        'nama_spesifikasi' => $spesifikasiProduk->spesifikasi->nama_spesifikasi,
                    ];

                    $totalPrice += $specPrice;
                    $hppTotal += (float) $bahan->hpp * $quantity;
                }
            } else {
                $inputValue = (float) $value;
                $bahan = $spesifikasiProduk->bahans->first();
                if ($bahan) {
                    $pricePerUnit = $this->getPriceForQuantity($bahan, (int) $inputValue);
                    $specPrice = $pricePerUnit * $inputValue * $quantity;

                    $specDetails[$specId] = [
                        'value' => $inputValue,
                        'bahan_id' => $bahan->id,
                        'input_type' => 'number',
                        'price' => $specPrice,
                        'nama_spesifikasi' => $spesifikasiProduk->spesifikasi->nama_spesifikasi,
                    ];

                    $totalPrice += $specPrice;
                    $hppTotal += (float) $bahan->hpp * $inputValue * $quantity;
                }
            }
        }

        return [
            'total_price' => $totalPrice,
            'specifications' => $specDetails,
            'hpp_total' => $hppTotal,
        ];
    }

    /**
     * Hitung total harga seluruh cart.
     *
     * @param array $cartItems Session cart items
     * @return array{subtotal: float, hpp_total: float, profit: float}
     */
    public function calculateCartTotal(array $cartItems): array
    {
        $subtotal = 0;
        $hppTotal = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item['total_price'] ?? 0;

            // Recalculate HPP untuk setiap item
            $quantity = $item['quantity'] ?? 1;
            $itemHpp = 0;

            foreach (($item['specifications'] ?? []) as $specId => $spec) {
                $bahan = Bahan::find($spec['bahan_id'] ?? null);
                if ($bahan) {
                    if (($spec['input_type'] ?? '') === 'select') {
                        $itemHpp += (float) $bahan->hpp * $quantity;
                    } else {
                        $inputValue = (float) ($spec['value'] ?? 1);
                        $itemHpp += (float) $bahan->hpp * $inputValue * $quantity;
                    }
                }
            }

            $hppTotal += $itemHpp;
        }

        return [
            'subtotal' => $subtotal,
            'hpp_total' => $hppTotal,
            'profit' => $subtotal - $hppTotal,
        ];
    }

    /**
     * Hitung HPP (Harga Pokok Penjualan) per item.
     * Akan digunakan di Profit Tracking.
     *
     * @param array $specifications [specId => value]
     * @param int $quantity
     * @return array{items: array, total_hpp: float}
     */
    public function calculateHppTotal(array $specifications, int $quantity): array
    {
        $items = [];
        $totalHpp = 0;

        foreach ($specifications as $specId => $value) {
            $spesifikasiProduk = SpesifikasiProduk::with(['spesifikasi', 'bahans'])
                ->find($specId);

            if (!$spesifikasiProduk) {
                continue;
            }

            $inputType = $spesifikasiProduk->spesifikasi->tipe_input;

            if ($inputType === 'select') {
                $bahan = Bahan::find($value);
                if ($bahan) {
                    $hppPerUnit = (float) $bahan->hpp;
                    $itemHpp = $hppPerUnit * $quantity;

                    $items[] = [
                        'bahan_id' => $bahan->id,
                        'nama_bahan' => $bahan->nama_bahan,
                        'hpp_per_unit' => $hppPerUnit,
                        'quantity' => $quantity,
                        'total_hpp' => $itemHpp,
                    ];

                    $totalHpp += $itemHpp;
                }
            } else {
                $inputValue = (float) $value;
                $bahan = $spesifikasiProduk->bahans->first();
                if ($bahan) {
                    $hppPerUnit = (float) $bahan->hpp;
                    $itemHpp = $hppPerUnit * $inputValue * $quantity;

                    $items[] = [
                        'bahan_id' => $bahan->id,
                        'nama_bahan' => $bahan->nama_bahan,
                        'hpp_per_unit' => $hppPerUnit,
                        'quantity' => $inputValue * $quantity,
                        'total_hpp' => $itemHpp,
                    ];

                    $totalHpp += $itemHpp;
                }
            }
        }

        return [
            'items' => $items,
            'total_hpp' => $totalHpp,
        ];
    }

    /**
     * Hitung admin fee — delegate ke AdminFeeService.
     *
     * @param float $total
     * @return array{total_fee: float, fee_breakdown: array, settings_applied: int}
     */
    public function calculateAdminFee(float $total): array
    {
        $adminFeeService = app(AdminFeeService::class);
        return $adminFeeService->calculateFees($total, 'pos_transaction');
    }
}

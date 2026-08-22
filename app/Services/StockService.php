<?php

namespace App\Services;

use App\Facades\Tenant;
use App\Models\Vendor;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\StockAlert;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\TransaksiItemSpecifications;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Validate stock for all items before checkout.
     *
     * @param array $cartItems Cart items from session
     * @return bool True if all stock is sufficient
     * @throws \Exception If stock is insufficient
     */
    public function validateStock(array $items): bool
    {
        $insufficientItems = [];

        foreach ($items as $item) {
            if ($item['quantity'] <= 0) {
                throw new \Exception('Jumlah item tidak valid (quantity harus lebih dari 0).');
            }

            foreach ($item['specifications'] as $specId => $spec) {
                $bahan = Bahan::find($spec['bahan_id']);

                if ($bahan && $bahan->stok !== null) {
                    $requiredStock = $spec['input_type'] === 'number'
                        ? (float) $spec['value'] * $item['quantity']
                        : $item['quantity'];

                    if ($bahan->stok < $requiredStock) {
                        $insufficientItems[] = "{$bahan->nama_bahan} (tersedia: {$bahan->stok}, dibutuhkan: {$requiredStock})";
                    }
                }
            }
        }

        if (!empty($insufficientItems)) {
            throw new \Exception('Stok bahan tidak mencukupi: ' . implode(', ', $insufficientItems));
        }

        return true;
    }

    /**
     * Decrement stock for each item (called after successful checkout).
     * Also checks minimum stock and creates alerts if needed.
     *
     * @param Transaksi $transaksi The completed transaction
     * @return void
     */
    public function decrementStock(Transaksi $transaksi): void
    {
        $items = $transaksi->transaksiItem()->with('transaksiItemSpecifications.bahan')->get();

        foreach ($items as $item) {
            foreach ($item->transaksiItemSpecifications as $spec) {
                if ($spec->bahan) {
                    $quantity = $spec->input_type === 'number'
                        ? (float) $spec->value * $item->kuantitas
                        : $item->kuantitas;

                    $bahan = $spec->bahan;
                    $previousStock = (int) $bahan->stok;
                    $bahan->decrement('stok', $quantity);
                    $currentStock = (int) $bahan->fresh()->stok;

                    Log::info('Stock decremented', [
                        'bahan_id' => $bahan->id,
                        'bahan_name' => $bahan->nama_bahan,
                        'quantity_decremented' => $quantity,
                        'remaining_stock' => $currentStock,
                        'transaksi_id' => $transaksi->id,
                    ]);

                    // Check minimum stock after decrement
                    $this->checkMinimumStock($bahan, $previousStock, $currentStock);
                }
            }
        }
    }

    /**
     * Restore stock (called when payment fails or expires).
     *
     * @param Transaksi $transaksi The failed/expired transaction
     * @return void
     */
    public function restoreStock(Transaksi $transaksi): void
    {
        $items = $transaksi->transaksiItem()->with('transaksiItemSpecifications.bahan')->get();

        foreach ($items as $item) {
            foreach ($item->transaksiItemSpecifications as $spec) {
                if ($spec->bahan) {
                    $quantity = $spec->input_type === 'number'
                        ? (float) $spec->value * $item->kuantitas
                        : $item->kuantitas;

                    $bahan = $spec->bahan;
                    $previousStock = (int) $bahan->stok;
                    $bahan->increment('stok', $quantity);
                    $currentStock = (int) $bahan->fresh()->stok;

                    Log::info('Stock restored', [
                        'bahan_id' => $bahan->id,
                        'bahan_name' => $bahan->nama_bahan,
                        'quantity_restored' => $quantity,
                        'new_stock' => $currentStock,
                        'transaksi_id' => $transaksi->id,
                    ]);

                    // If stock was previously at zero or low, create a restocked alert
                    $minimumStock = $bahan->minimum_stok ?? 5;
                    if ($previousStock <= $minimumStock && $currentStock > $minimumStock) {
                        $this->createStockAlert(
                            $bahan->vendor,
                            $bahan,
                            'restocked',
                            $previousStock,
                            $currentStock,
                            "Stok {$bahan->nama_bahan} telah diisi ulang dari {$previousStock} menjadi {$currentStock}"
                        );
                    }
                }
            }
        }
    }

    /**
     * Check if a bahan's stock has reached or fallen below minimum_stok.
     * Creates appropriate stock alerts.
     *
     * @param Bahan $bahan The material to check
     * @param int $previousStock Stock before the change
     * @param int $currentStock Stock after the change
     * @return void
     */
    public function checkMinimumStock(Bahan $bahan, int $previousStock, int $currentStock): void
    {
        $minimumStock = $bahan->minimum_stok ?? 5;

        if ($currentStock <= 0) {
            // Out of stock
            $this->createStockAlert(
                $bahan->vendor,
                $bahan,
                'out_of_stock',
                $previousStock,
                $currentStock,
                "Stok {$bahan->nama_bahan} habis (dari {$previousStock} ke {$currentStock})"
            );
        } elseif ($currentStock <= $minimumStock) {
            // Low stock
            $this->createStockAlert(
                $bahan->vendor,
                $bahan,
                'low_stock',
                $previousStock,
                $currentStock,
                "Stok {$bahan->nama_bahan} rendah: {$currentStock} {$bahan->satuan} (minimum: {$minimumStock})"
            );
        }
    }

    /**
     * Check for materials with stock at or below their minimum_stok threshold.
     *
     * @param int $vendorId The vendor ID
     * @return Collection Materials with low stock
     */
    public function checkLowStock(int $vendorId): Collection
    {
        return Bahan::where('vendor_id', $vendorId)
            ->whereColumn('stok', '<=', 'minimum_stok')
            ->where('stok', '>', 0)
            ->get();
    }

    /**
     * Check for materials that are completely out of stock.
     *
     * @param int $vendorId The vendor ID
     * @return Collection Materials with zero stock
     */
    public function checkOutOfStock(int $vendorId): Collection
    {
        return Bahan::where('vendor_id', $vendorId)
            ->where('stok', '<=', 0)
            ->get();
    }

    /**
     * Create a stock alert record.
     *
     * @param Vendor $vendor The vendor
     * @param Bahan $bahan The material
     * @param string $type Alert type: low_stock, out_of_stock, restocked
     * @param int $previousStock Stock before change
     * @param int $currentStock Stock after change
     * @param string|null $message Optional message
     * @return StockAlert
     */
    public function createStockAlert(
        Vendor $vendor,
        Bahan $bahan,
        string $type,
        int $previousStock,
        int $currentStock,
        ?string $message = null
    ): StockAlert {
        // Prevent duplicate alerts: check if there's an unread alert of the same type
        // for this bahan within the last 5 minutes
        $recentAlert = StockAlert::where('vendor_id', $vendor->id)
            ->where('bahan_id', $bahan->id)
            ->where('type', $type)
            ->where('is_read', false)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($recentAlert) {
            // Update existing alert instead of creating a new one
            $recentAlert->update([
                'current_stock' => $currentStock,
                'message' => $message ?? $recentAlert->message,
            ]);

            return $recentAlert->refresh();
        }

        $alert = StockAlert::create([
            'vendor_id' => $vendor->id,
            'bahan_id' => $bahan->id,
            'type' => $type,
            'previous_stock' => $previousStock,
            'current_stock' => $currentStock,
            'threshold' => $bahan->minimum_stok ?? 5,
            'message' => $message,
        ]);

        Log::warning("Stock alert created: {$type} for {$bahan->nama_bahan}", [
            'vendor_id' => $vendor->id,
            'bahan_id' => $bahan->id,
            'type' => $type,
            'previous_stock' => $previousStock,
            'current_stock' => $currentStock,
            'threshold' => $alert->threshold,
        ]);

        return $alert;
    }

    /**
     * Get unread stock alerts for a vendor.
     *
     * @param int $vendorId The vendor ID
     * @return Collection Unread stock alerts
     */
    public function getUnreadAlerts(int $vendorId): Collection
    {
        return StockAlert::where('vendor_id', $vendorId)
            ->where('is_read', false)
            ->with('bahan')
            ->latest()
            ->get();
    }

    /**
     * Get count of unread stock alerts for a vendor.
     *
     * @param int $vendorId The vendor ID
     * @return int Count of unread alerts
     */
    public function getUnreadAlertCount(int $vendorId): int
    {
        return StockAlert::where('vendor_id', $vendorId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark a stock alert as read.
     *
     * @param int $alertId The alert ID
     * @param int $vendorId The vendor ID (for security)
     * @return bool Success status
     */
    public function markAsRead(int $alertId, int $vendorId): bool
    {
        $alert = StockAlert::where('id', $alertId)
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$alert) {
            return false;
        }

        $alert->markAsRead();
        return true;
    }

    /**
     * Mark all stock alerts as read for a vendor.
     *
     * @param int $vendorId The vendor ID
     * @return int Number of alerts marked as read
     */
    public function markAllAsRead(int $vendorId): int
    {
        return StockAlert::where('vendor_id', $vendorId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Notify vendor about low stock items via email.
     *
     * @param Vendor $vendor The vendor
     * @param Collection $lowStockItems Materials with low stock
     * @return void
     */
    public function notifyLowStock(Vendor $vendor, Collection $lowStockItems): void
    {
        if ($lowStockItems->isEmpty()) {
            return;
        }

        $itemList = $lowStockItems->map(function ($item) {
            return "- {$item->nama_bahan}: {$item->stok} {$item->satuan}";
        })->join("\n");

        Log::warning("Low stock notification for vendor {$vendor->id} ({$vendor->name})", [
            'vendor_id' => $vendor->id,
            'low_stock_items' => $lowStockItems->pluck('id', 'nama_bahan')->toArray(),
        ]);

        // Send email notification if vendor has email
        if ($vendor->email) {
            \Illuminate\Support\Facades\Mail::raw(
                "Stok bahan berikut sudah mencapai batas minimum:\n\n{$itemList}\n\nSilakan lakukan restock segera.",
                function ($message) use ($vendor) {
                    $message->to($vendor->email)
                        ->subject('⚠️ Peringatan Stok Rendah - ' . config('app.name'));
                }
            );
        }
    }
}

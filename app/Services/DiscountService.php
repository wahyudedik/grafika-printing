<?php

namespace App\Services;

use App\Facades\Tenant;
use App\Models\Vendor\Coupon;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiDiscount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DiscountService
{
    /**
     * Validasi kupon dan hitung diskon
     *
     * @param string $code    Kode kupon
     * @param float  $subtotal Total sebelum diskon
     * @return array{valid: bool, discount_amount: float, message: string, coupon: ?Coupon}
     */
    public function validateCoupon(string $code, float $subtotal): array
    {
        $vendorId = Tenant::getVendorId();

        $coupon = Coupon::withoutGlobalScope('tenant')
            ->where('vendor_id', $vendorId)
            ->where('code', strtoupper(trim($code)))
            ->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'discount_amount' => 0,
                'message' => 'Kode kupon tidak ditemukan.',
                'coupon' => null,
            ];
        }

        if (!$coupon->isValid()) {
            return [
                'valid' => false,
                'discount_amount' => 0,
                'message' => 'Kupon ini sudah tidak aktif atau belum berlaku.',
                'coupon' => null,
            ];
        }

        if (!$coupon->canBeUsed()) {
            return [
                'valid' => false,
                'discount_amount' => 0,
                'message' => 'Kupon ini sudah mencapai batas penggunaan.',
                'coupon' => null,
            ];
        }

        if ($subtotal < $coupon->minimum_order) {
            return [
                'valid' => false,
                'discount_amount' => 0,
                'message' => 'Minimum pembelian untuk kupon ini adalah Rp ' . number_format($coupon->minimum_order, 0, ',', '.') . '.',
                'coupon' => null,
            ];
        }

        $discountAmount = $coupon->calculateDiscount($subtotal);

        return [
            'valid' => true,
            'discount_amount' => $discountAmount,
            'message' => 'Kupon berhasil diterapkan! Diskon: Rp ' . number_format($discountAmount, 0, ',', '.'),
            'coupon' => $coupon,
        ];
    }

    /**
     * Terapkan diskon ke transaksi
     *
     * @param Transaksi      $transaksi
     * @param array          $discountData Hasil dari validateCoupon
     * @param User           $user         User yang menerapkan diskon
     * @return TransaksiDiscount
     */
    public function applyDiscountToTransaction(
        Transaksi $transaksi,
        array $discountData,
        User $user
    ): TransaksiDiscount {
        $discount = TransaksiDiscount::create([
            'vendor_id' => $transaksi->vendor_id,
            'transaksi_id' => $transaksi->id,
            'coupon_id' => $discountData['coupon']?->id,
            'discount_code' => $discountData['coupon']?->code,
            'discount_type' => $discountData['coupon'] ? 'coupon' : 'manual',
            'discount_amount' => $discountData['discount_amount'],
            'description' => $discountData['message'] ?? null,
            'applied_by_user_id' => $user->id,
        ]);

        // Update transaksi
        $discountAmount = $discountData['discount_amount'];
        $totalBeforeDiscount = $transaksi->total_harga;

        $transaksi->update([
            'diskon_total' => $discountAmount,
            'total_sebelum_diskon' => $totalBeforeDiscount,
            'total_harga' => max(0, $totalBeforeDiscount - $discountAmount),
        ]);

        // Update kupon usage
        if ($discountData['coupon']) {
            $discountData['coupon']->incrementUsage();
        }

        return $discount;
    }

    /**
     * List semua kupon untuk vendor tertentu
     */
    public function getCoupons(): Collection
    {
        $vendorId = Tenant::getVendorId();
        return Coupon::withoutGlobalScope('tenant')
            ->where('vendor_id', $vendorId)
            ->orderByDesc('created_at')
            ->get();
    }
}

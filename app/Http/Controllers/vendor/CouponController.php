<?php

namespace App\Http\Controllers\vendor;

use App\Facades\Tenant;
use App\Models\Vendor\Coupon;
use App\Services\DiscountService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Concerns\HasVendorContext;
use App\Http\Responses\FlashMessage;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    use HasVendorContext;

    protected DiscountService $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * Daftar semua kupon
     */
    public function index()
    {
        $vendor = $this->requireVendor();
        $coupons = $this->discountService->getCoupons();

        return view('vendor.coupons.index', compact('coupons', 'vendor'));
    }

    /**
     * Form buat kupon baru
     */
    public function create()
    {
        return view('vendor.coupons.create');
    }

    /**
     * Simpan kupon baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_order' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['code'] = strtoupper(trim($validated['code']));
            $validated['is_active'] = $request->boolean('is_active', true);

            $coupon = Coupon::create($validated);

            return FlashMessage::backSuccess('Kupon berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Error creating coupon: ' . $e->getMessage());
            return FlashMessage::backError('Gagal membuat kupon: ' . $e->getMessage());
        }
    }

    /**
     * Form edit kupon
     */
    public function edit(Coupon $coupon)
    {
        return view('vendor.coupons.edit', compact('coupon'));
    }

    /**
     * Update kupon
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_order' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['code'] = strtoupper(trim($validated['code']));
            $validated['is_active'] = $request->boolean('is_active', true);

            $coupon->update($validated);

            return FlashMessage::backSuccess('Kupon berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating coupon: ' . $e->getMessage());
            return FlashMessage::backError('Gagal memperbarui kupon: ' . $e->getMessage());
        }
    }

    /**
     * Hapus kupon
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $coupon->delete();
            return FlashMessage::backSuccess('Kupon berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting coupon: ' . $e->getMessage());
            return FlashMessage::backError('Gagal menghapus kupon: ' . $e->getMessage());
        }
    }

    /**
     * Toggle aktif/nonaktif kupon
     */
    public function toggleActive(Coupon $coupon)
    {
        try {
            $coupon->update(['is_active' => !$coupon->is_active]);
            $status = $coupon->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return FlashMessage::backSuccess("Kupon berhasil {$status}.");
        } catch (\Exception $e) {
            Log::error('Error toggling coupon: ' . $e->getMessage());
            return FlashMessage::backError('Gagal mengubah status kupon: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Concerns;

use App\Facades\Tenant;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

trait HasVendorContext
{
    /**
     * Get the current vendor from authenticated user.
     * Uses Tenant facade for consistent vendor context.
     */
    protected function getVendor(): ?Vendor
    {
        $vendorId = Tenant::getVendorId();

        if ($vendorId) {
            return Vendor::find($vendorId);
        }

        // Fallback: get vendor from user relationship
        return Auth::user()?->vendorUser()?->first();
    }

    /**
     * Get the current vendor ID.
     */
    protected function getVendorId(): ?int
    {
        return Tenant::getVendorId() ?? Auth::user()?->vendorUser()?->first()?->id;
    }

    /**
     * Require vendor context or abort.
     */
    protected function requireVendor(): Vendor
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            abort(403, 'Tidak ada vendor context yang tersedia.');
        }

        return $vendor;
    }

    /**
     * Check if given model belongs to current vendor.
     */
    protected function isOwnedByCurrentVendor($model): bool
    {
        if (!property_exists($model, 'fillable') && !method_exists($model, 'getAttribute')) {
            return false;
        }

        $vendorId = $model->vendor_id ?? null;

        if ($vendorId === null) {
            return false;
        }

        return (int) $vendorId === (int) $this->getVendorId();
    }

    /**
     * Enforce vendor ownership or abort.
     */
    protected function authorizeVendorOwnership($model): void
    {
        if (!$this->isOwnedByCurrentVendor($model)) {
            abort(403, 'Akses ditolak: data bukan milik vendor ini.');
        }
    }
}

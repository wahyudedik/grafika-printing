<?php

namespace App\Services;

use App\Facades\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthorizationService
{
    /**
     * Check if current user can access vendor data.
     */
    public function canAccessVendorData(int $vendorId): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin/Dev have full access
        if (in_array($user->usertype, ['admin', 'dev'])) {
            return true;
        }

        // Vendor can only access own data
        if ($user->usertype === 'vendor') {
            return (int) Tenant::getVendorId() === (int) $vendorId;
        }

        return false;
    }

    /**
     * Check if current user can access user data.
     */
    public function canAccessUserData(int $userId): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin/Dev have full access
        if (in_array($user->usertype, ['admin', 'dev'])) {
            return true;
        }

        // User can only access own data
        if ($user->usertype === 'user') {
            return (int) $user->id === (int) $userId;
        }

        // Vendor can access users linked to their vendor
        if ($user->usertype === 'vendor') {
            return DB::table('vendor_user')
                ->where('user_id', $userId)
                ->where('vendor_id', Tenant::getVendorId())
                ->exists();
        }

        return false;
    }

    /**
     * Check if current user is admin or dev.
     */
    public function isAdmin(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->usertype, ['admin', 'dev']);
    }

    /**
     * Check if current user is a vendor.
     */
    public function isVendor(): bool
    {
        $user = Auth::user();
        return $user && $user->usertype === 'vendor';
    }

    /**
     * Enforce vendor data access or abort.
     */
    public function authorizeVendor(int $vendorId): void
    {
        if (!$this->canAccessVendorData($vendorId)) {
            abort(403, 'Akses ditolak ke data vendor.');
        }
    }

    /**
     * Enforce user data access or abort.
     */
    public function authorizeUser(int $userId): void
    {
        if (!$this->canAccessUserData($userId)) {
            abort(403, 'Akses ditolak ke data pengguna.');
        }
    }

    /**
     * Require admin role or abort.
     */
    public function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengakses.');
        }
    }

    /**
     * Require vendor role or abort.
     */
    public function requireVendor(): void
    {
        if (!$this->isVendor()) {
            abort(403, 'Hanya vendor yang dapat mengakses.');
        }
    }
}

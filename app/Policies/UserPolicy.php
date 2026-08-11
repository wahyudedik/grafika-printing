<?php

namespace App\Policies;

use App\Models\User;
use App\Facades\Tenant;
use Illuminate\Support\Facades\DB;

class UserPolicy
{
    public function viewAny(User $authUser): bool
    {
        return in_array($authUser->usertype, ['dev', 'admin', 'vendor']);
    }

    public function view(User $authUser, User $user): bool
    {
        // Admin/Dev have full access
        if (in_array($authUser->usertype, ['dev', 'admin'])) {
            return true;
        }

        // User can view own profile
        if ($authUser->usertype === 'user') {
            return (int) $authUser->id === (int) $user->id;
        }

        // Vendor can view users linked to their vendor
        if ($authUser->usertype === 'vendor') {
            return DB::table('vendor_user')
                ->where('user_id', $user->id)
                ->where('vendor_id', Tenant::getVendorId())
                ->exists();
        }

        return false;
    }

    public function create(User $authUser): bool
    {
        return in_array($authUser->usertype, ['dev', 'admin', 'vendor']);
    }

    public function update(User $authUser, User $user): bool
    {
        // Admin/Dev can update all
        if (in_array($authUser->usertype, ['dev', 'admin'])) {
            return true;
        }

        // User can update own profile
        if ($authUser->usertype === 'user') {
            return (int) $authUser->id === (int) $user->id;
        }

        // Vendor can update users linked to their vendor
        if ($authUser->usertype === 'vendor') {
            return DB::table('vendor_user')
                ->where('user_id', $user->id)
                ->where('vendor_id', Tenant::getVendorId())
                ->exists();
        }

        return false;
    }

    public function delete(User $authUser, User $user): bool
    {
        // Admin/Dev can delete all
        if (in_array($authUser->usertype, ['dev', 'admin'])) {
            return true;
        }

        // User cannot delete others
        return false;
    }
}

<?php

namespace App\Policies;

use App\Models\Vendor\Linktree;
use App\Models\User;
use App\Facades\Tenant;

class LinktreePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin', 'vendor']);
    }

    public function view(User $user, Linktree $linktree): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        return (int) $linktree->vendor_id === (int) Tenant::getVendorId();
    }

    public function create(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin', 'vendor']);
    }

    public function update(User $user, Linktree $linktree): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        return (int) $linktree->vendor_id === (int) Tenant::getVendorId();
    }

    public function delete(User $user, Linktree $linktree): bool
    {
        return $this->update($user, $linktree);
    }

    /**
     * Public can view active linktrees.
     */
    public function viewPublic(User $user, Linktree $linktree): bool
    {
        return $linktree->is_active;
    }
}

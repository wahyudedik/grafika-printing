<?php

namespace App\Policies;

use App\Models\Vendor\Produk;
use App\Models\User;
use App\Facades\Tenant;

class ProdukPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin', 'vendor']);
    }

    public function view(User $user, Produk $produk): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        return (int) $produk->vendor_id === (int) Tenant::getVendorId();
    }

    public function create(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin', 'vendor']);
    }

    public function update(User $user, Produk $produk): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        return (int) $produk->vendor_id === (int) Tenant::getVendorId();
    }

    public function delete(User $user, Produk $produk): bool
    {
        return $this->update($user, $produk);
    }
}

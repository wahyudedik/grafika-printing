<?php

namespace App\Policies;

use App\Models\Vendor\Transaksi;
use App\Models\User;
use App\Facades\Tenant;

class TransaksiPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin', 'vendor']);
    }

    public function view(User $user, Transaksi $transaksi): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        return (int) $transaksi->vendor_id === (int) Tenant::getVendorId();
    }

    public function create(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin', 'vendor']);
    }

    public function update(User $user, Transaksi $transaksi): bool
    {
        return $this->view($user, $transaksi);
    }

    public function delete(User $user, Transaksi $transaksi): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        return (int) $transaksi->vendor_id === (int) Tenant::getVendorId()
            && in_array($transaksi->status, ['pending']);
    }
}

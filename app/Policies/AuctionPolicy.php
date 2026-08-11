<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;

class AuctionPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Public
    }

    public function view(User $user, Auction $auction): bool
    {
        // Owner can view
        if ($auction->user_id === $user->id) {
            return true;
        }

        // Admin/dev can view
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        // Winning vendor can view
        if ($user->usertype === 'vendor' && $auction->winner_vendor_id) {
            $vendor = $user->vendorUser()->first();
            return $vendor && $auction->winner_vendor_id === $vendor->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->usertype, ['user', 'dev']);
    }

    public function update(User $user, Auction $auction): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        return $auction->user_id === $user->id
            && in_array($auction->status, ['pending', 'draft']);
    }

    public function delete(User $user, Auction $auction): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        return $auction->user_id === $user->id
            && !in_array($auction->status, ['paid', 'in_production', 'completed']);
    }

    public function pay(User $user, Auction $auction): bool
    {
        return $auction->user_id === $user->id
            && $auction->status === 'active';
    }

    public function bid(User $user, Auction $auction): bool
    {
        return $user->usertype === 'vendor'
            && $auction->status === 'active'
            && $auction->deadline > now();
    }

    public function approve(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin']);
    }

    public function reject(User $user): bool
    {
        return in_array($user->usertype, ['dev', 'admin']);
    }

    public function close(User $user, Auction $auction): bool
    {
        if (in_array($user->usertype, ['dev', 'admin'])) {
            return true;
        }

        return $auction->user_id === $user->id && $auction->status === 'active';
    }
}

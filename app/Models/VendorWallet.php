<?php

namespace App\Models;

use App\Models\Vendor\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class VendorWallet extends TenantModel
{
    protected $fillable = [
        'vendor_id',
        'balance',
        'pending_balance',
        'total_earned',
        'total_withdrawn',
        'is_active'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(VendorWalletTransaction::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(VendorWithdrawal::class);
    }

    /**
     * Add credit to wallet
     */
    public function addCredit($amount, $category, $description = null, $referenceId = null, $referenceType = null, $metadata = null)
    {
        return DB::transaction(function () use ($amount, $category, $description, $referenceId, $referenceType, $metadata) {
            $balanceBefore = $this->balance;
            $balanceAfter = $balanceBefore + $amount;

            // Update wallet balance
            $this->update([
                'balance' => $balanceAfter,
                'total_earned' => $this->total_earned + $amount
            ]);

            // Create transaction record
            $transaction = $this->transactions()->create([
                'vendor_id' => $this->vendor_id,
                'transaction_code' => 'CREDIT-' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                'type' => 'credit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'status' => 'completed',
                'metadata' => $metadata
            ]);

            return $transaction;
        });
    }

    /**
     * Add debit to wallet
     */
    public function addDebit($amount, $category, $description = null, $referenceId = null, $referenceType = null, $metadata = null)
    {
        return DB::transaction(function () use ($amount, $category, $description, $referenceId, $referenceType, $metadata) {
            if ($this->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            $balanceBefore = $this->balance;
            $balanceAfter = $balanceBefore - $amount;

            // Update wallet balance
            $this->update([
                'balance' => $balanceAfter
            ]);

            // Create transaction record
            $transaction = $this->transactions()->create([
                'vendor_id' => $this->vendor_id,
                'transaction_code' => 'DEBIT-' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                'type' => 'debit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'status' => 'completed',
                'metadata' => $metadata
            ]);

            return $transaction;
        });
    }

    /**
     * Get available balance (balance - pending withdrawals)
     */
    public function getAvailableBalanceAttribute()
    {
        $pendingWithdrawals = $this->withdrawals()
            ->whereIn('status', ['pending', 'approved', 'processing'])
            ->sum('amount');

        return $this->balance - $pendingWithdrawals;
    }

    /**
     * Check if wallet has sufficient balance
     */
    public function hasSufficientBalance($amount)
    {
        return $this->available_balance >= $amount;
    }

    /**
     * Get or create wallet for vendor
     */
    public static function getOrCreate($vendorId): VendorWallet
    {
        return static::firstOrCreate(
            ['vendor_id' => $vendorId],
            [
                'balance' => 0,
                'pending_balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
                'is_active' => true
            ]
        );
    }
}

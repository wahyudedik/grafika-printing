<?php

namespace App\Models;

use App\Models\Vendor\TenantModel;
use App\Services\EncryptionService;
use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class VendorWithdrawal extends TenantModel
{
    protected $fillable = [
        'vendor_id',
        'vendor_wallet_id',
        'withdrawal_code',
        'batch_id',
        'amount',
        'fee',
        'net_amount',
        'status',
        'method',
        'account_number',
        'account_name',
        'bank_name',
        'notes',
        'admin_notes',
        'processed_by',
        'processed_at',
        'completed_at',
        'payment_proof',
        'webhook_data'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'payment_proof' => 'array',
        'webhook_data' => 'array',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    protected $hidden = [
        'account_number',
        'account_name',
        'bank_name'
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function vendorWallet(): BelongsTo
    {
        return $this->belongsTo(VendorWallet::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Create withdrawal request
     */
    public static function createRequest($vendorId, $amount, $method, $accountNumber, $accountName, $bankName = null, $notes = null)
    {
        return DB::transaction(function () use ($vendorId, $amount, $method, $accountNumber, $accountName, $bankName, $notes) {
            $wallet = VendorWallet::getOrCreate($vendorId);

            // Check if vendor has sufficient balance
            if (!$wallet->hasSufficientBalance($amount)) {
                throw new \Exception('Saldo tidak mencukupi');
            }

            // Calculate fee (you can customize this logic)
            $fee = static::calculateFee($amount, $method);
            $netAmount = $amount - $fee;

            // Create withdrawal request
            $withdrawal = static::create([
                'vendor_id' => $vendorId,
                'vendor_wallet_id' => $wallet->id,
                'withdrawal_code' => 'WD-' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $netAmount,
                'status' => 'pending',
                'method' => $method,
                'account_number' => $accountNumber,
                'account_name' => $accountName,
                'bank_name' => $bankName,
                'notes' => $notes
            ]);

            return $withdrawal;
        });
    }

    /**
     * Calculate withdrawal fee
     */
    public static function calculateFee($amount, $method)
    {
        // Customize fee calculation based on method
        switch ($method) {
            case 'bank_transfer':
                return min(5000, $amount * 0.01); // 1% or max 5000
            case 'e_wallet':
                return min(3000, $amount * 0.005); // 0.5% or max 3000
            case 'cash':
                return 0; // No fee for cash
            default:
                return 0;
        }
    }

    /**
     * Approve withdrawal
     */
    public function approve($adminId, $adminNotes = null)
    {
        return DB::transaction(function () use ($adminId, $adminNotes) {
            if ($this->status !== 'pending') {
                throw new \Exception('Withdrawal request is not pending');
            }

            $this->update([
                'status' => 'approved',
                'processed_by' => $adminId,
                'processed_at' => now(),
                'admin_notes' => $adminNotes
            ]);

            // Log audit trail
            AuditLogService::logWithdrawal($this, 'approve');

            return $this;
        });
    }

    /**
     * Reject withdrawal
     */
    public function reject($adminId, $adminNotes = null)
    {
        return DB::transaction(function () use ($adminId, $adminNotes) {
            if ($this->status !== 'pending') {
                throw new \Exception('Withdrawal request is not pending');
            }

            $this->update([
                'status' => 'rejected',
                'processed_by' => $adminId,
                'processed_at' => now(),
                'admin_notes' => $adminNotes
            ]);

            // Log audit trail
            AuditLogService::logWithdrawal($this, 'reject');

            return $this;
        });
    }

    /**
     * Complete withdrawal
     */
    public function complete($paymentProof = null)
    {
        return DB::transaction(function () use ($paymentProof) {
            if ($this->status !== 'approved') {
                throw new \Exception('Withdrawal request is not approved');
            }

            // Deduct from wallet
            $this->vendorWallet->addDebit(
                $this->amount,
                'withdrawal',
                'Penarikan dana - ' . $this->withdrawal_code,
                $this->id,
                'withdrawal'
            );

            // Update total withdrawn
            $this->vendorWallet->update([
                'total_withdrawn' => $this->vendorWallet->total_withdrawn + $this->amount
            ]);

            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
                'payment_proof' => $paymentProof
            ]);

            // Log audit trail
            AuditLogService::logWithdrawal($this, 'complete');

            return $this;
        });
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'processing' => 'Diproses',
            'completed' => 'Selesai',
            'failed' => 'Gagal'
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status color class
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'processing' => 'primary',
            'completed' => 'success',
            'failed' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get method label
     */
    public function getMethodLabelAttribute()
    {
        $labels = [
            'bank_transfer' => 'Transfer Bank',
            'e_wallet' => 'E-Wallet',
            'cash' => 'Tunai'
        ];

        return $labels[$this->method] ?? ucfirst(str_replace('_', ' ', $this->method));
    }

    /**
     * Encrypt sensitive data before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($withdrawal) {
            // Encrypt sensitive data
            if ($withdrawal->account_number) {
                $withdrawal->account_number = EncryptionService::encryptFinancialData($withdrawal->account_number);
            }
            if ($withdrawal->account_name) {
                $withdrawal->account_name = EncryptionService::encryptFinancialData($withdrawal->account_name);
            }
            if ($withdrawal->bank_name) {
                $withdrawal->bank_name = EncryptionService::encryptFinancialData($withdrawal->bank_name);
            }
        });

        static::saved(function ($withdrawal) {
            // Log audit trail
            AuditLogService::logWithdrawal($withdrawal, 'create');
        });

        static::updated(function ($withdrawal) {
            // Log audit trail for updates
            AuditLogService::logWithdrawal($withdrawal, 'update', $withdrawal->getOriginal());
        });
    }

    /**
     * Decrypt sensitive data for display
     */
    public function getDecryptedAccountNumberAttribute()
    {
        if (!$this->account_number) return null;

        try {
            return EncryptionService::decryptFinancialData($this->account_number);
        } catch (\Exception $e) {
            return EncryptionService::maskSensitiveData($this->account_number);
        }
    }

    public function getDecryptedAccountNameAttribute()
    {
        if (!$this->account_name) return null;

        try {
            return EncryptionService::decryptFinancialData($this->account_name);
        } catch (\Exception $e) {
            return EncryptionService::maskSensitiveData($this->account_name);
        }
    }

    public function getDecryptedBankNameAttribute()
    {
        if (!$this->bank_name) return null;

        try {
            return EncryptionService::decryptFinancialData($this->bank_name);
        } catch (\Exception $e) {
            return EncryptionService::maskSensitiveData($this->bank_name);
        }
    }

    /**
     * Get masked account number for display
     */
    public function getMaskedAccountNumberAttribute()
    {
        if (!$this->account_number) return null;
        return EncryptionService::maskSensitiveData($this->account_number);
    }

    /**
     * Get masked account name for display
     */
    public function getMaskedAccountNameAttribute()
    {
        if (!$this->account_name) return null;
        return EncryptionService::maskSensitiveData($this->account_name);
    }

    /**
     * Get masked bank name for display
     */
    public function getMaskedBankNameAttribute()
    {
        if (!$this->bank_name) return null;
        return EncryptionService::maskSensitiveData($this->bank_name);
    }
}

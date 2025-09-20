<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'action_type',
        'entity_type',
        'entity_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'transaction_reference',
        'amount',
        'status',
        'notes',
        'risk_level'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Action types
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_APPROVE = 'approve';
    const ACTION_REJECT = 'reject';
    const ACTION_WITHDRAW = 'withdraw';
    const ACTION_DEPOSIT = 'deposit';
    const ACTION_TRANSFER = 'transfer';

    // Entity types
    const ENTITY_WITHDRAWAL = 'withdrawal';
    const ENTITY_WALLET = 'wallet';
    const ENTITY_PAYMENT = 'payment';
    const ENTITY_AUCTION = 'auction';
    const ENTITY_ADMIN_FEE = 'admin_fee';

    // Risk levels
    const RISK_LOW = 'low';
    const RISK_MEDIUM = 'medium';
    const RISK_HIGH = 'high';
    const RISK_CRITICAL = 'critical';

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor related to this log
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Scope for high-risk transactions
     */
    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', [self::RISK_HIGH, self::RISK_CRITICAL]);
    }

    /**
     * Scope for financial actions
     */
    public function scopeFinancialActions($query)
    {
        return $query->whereIn('action_type', [
            self::ACTION_WITHDRAW,
            self::ACTION_DEPOSIT,
            self::ACTION_TRANSFER,
            self::ACTION_APPROVE,
            self::ACTION_REJECT
        ]);
    }

    /**
     * Scope for vendor-specific logs
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope for admin logs
     */
    public function scopeAdminLogs($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('usertype', 'dev');
        });
    }

    /**
     * Get masked sensitive data for display
     */
    public function getMaskedOldDataAttribute()
    {
        if (!$this->old_data) return null;

        $masked = $this->old_data;
        $sensitiveFields = ['account_number', 'account_name', 'bank_name', 'ewallet_number'];

        foreach ($sensitiveFields as $field) {
            if (isset($masked[$field])) {
                $masked[$field] = $this->maskSensitiveData($masked[$field]);
            }
        }

        return $masked;
    }

    /**
     * Get masked new data for display
     */
    public function getMaskedNewDataAttribute()
    {
        if (!$this->new_data) return null;

        $masked = $this->new_data;
        $sensitiveFields = ['account_number', 'account_name', 'bank_name', 'ewallet_number'];

        foreach ($sensitiveFields as $field) {
            if (isset($masked[$field])) {
                $masked[$field] = $this->maskSensitiveData($masked[$field]);
            }
        }

        return $masked;
    }

    /**
     * Mask sensitive data
     */
    private function maskSensitiveData($data)
    {
        if (empty($data)) return $data;

        $length = strlen($data);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return substr($data, 0, 4) . str_repeat('*', $length - 4);
    }

    /**
     * Create audit log entry
     */
    public static function createLog($data)
    {
        return self::create(array_merge($data, [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]));
    }
}

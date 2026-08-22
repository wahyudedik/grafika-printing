<?php

namespace App\Models\Vendor;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionVoidLog extends TenantModel
{
    use HasFactory;

    protected $table = 'transaction_void_logs';

    protected $fillable = [
        'vendor_id',
        'transaksi_id',
        'user_id',
        'action',
        'reason',
        'old_data',
        'new_data',
        'refund_amount',
        'stock_restored',
        'refund_processed',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'refund_amount' => 'decimal:2',
        'stock_restored' => 'boolean',
        'refund_processed' => 'boolean',
    ];

    /**
     * Relationship: Transaksi (transaction) associated with this void log.
     */
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    /**
     * Relationship: User who performed the void action.
     */
    public function voidedByUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

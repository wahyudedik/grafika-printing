<?php

namespace App\Models;

use App\Models\User\UserTenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediationRequest extends UserTenantModel
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'vendor_id',
        'user_id',
        'requested_by',
        'reason',
        'description',
        'status',
        'admin_notes',
        'resolution',
        'resolved_at',
        'resolved_by',
        'evidence_files',
        'admin_decision',
        'compensation_amount',
        'penalty_amount'
    ];

    protected $casts = [
        'evidence_files' => 'array',
        'resolved_at' => 'datetime',
        'compensation_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2'
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    // Admin Decision Constants
    const DECISION_FAVOR_USER = 'favor_user';
    const DECISION_FAVOR_VENDOR = 'favor_vendor';
    const DECISION_COMPROMISE = 'compromise';
    const DECISION_NO_FAULT = 'no_fault';

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_IN_REVIEW => 'Under Review',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_IN_REVIEW => 'blue',
            self::STATUS_RESOLVED => 'green',
            self::STATUS_CLOSED => 'gray',
            default => 'gray'
        };
    }

    public function getDecisionLabelAttribute()
    {
        return match ($this->admin_decision) {
            self::DECISION_FAVOR_USER => 'Favor User',
            self::DECISION_FAVOR_VENDOR => 'Favor Vendor',
            self::DECISION_COMPROMISE => 'Compromise',
            self::DECISION_NO_FAULT => 'No Fault',
            default => 'Pending'
        };
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isResolved()
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function resolve($adminId, $decision, $resolution, $compensationAmount = 0, $penaltyAmount = 0)
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
            'resolved_by' => $adminId,
            'admin_decision' => $decision,
            'resolution' => $resolution,
            'compensation_amount' => $compensationAmount,
            'penalty_amount' => $penaltyAmount
        ]);

        // Update order tracking
        $this->auction->orderTracking()->update([
            'status' => OrderTracking::STATUS_COMPLETED,
            'mediation_status' => 'resolved',
            'mediation_resolution' => $resolution
        ]);
    }

    public function close()
    {
        $this->update([
            'status' => self::STATUS_CLOSED
        ]);
    }
}

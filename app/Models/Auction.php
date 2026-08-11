<?php

namespace App\Models;

use App\Models\User\UserTenantModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends UserTenantModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'kode',
        'title',
        'description',
        'category',
        'quantity',
        'budget',
        'admin_fee_amount',
        'payment_gateway_fee',
        'total_amount_with_fees',
        'vendor_receives',
        'admin_receives',
        'fee_breakdown',
        'fees_calculated',
        'deadline',
        'file_path',
        'status',
        'winner_vendor_id',
        'winning_bid',
        'specifications',
        'alamat_pengiriman',
        'no_telp',
        'email_pengiriman',
        'catatan_khusus',
        'metode_pembayaran',
        'estimasi_selesai',
        'progress_percentage',
        'catatan_vendor',
        'transaksi_id',
        'pos_integrated',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'approved_by',
        'approved_at',
        'admin_approval_status',
        'admin_approval_date',
        'admin_approval_notes',
        'delivery_status',
        'tracking_number',
        'shipping_cost',
        'user_rating',
        'user_feedback',
        'completion_date'
    ];

    protected $casts = [
        'deadline' => 'date',
        'budget' => 'decimal:2',
        'admin_fee_amount' => 'decimal:2',
        'payment_gateway_fee' => 'decimal:2',
        'total_amount_with_fees' => 'decimal:2',
        'vendor_receives' => 'decimal:2',
        'admin_receives' => 'decimal:2',
        'fee_breakdown' => 'array',
        'fees_calculated' => 'boolean',
        'winning_bid' => 'decimal:2',
        'quantity' => 'integer',
        'estimasi_selesai' => 'datetime',
        'progress_percentage' => 'integer',
        'pos_integrated' => 'boolean',
        'rejected_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function winnerVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'winner_vendor_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Vendor\Transaksi::class, 'transaksi_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->deadline > now();
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed' || $this->deadline <= now();
    }

    public function getLowestBid()
    {
        return $this->bids()->where('status', 'pending')->min('bid_amount');
    }

    public function getBidCount(): int
    {
        return $this->bids()->where('status', 'pending')->count();
    }

    /**
     * Relasi dengan XenditPayment
     */
    public function xenditPayments()
    {
        return $this->hasMany(XenditPayment::class);
    }

    /**
     * Get the latest payment for this auction
     */
    public function latestPayment()
    {
        return $this->hasOne(XenditPayment::class)->latest();
    }

    /**
     * Relasi dengan ShippingInvoice
     */
    public function shippingInvoice()
    {
        return $this->hasOne(ShippingInvoice::class);
    }

    public function deliveryConfirmation()
    {
        return $this->hasOne(DeliveryConfirmation::class);
    }

    public function hasDeliveryConfirmation(): bool
    {
        return $this->deliveryConfirmation()->exists();
    }

    public function isDeliveryConfirmed(): bool
    {
        return $this->deliveryConfirmation && $this->deliveryConfirmation->isConfirmed();
    }

    // Admin Approval Methods
    public function isPendingApproval(): bool
    {
        return $this->admin_approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->admin_approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->admin_approval_status === 'rejected';
    }

    public function approve($adminId, $notes = null)
    {
        $this->update([
            'admin_approval_status' => 'approved',
            'admin_approval_date' => now(),
            'admin_approval_notes' => $notes,
            'approved_by' => $adminId,
            'status' => 'active'
        ]);
    }

    public function reject($adminId, $reason)
    {
        $this->update([
            'admin_approval_status' => 'rejected',
            'admin_approval_date' => now(),
            'admin_approval_notes' => $reason,
            'approved_by' => $adminId,
            'status' => 'rejected'
        ]);
    }

    // Delivery Methods
    public function isShipped(): bool
    {
        return $this->delivery_status === 'shipped';
    }

    public function isDelivered(): bool
    {
        return $this->delivery_status === 'delivered';
    }

    public function isCompleted(): bool
    {
        return $this->delivery_status === 'completed';
    }

    public function markAsShipped($trackingNumber, $shippingCost = null)
    {
        $this->update([
            'delivery_status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'shipping_cost' => $shippingCost
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'delivery_status' => 'delivered'
        ]);
    }

    public function complete($rating = null, $feedback = null)
    {
        $this->update([
            'delivery_status' => 'completed',
            'user_rating' => $rating,
            'user_feedback' => $feedback,
            'completion_date' => now(),
            'status' => 'completed'
        ]);

        // Transfer money to vendor wallet
        if ($this->winner_vendor_id && $this->winning_bid) {
            $vendor = Vendor::find($this->winner_vendor_id);
            if ($vendor) {
                $wallet = $vendor->getOrCreateWallet();
                $wallet->addCredit(
                    $this->winning_bid,
                    'auction_payment',
                    "Payment for auction: {$this->title}",
                    $this->id,
                    'auction'
                );
            }
        }
    }

    // Scopes
    public function scopePendingApproval($query)
    {
        return $query->where('admin_approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('admin_approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('admin_approval_status', 'rejected');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('delivery_status', 'completed');
    }
}

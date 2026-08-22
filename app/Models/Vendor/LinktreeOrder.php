<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LinktreeOrder extends TenantModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'linktree_id',
        'linktree_product_id',
        'produk_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'selected_specs',
        'notes',
        'quantity',
        'total_price',
        'status',
        'payment_status',
        'payment_proof',
        'vendor_notes',
        'whatsapp_message',
        'whatsapp_sent',
    ];

    protected $casts = [
        'selected_specs' => 'array',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'whatsapp_sent' => 'boolean',
    ];

    // Auto-generate UUID on creating
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function linktree()
    {
        return $this->belongsTo(Linktree::class);
    }

    public function linktreeProduct()
    {
        return $this->belongsTo(LinktreeProduct::class);
    }

    public function produk()
    {
        return $this->belongsTo(\App\Models\Vendor\Produk::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'purple',
            'shipped' => 'indigo',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'Belum Bayar',
            'proof_sent' => 'Bukti Dikirim',
            'confirmed' => 'Pembayaran Dikonfirmasi',
            'rejected' => 'Pembayaran Ditolak',
            default => $this->payment_status,
        };
    }

    public function getSelectedSpecsTextAttribute(): string
    {
        if (empty($this->selected_specs)) return '-';

        return collect($this->selected_specs)
            ->map(fn($spec) => ($spec['nama'] ?? $spec['name'] ?? '') . ': ' . ($spec['value'] ?? ''))
            ->implode(' | ');
    }

    // Generate WhatsApp message
    public function generateWhatsAppMessage(): string
    {
        $produk = $this->produk;
        $specs = $this->selected_specs_text;

        $message = "🛒 *Pesanan Linktree*\n\n";
        $message .= "📦 Produk: {$produk->nama_produk}\n";
        $message .= "📝 Spesifikasi: {$specs}\n";
        $message .= "🔢 Jumlah: {$this->quantity}\n";

        if ($this->total_price) {
            $message .= "💰 Total: Rp " . number_format($this->total_price, 0, ',', '.') . "\n";
        }

        if ($this->notes) {
            $message .= "💬 Catatan: {$this->notes}\n";
        }

        $message .= "\n📋 No. Order: {$this->uuid}\n";
        $message .= "\n Mohon kirimkan bukti pembayaran setelah transfer. Terima kasih! 🙏";

        return $message;
    }

    // Get WhatsApp URL
    public function getWhatsAppUrl(): string
    {
        $vendor = $this->vendor;
        $phone = $vendor->phone ?? $vendor->whatsapp ?? '';
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) > 0 && substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $message = urlencode($this->generateWhatsAppMessage());

        return "https://wa.me/{$phone}?text={$message}";
    }
}

<?php

namespace App\Models\Vendor;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Pelanggan;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor\TransaksiItem;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\OrderStatusChanged;
use App\Models\Vendor\TransaksiItemSpecifications;

class Transaksi extends Model
{
    protected $table = 'transaksis';

    // Add eager loading by default
    protected $with =
    ['pelanggan', 'transaksiItem.produk', 'vendor'];
    
    protected $fillable = [
        'vendor_id',
        'kode',
        'user_id',
        'pelanggan_id',
        'total_harga',
        'status',
        'payment_method',
        'estimasi_selesai',
        'tanggal_dibuat',
        'progress_percentage',
        'catatan'
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'status' => 'string',
        'tanggal_dibuat' => 'date'
    ];
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function transaksiItem()
    {
        return $this->hasMany(TransaksiItem::class, 'transaksi_id');
    }

    public function transaksiItemSpecifications()
    {
        return $this->hasManyThrough(
            TransaksiItemSpecifications::class,
            TransaksiItem::class,
            'transaksi_id',
            'transaksi_item_id'
        );
    }

    protected static function booted()
    {
        static::creating(function ($transaksi) {
            // Verify pelanggan exists before creating transaction
            if (!Pelanggan::find($transaksi->pelanggan_id)) {
                throw new \Exception('Customer not found');
            }
        });

        static::created(function ($transaksi) {
            // Load relationships before notification
            $transaksi->load(['pelanggan', 'transaksiItem.produk', 'vendor']);
            if ($transaksi->pelanggan) {
                $transaksi->pelanggan->notify(new OrderStatusChanged($transaksi));
            }
        });
    }

    public function updateOrderStatus($status)
    {
        $progressMap = [
            'pending' => 0,
            'processing' => 25,
            'quality_check' => 80,
            'completed' => 100,
            'cancelled' => 0
        ];

        DB::transaction(function () use ($status, $progressMap) {
            $this->forceFill([
                'status' => $status,
                'progress_percentage' => $progressMap[$status]
            ])->save();

            $this->refresh();
            sleep(1);
            $this->pelanggan->notify(new OrderStatusChanged($this));
        });
    }
}

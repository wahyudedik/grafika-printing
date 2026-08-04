<?php

namespace App\Models\Vendor;

use App\Models\Vendor\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinktreeProduct extends TenantModel
{
    protected $table = 'linktree_products';

    protected $fillable = [
        'linktree_id',
        'produk_id',
        'sort_order',
        'is_active',
        'custom_price',
        'custom_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship: Linktree yang memiliki produk ini
     */
    public function linktree(): BelongsTo
    {
        return $this->belongsTo(Linktree::class, 'linktree_id');
    }

    /**
     * Relationship: Produk yang ditampilkan
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Vendor\Produk::class, 'produk_id');
    }

    /**
     * Cek apakah produk aktif
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Get harga tampilan (custom_price atau dari produk)
     */
    public function getDisplayPriceAttribute(): ?string
    {
        if ($this->custom_price) {
            return $this->custom_price;
        }

        if ($this->produk && isset($this->produk->harga_dasar)) {
            return 'Rp ' . number_format((float) $this->produk->harga_dasar, 0, ',', '.');
        }

        return null;
    }

    /**
     * Get deskripsi tampilan (custom_description atau dari produk)
     */
    public function getDisplayDescriptionAttribute(): ?string
    {
        if ($this->custom_description) {
            return $this->custom_description;
        }

        return $this->produk?->deskripsi;
    }

    /**
     * Get gambar produk (ambil gambar pertama)
     */
    public function getDisplayImageAttribute(): ?string
    {
        if ($this->produk && is_array($this->produk->gambar) && count($this->produk->gambar) > 0) {
            return $this->produk->gambar[0];
        }

        return null;
    }

    /**
     * Get nama produk
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->produk?->nama_produk ?? 'Produk';
    }

    /**
     * Scope: hanya produk aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: urutkan berdasarkan sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}

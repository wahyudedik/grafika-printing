<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Vendor\TenantModel;

class LinktreeProduct extends TenantModel
{
    protected $table = 'linktree_products';

    protected $fillable = [
        'vendor_id',
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
     * Relationship: Vendor yang memiliki linktree produk ini
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Vendor::class, 'vendor_id');
    }

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
     * Get ringkasan spesifikasi produk
     * Contoh: "Bahan: Kertas Art Carton | Ukuran: A4 | Finishing: Laminasi"
     */
    public function getSpesifikasiSummaryAttribute(): string
    {
        if (!$this->produk) return '-';

        $specs = $this->produk->spesifikasiProduk()->with(['spesifikasi', 'bahanSpesifikasiProduk'])->get();

        if ($specs->isEmpty()) return '-';

        return $specs->map(function ($spec) {
            $nama = $spec->spesifikasi->nama_spesifikasi ?? '-';
            $pilihan = $spec->pilihan ?? [];
            $value = is_array($pilihan) && count($pilihan) > 0
                ? implode(', ', $pilihan)
                : '-';

            return "{$nama}: {$value}";
        })->implode(' | ');
    }

    /**
     * Get nama kategori dari produk
     */
    public function getKategoriNameAttribute(): string
    {
        return $this->produk?->kategori?->nama_kategori ?? '-';
    }

    /**
     * Get daftar bahan yang tersedia untuk produk ini (collection of nama_bahan)
     */
    public function getBahansListAttribute()
    {
        if (!$this->produk) return collect();

        $bahans = collect();
        $this->produk->spesifikasiProduk()->with('bahanSpesifikasiProduk')->each(function ($spec) use (&$bahans) {
            $spec->bahanSpesifikasiProduk->each(function ($bahan) use (&$bahans) {
                $bahans->push($bahan->nama_bahan);
            });
        });

        return $bahans->unique()->values();
    }

    /**
     * Get array lengkap spesifikasi dengan nama, tipe_input, satuan, pilihan
     */
    public function getFullSpecsAttribute(): array
    {
        if (!$this->produk) return [];

        return $this->produk->spesifikasiProduk()->with(['spesifikasi', 'bahanSpesifikasiProduk'])->get()
            ->map(function ($spec) {
                return [
                    'nama' => $spec->spesifikasi->nama_spesifikasi ?? '-',
                    'tipe_input' => $spec->spesifikasi->tipe_input ?? '-',
                    'satuan' => $spec->spesifikasi->satuan ?? null,
                    'pilihan' => $spec->pilihan ?? [],
                    'wajib_diisi' => $spec->wajib_diisi ?? false,
                    'bahans' => $spec->bahanSpesifikasiProduk->map(fn($b) => [
                        'id' => $b->id,
                        'nama' => $b->nama_bahan,
                        'hpp' => $b->hpp,
                    ])->toArray(),
                ];
            })
            ->toArray();
    }

    /**
     * Load spesifikasiProduk dengan relasi spesifikasi dan bahanSpesifikasiProduk
     */
    public function loadFullSpecs(): self
    {
        if ($this->produk) {
            $this->produk->load('spesifikasiProduk.spesifikasi', 'spesifikasiProduk.bahanSpesifikasiProduk');
        }

        return $this;
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

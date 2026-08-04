<?php

namespace App\Models\Vendor;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;

class PrinterSetting extends TenantModel
{
    protected $table = 'printer_settings';

    protected $fillable = [
        'vendor_id',
        'paper_width',
        'font_size',
        'margin',
        'auto_print',
        'auto_cut',
        'auto_close_window',
        'print_delay',
        'printer_name',
        'is_active',
    ];

    protected $casts = [
        'auto_print' => 'boolean',
        'auto_cut' => 'boolean',
        'auto_close_window' => 'boolean',
        'is_active' => 'boolean',
        'font_size' => 'integer',
        'print_delay' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get or create printer settings for a vendor
     */
    public static function forVendor(int $vendorId): static
    {
        return static::firstOrCreate(
            ['vendor_id' => $vendorId],
            [
                'paper_width' => '80mm',
                'font_size' => 12,
                'margin' => '0mm',
                'auto_print' => true,
                'auto_cut' => true,
                'auto_close_window' => true,
                'print_delay' => 500,
                'is_active' => true,
            ]
        );
    }

    /**
     * Get paper width in mm as integer
     */
    public function getPaperWidthMm(): int
    {
        return (int) str_replace('mm', '', $this->paper_width);
    }

    /**
     * Get CSS width for the paper
     */
    public function getCssWidth(): string
    {
        return $this->paper_width;
    }
}

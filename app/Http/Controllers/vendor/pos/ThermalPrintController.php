<?php

namespace App\Http\Controllers\vendor\pos;

use App\Http\Responses\FlashMessage;

use App\Models\Vendor\Transaksi;
use App\Models\Vendor\PrinterSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Concerns\HasVendorContext;



class ThermalPrintController extends Controller
{
    use HasVendorContext;


    /**
     * Direct thermal printing with browser print dialog
     */
    public function printDirect(Transaksi $transaksi)
    {
        try {
            $vendor = $this->requireVendor();

            if (!$vendor) {
                return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan.');
            }

            $transaksi = Transaksi::with([
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan',
                'pelanggan',
                'vendor'
            ])->where('vendor_id', $vendor->id)
                ->findOrFail($transaksi->id);

            // Get printer settings for this vendor
            $printerSettings = PrinterSetting::forVendor($vendor->id);

            return view('pos.thermal-print', compact('transaksi', 'printerSettings') + ['method' => 'html']);
        } catch (\Exception $e) {
            Log::error('Thermal print error: ' . $e->getMessage());
            return FlashMessage::backError('Gagal mencetak: ' . $e->getMessage());
        }
    }

    /**
     * Print via JavaScript with WebUSB support and printer selection
     */
    public function printViaJS(Transaksi $transaksi)
    {
        try {
            $vendor = $this->requireVendor();

            if (!$vendor) {
                return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan.');
            }

            $transaksi = Transaksi::with([
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan',
                'pelanggan',
                'vendor'
            ])->where('vendor_id', $vendor->id)
                ->findOrFail($transaksi->id);

            // Get printer settings for this vendor
            $printerSettings = PrinterSetting::forVendor($vendor->id);

            return view('pos.thermal-print', compact('transaksi', 'printerSettings') + ['method' => 'js']);
        } catch (\Exception $e) {
            Log::error('Thermal print JS error: ' . $e->getMessage());
            return FlashMessage::backError('Gagal mencetak: ' . $e->getMessage());
        }
    }

    /**
     * Show printer settings page
     */
    public function showSettings()
    {
        try {
            $vendor = $this->requireVendor();

            if (!$vendor) {
                return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan.');
            }

            $printerSettings = PrinterSetting::forVendor($vendor->id);

            return view('pos.printer-settings', compact('printerSettings', 'vendor'));
        } catch (\Exception $e) {
            Log::error('Printer settings error: ' . $e->getMessage());
            return FlashMessage::backError('Gagal membuka pengaturan printer.');
        }
    }

    /**
     * Save printer settings
     */
    public function saveSettings(Request $request)
    {
        try {
            $vendor = $this->requireVendor();

            if (!$vendor) {
                return FlashMessage::error(redirect()->route('vendor.dashboard'), 'Vendor tidak ditemukan.');
            }

            $validated = $request->validate([
                'paper_width' => 'required|in:58mm,80mm',
                'font_size' => 'required|integer|min:8|max:20',
                'margin' => 'required|string|max:10',
                'auto_print' => 'boolean',
                'auto_cut' => 'boolean',
                'auto_close_window' => 'boolean',
                'print_delay' => 'integer|min:0|max:5000',
                'printer_name' => 'nullable|string|max:255',
            ]);

            // Set boolean defaults
            $validated['auto_print'] = $request->boolean('auto_print');
            $validated['auto_cut'] = $request->boolean('auto_cut');
            $validated['auto_close_window'] = $request->boolean('auto_close_window');

            $printerSettings = PrinterSetting::forVendor($vendor->id);
            $printerSettings->update($validated);

            return FlashMessage::success(redirect()->route('vendor.pos.printer.settings'), 'Pengaturan printer berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('Save printer settings error: ' . $e->getMessage());
            return FlashMessage::backError('Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Get printer settings as JSON (for AJAX)
     */
    public function getPrinterSettings()
    {
        try {
            $vendor = $this->requireVendor();

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor tidak ditemukan.'
                ], 404);
            }

            $settings = PrinterSetting::forVendor($vendor->id);

            return response()->json([
                'success' => true,
                'settings' => [
                    'paper_width' => $settings->paper_width,
                    'font_size' => $settings->font_size,
                    'margin' => $settings->margin,
                    'auto_print' => $settings->auto_print,
                    'auto_cut' => $settings->auto_cut,
                    'auto_close_window' => $settings->auto_close_window,
                    'print_delay' => $settings->print_delay,
                    'printer_name' => $settings->printer_name,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get printer settings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil pengaturan.'
            ], 500);
        }
    }
}

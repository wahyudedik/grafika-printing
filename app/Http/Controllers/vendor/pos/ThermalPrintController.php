<?php

namespace App\Http\Controllers\vendor\pos;

use App\Models\Vendor\Transaksi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ThermalPrintController extends Controller
{
    /**
     * Direct thermal printing without PDF download
     */
    public function printDirect(Transaksi $transaksi)
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = Auth::user()->vendorUser->first();

            $transaksi = Transaksi::with([
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan',
                'pelanggan',
                'vendor'
            ])->where('vendor_id', $vendor->id)
                ->findOrFail($transaksi->id);

            // Return thermal printer optimized view
            return view('pos.thermal-print', compact('transaksi'));
        } catch (\Exception $e) {
            Log::error('Thermal print error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to print: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print via JavaScript (direct browser printing)
     */
    public function printViaJS(Transaksi $transaksi)
    {
        try {
            // Dapatkan vendor dari user yang sedang login
            $vendor = Auth::user()->vendorUser->first();

            $transaksi = Transaksi::with([
                'transaksiItem.produk',
                'transaksiItem.transaksiItemSpecifications.spesifikasiProduk.spesifikasi',
                'transaksiItem.transaksiItemSpecifications.bahan',
                'pelanggan',
                'vendor'
            ])->where('vendor_id', $vendor->id)
                ->findOrFail($transaksi->id);

            // Return JavaScript print view
            return view('pos.thermal-print-js', compact('transaksi'));
        } catch (\Exception $e) {
            Log::error('Thermal print JS error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to print: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get thermal printer settings
     */
    public function getPrinterSettings()
    {
        return response()->json([
            'success' => true,
            'settings' => [
                'paper_width' => '80mm',
                'font_size' => '12px',
                'margin' => '0mm',
                'auto_cut' => true,
                'auto_open_cash_drawer' => false
            ]
        ]);
    }
}

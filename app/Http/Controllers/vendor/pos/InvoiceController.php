<?php

namespace App\Http\Controllers\vendor\pos;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Vendor\Transaksi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function show(Transaksi $transaksi)
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

            return view('pos.show', compact('transaksi'));
        } catch (\Exception $e) {
            Log::error('Error showing invoice: ' . $e->getMessage());
            return redirect()->route('pos.index')
                ->with('toast_error', 'Failed to display invoice: ' . $e->getMessage());
        }
    }

    public function download(Transaksi $transaksi)
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

            $pdf = Pdf::loadView('pos.print-invoice', compact('transaksi'));
            return $pdf->download("invoice-{$transaksi->kode}.pdf");
        } catch (\Exception $e) {
            Log::error('Error downloading invoice: ' . $e->getMessage());
            return redirect()->route('pos.index')
                ->with('toast_error', 'Failed to download invoice: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Vendor\Transaksi;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderTrackingController extends Controller
{
    /**
     * Display order tracking for user
     */
    public function index()
    {
        $user = Auth::user();

        // Get auctions where user is the creator and has been won
        $auctions = Auction::where('user_id', $user->id)
            ->where('status', 'closed')
            ->whereNotNull('transaksi_id')
            ->with(['transaksi', 'winnerVendor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.tracking.index', compact('auctions'));
    }

    /**
     * Display order tracking for vendor
     */
    public function vendorIndex()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            abort(403, 'Anda tidak memiliki akses vendor');
        }

        // Get transactions from auctions where this vendor is the winner
        $transaksis = \App\Models\Vendor\Transaksi::where('vendor_id', $vendor->id)
            ->whereNotNull('auction_id')
            ->with(['auction', 'pelanggan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.tracking.index', compact('transaksis'));
    }

    /**
     * Show specific order tracking
     */
    public function show(Auction $auction)
    {
        // Check if user owns this auction
        if ($auction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat tracking ini');
        }

        $auction->load(['transaksi', 'winnerVendor']);

        if (!$auction->transaksi) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        return view('user.tracking.show', compact('auction'));
    }

    /**
     * Update tracking status (for vendor)
     */
    public function updateStatus(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'tracking_status' => 'required|in:menunggu,diproses,dicetak,dikirim,selesai',
            'no_resi' => 'nullable|string|max:50',
            'kurir' => 'nullable|string|max:50',
            'ongkir' => 'nullable|numeric|min:0',
            'is_cod' => 'boolean'
        ]);

        $transaksi->update([
            'tracking_status' => $request->tracking_status,
            'no_resi' => $request->no_resi,
            'kurir' => $request->kurir,
            'ongkir' => $request->ongkir ?? 0,
            'is_cod' => $request->is_cod ?? false,
            'alamat_pengiriman' => $request->alamat_pengiriman ?? $transaksi->alamat_pengiriman
        ]);

        // Update timestamps based on status
        $this->updateTrackingTimestamps($transaksi, $request->tracking_status);

        return redirect()->back()
            ->with('success', 'Status tracking berhasil diperbarui!');
    }

    /**
     * Calculate shipping cost using RajaOngkir API
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'weight' => 'required|integer|min:1',
            'courier' => 'required|string'
        ]);

        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.api_key'),
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier,
                'price' => 'lowest'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghitung ongkir'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('RajaOngkir API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung ongkir'
            ], 500);
        }
    }

    /**
     * Track shipment using RajaOngkir API
     */
    public function trackShipment(Request $request)
    {
        $request->validate([
            'awb' => 'required|string',
            'courier' => 'required|string'
        ]);

        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.api_key')
            ])->get('https://rajaongkir.komerce.id/api/v1/track/waybill', [
                'awb' => $request->awb,
                'courier' => $request->courier
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal melacak pengiriman'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('RajaOngkir Tracking Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melacak pengiriman'
            ], 500);
        }
    }

    /**
     * Update tracking timestamps
     */
    private function updateTrackingTimestamps(Transaksi $transaksi, string $status)
    {
        $now = now();

        switch ($status) {
            case 'diproses':
                $transaksi->update(['diproses_at' => $now]);
                break;
            case 'dicetak':
                $transaksi->update(['dicetak_at' => $now]);
                break;
            case 'dikirim':
                $transaksi->update(['dikirim_at' => $now]);
                break;
            case 'selesai':
                $transaksi->update(['selesai_at' => $now]);
                break;
        }
    }
}

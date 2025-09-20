<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShippingCalculatorController extends Controller
{
    protected $rajaOngkirService;

    public function __construct(RajaOngkirService $rajaOngkirService)
    {
        $this->rajaOngkirService = $rajaOngkirService;
    }

    /**
     * Show shipping calculator page
     */
    public function index()
    {
        $vendor = Auth::user()->vendorUser->first();

        if (!$vendor) {
            return redirect()->route('vendor.dashboard')
                ->with('toast_error', 'Vendor tidak ditemukan');
        }

        // Get cities data (you can cache this or load from database)
        $cities = $this->getCitiesData();

        return view('vendor.tracking.shipping-calculator', compact('vendor', 'cities'));
    }

    /**
     * Calculate shipping cost
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'weight' => 'required|integer|min:1|max:30000',
            'courier' => 'required|string',
            'service' => 'nullable|string'
        ]);

        try {
            $shippingData = $this->rajaOngkirService->calculateShipping([
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier
            ]);

            if ($shippingData['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $shippingData['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $shippingData['message']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Shipping calculation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung ongkir'
            ], 500);
        }
    }

    /**
     * Get cities data
     */
    private function getCitiesData()
    {
        // This is a simplified version. In production, you should:
        // 1. Cache this data
        // 2. Store in database
        // 3. Use RajaOngkir API to get cities

        return [
            ['city_id' => '151', 'city_name' => 'Jakarta Pusat', 'province' => 'DKI Jakarta'],
            ['city_id' => '152', 'city_name' => 'Jakarta Utara', 'province' => 'DKI Jakarta'],
            ['city_id' => '153', 'city_name' => 'Jakarta Selatan', 'province' => 'DKI Jakarta'],
            ['city_id' => '154', 'city_name' => 'Jakarta Barat', 'province' => 'DKI Jakarta'],
            ['city_id' => '155', 'city_name' => 'Jakarta Timur', 'province' => 'DKI Jakarta'],
            ['city_id' => '501', 'city_name' => 'Bandung', 'province' => 'Jawa Barat'],
            ['city_id' => '502', 'city_name' => 'Bogor', 'province' => 'Jawa Barat'],
            ['city_id' => '503', 'city_name' => 'Depok', 'province' => 'Jawa Barat'],
            ['city_id' => '504', 'city_name' => 'Tangerang', 'province' => 'Banten'],
            ['city_id' => '505', 'city_name' => 'Bekasi', 'province' => 'Jawa Barat'],
            ['city_id' => '601', 'city_name' => 'Surabaya', 'province' => 'Jawa Timur'],
            ['city_id' => '602', 'city_name' => 'Malang', 'province' => 'Jawa Timur'],
            ['city_id' => '603', 'city_name' => 'Sidoarjo', 'province' => 'Jawa Timur'],
            ['city_id' => '701', 'city_name' => 'Yogyakarta', 'province' => 'DI Yogyakarta'],
            ['city_id' => '801', 'city_name' => 'Semarang', 'province' => 'Jawa Tengah'],
            ['city_id' => '802', 'city_name' => 'Solo', 'province' => 'Jawa Tengah'],
            ['city_id' => '901', 'city_name' => 'Denpasar', 'province' => 'Bali'],
            ['city_id' => '1001', 'city_name' => 'Medan', 'province' => 'Sumatera Utara'],
            ['city_id' => '1002', 'city_name' => 'Pekanbaru', 'province' => 'Riau'],
            ['city_id' => '1101', 'city_name' => 'Palembang', 'province' => 'Sumatera Selatan'],
            ['city_id' => '1201', 'city_name' => 'Makassar', 'province' => 'Sulawesi Selatan'],
            ['city_id' => '1301', 'city_name' => 'Balikpapan', 'province' => 'Kalimantan Timur'],
            ['city_id' => '1401', 'city_name' => 'Manado', 'province' => 'Sulawesi Utara'],
            ['city_id' => '1501', 'city_name' => 'Jayapura', 'province' => 'Papua'],
        ];
    }

    /**
     * Get available couriers
     */
    public function getCouriers()
    {
        return response()->json([
            'success' => true,
            'data' => $this->rajaOngkirService->getAvailableCouriers()
        ]);
    }

    /**
     * Get service types for courier
     */
    public function getServiceTypes(Request $request)
    {
        $request->validate([
            'courier' => 'required|string'
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->rajaOngkirService->getServiceTypes($request->courier)
        ]);
    }

    /**
     * Save shipping calculation to transaction
     */
    public function saveShipping(Request $request, $transaksiId)
    {
        $request->validate([
            'courier' => 'required|string',
            'service' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'etd' => 'nullable|string',
            'address' => 'required|string'
        ]);

        try {
            $transaksi = \App\Models\Vendor\Transaksi::findOrFail($transaksiId);

            // Check if user has permission to update this transaction
            $vendor = Auth::user()->vendorUser->first();
            if ($transaksi->vendor_id !== $vendor->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk transaksi ini'
                ], 403);
            }

            // Update transaction with shipping details
            $transaksi->update([
                'kurir' => $request->courier,
                'ongkir' => $request->cost,
                'alamat_pengiriman' => $request->address,
                'tracking_status' => 'dikirim'
            ]);

            Log::info('Shipping details saved', [
                'transaction_id' => $transaksiId,
                'courier' => $request->courier,
                'cost' => $request->cost,
                'vendor_id' => $vendor->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail pengiriman berhasil disimpan',
                'data' => [
                    'courier' => $request->courier,
                    'service' => $request->service,
                    'cost' => $request->cost,
                    'etd' => $request->etd
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving shipping details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan detail pengiriman'
            ], 500);
        }
    }
}

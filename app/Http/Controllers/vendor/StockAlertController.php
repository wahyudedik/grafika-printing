<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Http\Concerns\HasVendorContext;
use App\Models\Vendor\Bahan;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockAlertController extends Controller
{
    use HasVendorContext;

    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display a listing of stock alerts.
     */
    public function index(Request $request)
    {
        try {
            $vendor = $this->requireVendor();

            $filter = $request->get('filter', 'unread'); // unread, read, all

            $query = \App\Models\Vendor\StockAlert::where('vendor_id', $vendor->id)
                ->with('bahan');

            if ($filter === 'unread') {
                $query->where('is_read', false);
            } elseif ($filter === 'read') {
                $query->where('is_read', true);
            }

            $alerts = $query->latest()->paginate(20)->withQueryString();

            $unreadCount = $this->stockService->getUnreadAlertCount($vendor->id);

            return view('vendor.stock-alerts.index', compact('alerts', 'filter', 'unreadCount'));
        } catch (\Exception $e) {
            Log::error('Error loading stock alerts: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat data alert stok: ' . $e->getMessage());
        }
    }

    /**
     * Mark a specific alert as read.
     */
    public function markAsRead(int $id): JsonResponse
    {
        try {
            $vendor = $this->requireVendor();
            $success = $this->stockService->markAsRead($id, $vendor->id);

            if ($success) {
                return response()->json(['success' => true, 'message' => 'Alert ditandai sudah dibaca.']);
            }

            return response()->json(['success' => false, 'message' => 'Alert tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error marking alert as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui alert.'], 500);
        }
    }

    /**
     * Mark all alerts as read for the current vendor.
     */
    public function markAllRead(): JsonResponse
    {
        try {
            $vendor = $this->requireVendor();
            $count = $this->stockService->markAllAsRead($vendor->id);

            return response()->json([
                'success' => true,
                'message' => "{$count} alert ditandai sudah dibaca.",
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking all alerts as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui alert.'], 500);
        }
    }

    /**
     * AJAX: Get unread alert count.
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $vendor = $this->requireVendor();
            $count = $this->stockService->getUnreadAlertCount($vendor->id);

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Update the minimum_stok threshold for a specific bahan.
     */
    public function updateMinimumStok(Request $request, Bahan $bahan): JsonResponse
    {
        try {
            $vendor = $this->requireVendor();

            if ($bahan->vendor_id !== $vendor->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }

            $validated = $request->validate([
                'minimum_stok' => 'required|integer|min:0',
                'maksimum_stok' => 'nullable|integer|min:0|gte:minimum_stok',
            ]);

            $bahan->update($validated);

            return response()->json([
                'success' => true,
                'message' => "Threshold stok minimum {$bahan->nama_bahan} diperbarui ke {$bahan->minimum_stok}.",
                'bahan' => [
                    'id' => $bahan->id,
                    'nama_bahan' => $bahan->nama_bahan,
                    'minimum_stok' => $bahan->minimum_stok,
                    'maksimum_stok' => $bahan->maksimum_stok,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating minimum stock: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui threshold stok.'], 500);
        }
    }
}

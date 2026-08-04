<?php

namespace App\Http\Controllers;

use App\Models\Vendor\Linktree;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LinktreePaymentController extends Controller
{
    protected XenditService $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    /**
     * Generate QRIS payment for linktree.
     * Called from the public linktree page when visitor wants to pay via QRIS.
     */
    public function generateQris(Request $request, string $customUrl)
    {
        $linktree = Linktree::where('custom_url', $customUrl)
            ->where('is_active', true)
            ->first();

        if (!$linktree) {
            return response()->json(['error' => 'Linktree tidak ditemukan'], 404);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1000|max:10000000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $externalId = 'linktree_' . $linktree->id . '_' . Str::random(10) . '_' . time();

            $result = $this->xenditService->createQrisPayment([
                'external_id' => $externalId,
                'amount' => (int) $request->amount,
                'description' => $request->description ?? 'Pembayaran ke ' . ($linktree->vendor->name ?? $linktree->vendor->nama_vendor ?? 'Vendor'),
                'customer_email' => $request->customer_email ?? null,
                'success_redirect_url' => route('linktree.public.show', $customUrl) . '?payment=success',
                'failure_redirect_url' => route('linktree.public.show', $customUrl) . '?payment=failed',
                'items' => [
                    [
                        'name' => $request->description ?? 'Pembayaran Linktree',
                        'quantity' => 1,
                        'price' => (int) $request->amount
                    ]
                ]
            ]);

            if ($result) {
                Log::info('Linktree QRIS Payment Created', [
                    'linktree_id' => $linktree->id,
                    'external_id' => $externalId,
                    'amount' => $request->amount,
                    'invoice_id' => $result['id']
                ]);

                return response()->json([
                    'success' => true,
                    'invoice_url' => $result['invoice_url'],
                    'qr_code' => $result['qr_code'] ?? null,
                    'amount' => $result['amount'],
                    'invoice_id' => $result['id'],
                    'expires_at' => $result['expires_at']
                ]);
            }

            return response()->json([
                'error' => 'Gagal membuat pembayaran QRIS. Silakan coba lagi.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Linktree QRIS Payment Error', [
                'linktree_id' => $linktree->id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Check payment status.
     */
    public function checkStatus(string $customUrl, string $invoiceId)
    {
        $linktree = Linktree::where('custom_url', $customUrl)
            ->where('is_active', true)
            ->first();

        if (!$linktree) {
            return response()->json(['error' => 'Linktree tidak ditemukan'], 404);
        }

        try {
            $result = $this->xenditService->getPaymentLink($invoiceId);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'status' => $result['status'],
                    'paid_at' => $result['paid_at'] ?? null,
                    'payment_method' => $result['payment_method'] ?? null
                ]);
            }

            return response()->json(['error' => 'Invoice tidak ditemukan'], 404);

        } catch (\Exception $e) {
            Log::error('Linktree Payment Status Check Error', [
                'linktree_id' => $linktree->id,
                'invoice_id' => $invoiceId,
                'message' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Gagal cek status pembayaran'], 500);
        }
    }
}

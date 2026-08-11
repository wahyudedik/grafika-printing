<?php

namespace App\Http\Controllers;

use App\Models\ManualTransferOrder;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Responses\FlashMessage;


class ManualTransferController extends Controller
{
    /**
     * Place a manual transfer order (public, no auth required)
     */
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($validated['vendor_id']);

        // Calculate total from items
        $totalAmount = collect($validated['items'])->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        });

        // Get vendor bank account
        $bankAccount = $vendor->getPrimaryBankAccount();

        $order = ManualTransferOrder::create([
            'vendor_id' => $validated['vendor_id'],
            'user_id' => auth()->id(),
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'customer_email' => $validated['customer_email'] ?? null,
            'items' => $validated['items'],
            'total_amount' => $totalAmount,
            'bank_name' => $bankAccount['bank_name'] ?? null,
            'account_number' => $bankAccount['account_number'] ?? null,
            'account_name' => $bankAccount['account_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => ManualTransferOrder::STATUS_PENDING,
        ]);

        Log::info("Manual transfer order created: {$order->order_number} for vendor {$vendor->name}");

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat. Silakan lakukan transfer dan upload bukti transfer.',
                'order' => $order,
                'bank_info' => [
                    'bank_name' => $bankAccount['bank_name'] ?? '-',
                    'account_number' => $bankAccount['account_number'] ?? '-',
                    'account_name' => $bankAccount['account_name'] ?? '-',
                ],
            ]);
        }

        return FlashMessage::success(redirect()->route('manual-transfer.status', $order->order_number), 'Order berhasil dibuat. Silakan lakukan transfer.');
    }

    /**
     * Check order status (public)
     */
    public function checkStatus(string $orderNumber)
    {
        $order = ManualTransferOrder::where('order_number', $orderNumber)->firstOrFail();

        return view('manual-transfer.status', compact('order'));
    }

    /**
     * Upload transfer proof
     */
    public function uploadProof(Request $request, string $orderNumber)
    {
        $order = ManualTransferOrder::where('order_number', $orderNumber)->firstOrFail();

        $request->validate([
            'transfer_proof' => 'required|image|max:5120', // 5MB max
        ]);

        $file = $request->file('transfer_proof');
        $filename = 'transfer_proof_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/manual_transfer_proofs', $filename);

        $order->update([
            'transfer_proof' => $filename,
            'status' => ManualTransferOrder::STATUS_PAID,
            'paid_at' => now(),
        ]);

        Log::info("Transfer proof uploaded for order: {$order->order_number}");

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bukti transfer berhasil diunggah.',
            ]);
        }

        return FlashMessage::backSuccess('Bukti transfer berhasil diunggah. Menunggu konfirmasi vendor.');
    }
}

<?php



namespace App\Http\Controllers\vendor\pos;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Transaksi;
use App\Services\XenditService;
use App\Services\AdminFeeService;
use App\Http\Responses\FlashMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Concerns\HasVendorContext;

class PaymentController extends Controller
{
    use HasVendorContext;


    protected $xenditService;
    protected $adminFeeService;

    public function __construct(XenditService $xenditService, AdminFeeService $adminFeeService)
    {
        $this->xenditService = $xenditService;
        $this->adminFeeService = $adminFeeService;
    }

    /**
     * Show payment options for a transaction
     */
    public function showPaymentOptions(Transaksi $transaksi)
    {
        // Check if user is authorized to access this transaction
        $vendor = $this->requireVendor();
        if (!$vendor || $transaksi->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized access to transaction.');
        }

        // Check if transaction is ready for payment
        if ($transaksi->status !== 'pending') {
            return FlashMessage::error(redirect()->route('vendor.pos.invoice.show', $transaksi), 'Transaction is not ready for payment.');
        }

        return view('pos.payment-options', compact('transaksi'));
    }

    /**
     * Process cash payment
     */
    public function processCashPayment(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:' . $transaksi->total_harga,
            'change_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::transaction(function () use ($request, $transaksi) {
                // Update transaction with cash payment
                $transaksi->update([
                    'payment_method' => 'cash',
                    'payment_amount' => $request->payment_amount,
                    'change_amount' => $request->change_amount ?? 0,
                    'status' => 'completed',
                    'paid_at' => now(),
                    'notes' => $request->notes
                ]);

                // Log the payment
                Log::info('Cash payment processed', [
                    'transaction_id' => $transaksi->id,
                    'amount' => $request->payment_amount,
                    'change' => $request->change_amount ?? 0
                ]);
            });

            return FlashMessage::success(redirect()->route('vendor.pos.payment.success', $transaksi), 'Cash payment processed successfully!');
        } catch (\Exception $e) {
            Log::error('Cash payment failed', [
                'transaction_id' => $transaksi->id,
                'error' => $e->getMessage()
            ]);

            return FlashMessage::backError('Failed to process cash payment: ' . $e->getMessage());
        }
    }

    /**
     * Process Xendit payment
     */
    public function processXenditPayment(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'payment_type' => 'required|string|in:bank_transfer,ewallet,retail,qris',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20'
        ]);

        try {
            // Calculate admin fees for POS transaction
            $feeCalculation = $this->adminFeeService->calculateFees($transaksi->total_harga, 'pos_transaction');

            $externalId = 'pos_' . $transaksi->id . '_' . time();
            $totalAmount = $transaksi->total_harga + $feeCalculation['total_fee'];

            $paymentData = [
                'external_id' => $externalId,
                'amount' => $totalAmount,
                'description' => 'POS Payment: ' . $transaksi->kode,
                'customer' => [
                    'given_names' => $transaksi->pelanggan->nama,
                    'email' => $request->customer_email,
                    'mobile_number' => $request->customer_phone
                ],
                'items' => [
                    [
                        'name' => 'POS Transaction #' . $transaksi->kode,
                        'quantity' => 1,
                        'price' => $transaksi->total_harga,
                        'category' => 'Printing Service'
                    ],
                    [
                        'name' => 'Admin Fee',
                        'quantity' => 1,
                        'price' => $feeCalculation['total_fee'],
                        'category' => 'Fee'
                    ]
                ],
                'success_redirect_url' => route('vendor.pos.payment.success', $transaksi),
                'failure_redirect_url' => route('vendor.pos.payment.failure', $transaksi),
                'invoice_duration' => 86400, // 24 hours
                'payment_methods' => $this->getPaymentMethods($request->payment_type)
            ];

            $response = $this->xenditService->createPaymentLink($paymentData);

            if ($response && isset($response['invoice_url'])) {
                // Create XenditPayment record with transaksi_id
                \App\Models\XenditPayment::create([
                    'external_id' => $externalId,
                    'xendit_id' => $response['id'] ?? null,
                    'amount' => $totalAmount,
                    'status' => 'pending',
                    'checkout_url' => $response['checkout_url'] ?? null,
                    'payment_method' => $request->payment_type,
                    'user_id' => auth()->id(),
                    'transaksi_id' => $transaksi->id,
                ]);

                // Update transaction with Xendit payment info
                $transaksi->update([
                    'payment_method' => 'xendit',
                    'xendit_payment_id' => $response['id'] ?? null,
                    'xendit_external_id' => $externalId,
                    'payment_amount' => $totalAmount,
                    'admin_fee' => $feeCalculation['total_fee'],
                    'status' => 'payment_pending',
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone
                ]);

                Log::info('Xendit payment link created', [
                    'transaction_id' => $transaksi->id,
                    'external_id' => $externalId,
                    'amount' => $totalAmount,
                    'payment_url' => $response['invoice_url']
                ]);

                return redirect($response['invoice_url']);
            } else {
                throw new \Exception('Failed to create Xendit payment link.');
            }
        } catch (\Exception $e) {
            Log::error('Xendit payment creation failed', [
                'transaction_id' => $transaksi->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return FlashMessage::error(redirect()->route('vendor.pos.payment.failure', $transaksi), 'Failed to create payment link: ' . $e->getMessage());
        }
    }

    /**
     * Show payment success page
     */
    public function paymentSuccess(Transaksi $transaksi)
    {
        // Check if user is authorized to access this transaction
        $vendor = $this->requireVendor();
        if (!$vendor || $transaksi->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized access to transaction.');
        }

        // Update transaction status if payment was successful
        if ($transaksi->status === 'payment_pending') {
            $transaksi->update([
                'status' => 'completed',
                'paid_at' => now()
            ]);
        }

        return view('pos.payment-success', compact('transaksi'));
    }

    /**
     * Show payment failure page
     */
    public function paymentFailure(Transaksi $transaksi)
    {
        // Check if user is authorized to access this transaction
        $vendor = $this->requireVendor();
        if (!$vendor || $transaksi->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized access to transaction.');
        }

        return view('pos.payment-failure', compact('transaksi'));
    }

    /**
     * Get payment methods based on type
     */
    private function getPaymentMethods($type)
    {
        switch ($type) {
            case 'bank_transfer':
                return ['BCA', 'BNI', 'BRI', 'BSI', 'MANDIRI', 'PERMATA'];
            case 'ewallet':
                return ['OVO', 'DANA', 'LINKAJA', 'SHOPEEPAY'];
            case 'retail':
                return ['ALFAMART', 'INDOMARET'];
            case 'qris':
                return ['QRIS'];
            default:
                return ['BCA', 'BNI', 'BRI', 'BSI', 'MANDIRI', 'PERMATA', 'OVO', 'DANA', 'LINKAJA', 'SHOPEEPAY', 'ALFAMART', 'INDOMARET'];
        }
    }
}

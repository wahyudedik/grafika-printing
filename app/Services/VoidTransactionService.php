<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransactionVoidLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoidTransactionService
{
    protected StockService $stockService;
    protected XenditService $xenditService;

    public function __construct(StockService $stockService, XenditService $xenditService)
    {
        $this->stockService = $stockService;
        $this->xenditService = $xenditService;
    }

    /**
     * Cek apakah transaksi bisa di-void.
     *
     * Rules:
     * - Status harus: pending, processing, quality_check
     * - Jika sudah completed, tidak bisa di-void
     * - Jika sudah cancelled, tidak bisa di-void
     * - Jika sudah di-void, tidak bisa di-void lagi
     *
     * @param Transaksi $transaksi
     * @return array{can_void: bool, message: string}
     */
    public function canBeVoided(Transaksi $transaksi): array
    {
        // Sudah di-void
        if ($transaksi->is_voided) {
            return [
                'can_void' => false,
                'message' => 'Transaksi ini sudah di-void sebelumnya.',
            ];
        }

        // Sudah selesai
        if ($transaksi->status === 'completed') {
            return [
                'can_void' => false,
                'message' => 'Transaksi yang sudah selesai tidak dapat di-void. Silakan gunakan fitur refund jika diperlukan.',
            ];
        }

        // Sudah dikirim
        if ($transaksi->status === 'dikirim') {
            return [
                'can_void' => false,
                'message' => 'Transaksi yang sudah dikirim tidak dapat di-void.',
            ];
        }

        // Sudah dibatalkan
        if ($transaksi->status === 'cancelled') {
            return [
                'can_void' => false,
                'message' => 'Transaksi ini sudah dibatalkan.',
            ];
        }

        // Status yang bisa di-void: pending, processing, quality_check
        $voidableStatuses = ['pending', 'processing', 'quality_check'];
        if (!in_array($transaksi->status, $voidableStatuses)) {
            return [
                'can_void' => false,
                'message' => 'Status transaksi "' . $transaksi->status . '" tidak dapat di-void.',
            ];
        }

        return [
            'can_void' => true,
            'message' => 'Transaksi dapat di-void.',
        ];
    }

    /**
     * Proses void transaksi.
     *
     * Steps:
     * 1. Set is_voided = true, void_status = 'voided', void_reason, voided_at, voided_by
     * 2. Set status = 'cancelled'
     * 3. Restore stock via StockService::restoreStock()
     * 4. Jika sudah bayar via Xendit, proses refund
     * 5. Buat TransactionVoidLog
     * 6. Log audit
     *
     * @param Transaksi $transaksi
     * @param string $reason Alasan void
     * @param User $user User yang melakukan void
     * @return array{success: bool, message: string, void_log: TransactionVoidLog|null, refund_status: string|null}
     */
    public function voidTransaction(Transaksi $transaksi, string $reason, User $user): array
    {
        // Cek apakah bisa di-void
        $check = $this->canBeVoided($transaksi);
        if (!$check['can_void']) {
            return [
                'success' => false,
                'message' => $check['message'],
                'void_log' => null,
                'refund_status' => null,
            ];
        }

        DB::beginTransaction();

        try {
            // Snapshot data sebelum void untuk audit log
            $oldData = [
                'status' => $transaksi->status,
                'total_harga' => $transaksi->total_harga,
                'terbayar' => $transaksi->terbayar,
                'payment_method' => $transaksi->payment_method,
                'progress_percentage' => $transaksi->progress_percentage,
            ];

            // Step 1 & 2: Update transaksi
            $transaksi->update([
                'is_voided' => true,
                'void_status' => 'voided',
                'void_reason' => $reason,
                'voided_by' => $user->id,
                'voided_at' => now(),
                'status' => 'cancelled',
                'progress_percentage' => 0,
            ]);

            // Step 3: Restore stock
            $stockRestored = false;
            try {
                $this->stockService->restoreStock($transaksi);
                $stockRestored = true;
            } catch (\Exception $e) {
                Log::error('Stock restore failed during void', [
                    'transaksi_id' => $transaksi->id,
                    'error' => $e->getMessage(),
                ]);
                // Stock restore gagal, tapi void tetap dilanjutkan
                // Karena stock restore bisa dilakukan manual
            }

            // Step 4: Proses refund jika pembayaran via Xendit
            $refundStatus = null;
            $refundProcessed = false;
            $refundAmount = null;

            if ($this->shouldProcessRefund($transaksi)) {
                $refundResult = $this->processRefund($transaksi, $reason);
                $refundStatus = $refundResult['status'] ?? 'failed';
                $refundProcessed = $refundResult['success'] ?? false;
                $refundAmount = $refundResult['refund_amount'] ?? null;

                // Update void_status berdasarkan hasil refund
                if ($refundProcessed) {
                    $transaksi->update([
                        'void_status' => 'refund_pending',
                        'refund_amount' => $refundAmount,
                    ]);
                    $refundStatus = 'refund_pending';
                } else {
                    $transaksi->update([
                        'void_status' => 'voided', // Refund gagal, tapi void tetap
                    ]);
                    $refundStatus = 'refund_failed';
                }
            }

            $newData = [
                'status' => 'cancelled',
                'is_voided' => true,
                'void_status' => $refundProcessed ? 'refund_pending' : 'voided',
                'refund_amount' => $refundAmount,
            ];

            // Step 5: Buat TransactionVoidLog
            $voidLog = TransactionVoidLog::create([
                'vendor_id' => $transaksi->vendor_id,
                'transaksi_id' => $transaksi->id,
                'user_id' => $user->id,
                'action' => 'void',
                'reason' => $reason,
                'old_data' => $oldData,
                'new_data' => $newData,
                'refund_amount' => $refundAmount,
                'stock_restored' => $stockRestored,
                'refund_processed' => $refundProcessed,
            ]);

            // Step 6: Log audit
            AuditLogService::logUpdated(
                $transaksi,
                $oldData,
                'Transaksi di-void: ' . $transaksi->kode . ' | Alasan: ' . $reason
            );

            DB::commit();

            Log::info('Transaction voided successfully', [
                'transaksi_id' => $transaksi->id,
                'kode' => $transaksi->kode,
                'voided_by' => $user->id,
                'stock_restored' => $stockRestored,
                'refund_status' => $refundStatus,
            ]);

            return [
                'success' => true,
                'message' => 'Transaksi berhasil di-void.' . ($refundStatus === 'refund_pending' ? ' Refund sedang diproses.' : ''),
                'void_log' => $voidLog,
                'refund_status' => $refundStatus,
            ];
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Transaction void failed', [
                'transaksi_id' => $transaksi->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memproses void: ' . $e->getMessage(),
                'void_log' => null,
                'refund_status' => null,
            ];
        }
    }

    /**
     * Proses refund untuk transaksi yang sudah dibayar via Xendit.
     *
     * @param Transaksi $transaksi
     * @param string $reason
     * @return array{success: bool, status: string, refund_amount: float|null, refund_id: string|null}
     */
    public function processRefund(Transaksi $transaksi, string $reason): array
    {
        try {
            // Cek apakah ada payment ID yang valid
            $paymentId = $transaksi->xendit_payment_id;

            if (empty($paymentId)) {
                Log::warning('No Xendit payment ID found for refund', [
                    'transaksi_id' => $transaksi->id,
                ]);
                return [
                    'success' => false,
                    'status' => 'no_payment_id',
                    'refund_amount' => null,
                    'refund_id' => null,
                ];
            }

            // Hitung jumlah refund (jumlah yang sudah dibayar)
            $refundAmount = (int) round($transaksi->terbayar);

            if ($refundAmount <= 0) {
                Log::warning('Zero refund amount', [
                    'transaksi_id' => $transaksi->id,
                    'terbayar' => $transaksi->terbayar,
                ]);
                return [
                    'success' => false,
                    'status' => 'zero_amount',
                    'refund_amount' => null,
                    'refund_id' => null,
                ];
            }

            // Panggil Xendit refund API
            $result = $this->xenditService->createRefund(
                $paymentId,
                $refundAmount,
                'Void transaksi: ' . $transaksi->kode . ' - ' . $reason
            );

            if ($result && isset($result['id'])) {
                // Update transaksi dengan refund info
                $transaksi->update([
                    'refund_id' => $result['id'],
                    'refund_amount' => $refundAmount,
                ]);

                // Buat log refund
                TransactionVoidLog::create([
                    'vendor_id' => $transaksi->vendor_id,
                    'transaksi_id' => $transaksi->id,
                    'user_id' => $transaksi->voided_by,
                    'action' => 'refund',
                    'reason' => 'Refund untuk void transaksi: ' . $transaksi->kode,
                    'refund_amount' => $refundAmount,
                    'stock_restored' => false,
                    'refund_processed' => true,
                ]);

                return [
                    'success' => true,
                    'status' => 'refund_pending',
                    'refund_amount' => $refundAmount,
                    'refund_id' => $result['id'],
                ];
            }

            return [
                'success' => false,
                'status' => 'api_error',
                'refund_amount' => null,
                'refund_id' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'transaksi_id' => $transaksi->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'exception',
                'refund_amount' => null,
                'refund_id' => null,
            ];
        }
    }

    /**
     * Cek apakah refund harus diproses.
     * Refund hanya untuk pembayaran online (Xendit) yang sudah terbayar.
     *
     * @param Transaksi $transaksi
     * @return bool
     */
    protected function shouldProcessRefund(Transaksi $transaksi): bool
    {
        // Hanya refund untuk pembayaran online via Xendit
        $onlinePaymentMethods = ['xendit', 'online', 'qris', 'ewallet', 'bank_transfer'];
        $paymentMethod = strtolower($transaksi->payment_method ?? '');

        if (!in_array($paymentMethod, $onlinePaymentMethods)) {
            return false;
        }

        // Harus sudah terbayar
        if ($transaksi->payment_status !== 'paid' && $transaksi->terbayar <= 0) {
            return false;
        }

        // Harus ada payment ID
        if (empty($transaksi->xendit_payment_id)) {
            return false;
        }

        return true;
    }

    /**
     * Get riwayat void untuk transaksi.
     *
     * @param Transaksi $transaksi
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVoidHistory(Transaksi $transaksi)
    {
        return TransactionVoidLog::where('transaksi_id', $transaksi->id)
            ->with('voidedByUser')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

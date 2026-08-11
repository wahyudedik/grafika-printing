<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log financial transaction
     */
    public static function logFinancialTransaction($data)
    {
        try {
            $logData = [
                'user_id' => auth()->id(),
                'vendor_id' => $data['vendor_id'] ?? null,
                'action_type' => $data['action_type'],
                'entity_type' => $data['entity_type'],
                'entity_id' => $data['entity_id'],
                'old_data' => $data['old_data'] ?? null,
                'new_data' => $data['new_data'] ?? null,
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'amount' => $data['amount'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'notes' => $data['notes'] ?? null,
                'risk_level' => self::calculateRiskLevel($data)
            ];

            return FinancialAuditLog::createLog($logData);
        } catch (\Exception $e) {
            Log::error('Failed to create audit log: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log withdrawal action
     */
    public static function logWithdrawal($withdrawal, $action, $oldData = null)
    {
        $riskLevel = self::calculateWithdrawalRisk($withdrawal);

        return self::logFinancialTransaction([
            'vendor_id' => $withdrawal->vendor_id,
            'action_type' => $action,
            'entity_type' => FinancialAuditLog::ENTITY_WITHDRAWAL,
            'entity_id' => $withdrawal->id,
            'old_data' => $oldData,
            'new_data' => $withdrawal->toArray(),
            'transaction_reference' => $withdrawal->withdrawal_code,
            'amount' => $withdrawal->amount,
            'status' => $withdrawal->status,
            'notes' => "Withdrawal {$action} for vendor {$withdrawal->vendor->name}",
            'risk_level' => $riskLevel
        ]);
    }

    /**
     * Log wallet transaction
     */
    public static function logWalletTransaction($walletTransaction, $action, $oldData = null)
    {
        return self::logFinancialTransaction([
            'vendor_id' => $walletTransaction->vendor_id,
            'action_type' => $action,
            'entity_type' => FinancialAuditLog::ENTITY_WALLET,
            'entity_id' => $walletTransaction->id,
            'old_data' => $oldData,
            'new_data' => $walletTransaction->toArray(),
            'transaction_reference' => $walletTransaction->transaction_code,
            'amount' => $walletTransaction->amount,
            'status' => $walletTransaction->status,
            'notes' => "Wallet transaction {$action}",
            'risk_level' => self::calculateWalletRisk($walletTransaction)
        ]);
    }

    /**
     * Log admin action
     */
    public static function logAdminAction($entity, $action, $oldData = null, $notes = null)
    {
        $entityType = self::getEntityType($entity);

        return self::logFinancialTransaction([
            'vendor_id' => $entity->vendor_id ?? null,
            'action_type' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entity->id,
            'old_data' => $oldData,
            'new_data' => $entity->toArray(),
            'amount' => $entity->amount ?? $entity->total_amount ?? null,
            'status' => $entity->status ?? 'completed',
            'notes' => $notes ?? "Admin {$action} {$entityType}",
            'risk_level' => self::calculateAdminRisk($action, $entity)
        ]);
    }

    /**
     * Calculate risk level based on transaction data
     */
    private static function calculateRiskLevel($data)
    {
        $riskScore = 0;

        // High amount increases risk
        if (isset($data['amount']) && $data['amount'] > 10000000) { // > 10M
            $riskScore += 3;
        } elseif (isset($data['amount']) && $data['amount'] > 5000000) { // > 5M
            $riskScore += 2;
        } elseif (isset($data['amount']) && $data['amount'] > 1000000) { // > 1M
            $riskScore += 1;
        }

        // Withdrawal actions are higher risk
        if (isset($data['action_type']) && in_array($data['action_type'], ['withdraw', 'approve', 'reject'])) {
            $riskScore += 2;
        }

        // Critical actions
        if (isset($data['action_type']) && $data['action_type'] === 'delete') {
            $riskScore += 3;
        }

        // Determine risk level
        if ($riskScore >= 5) return FinancialAuditLog::RISK_CRITICAL;
        if ($riskScore >= 3) return FinancialAuditLog::RISK_HIGH;
        if ($riskScore >= 1) return FinancialAuditLog::RISK_MEDIUM;
        return FinancialAuditLog::RISK_LOW;
    }

    /**
     * Calculate withdrawal risk
     */
    private static function calculateWithdrawalRisk($withdrawal)
    {
        $riskScore = 0;

        // Large withdrawal amount
        if ($withdrawal->amount > 10000000) {
            $riskScore += 3;
        } elseif ($withdrawal->amount > 5000000) {
            $riskScore += 2;
        } elseif ($withdrawal->amount > 1000000) {
            $riskScore += 1;
        }

        // E-wallet withdrawals are higher risk
        if ($withdrawal->method === 'e_wallet') {
            $riskScore += 1;
        }

        // New vendor (less than 30 days)
        if ($withdrawal->vendor->created_at->diffInDays(now()) < 30) {
            $riskScore += 2;
        }

        if ($riskScore >= 4) return FinancialAuditLog::RISK_CRITICAL;
        if ($riskScore >= 2) return FinancialAuditLog::RISK_HIGH;
        if ($riskScore >= 1) return FinancialAuditLog::RISK_MEDIUM;
        return FinancialAuditLog::RISK_LOW;
    }

    /**
     * Calculate wallet transaction risk
     */
    private static function calculateWalletRisk($transaction)
    {
        if ($transaction->amount > 5000000) {
            return FinancialAuditLog::RISK_HIGH;
        } elseif ($transaction->amount > 1000000) {
            return FinancialAuditLog::RISK_MEDIUM;
        }
        return FinancialAuditLog::RISK_LOW;
    }

    /**
     * Calculate admin action risk
     */
    private static function calculateAdminRisk($action, $entity)
    {
        if ($action === 'delete') return FinancialAuditLog::RISK_CRITICAL;
        if ($action === 'reject') return FinancialAuditLog::RISK_HIGH;
        if ($action === 'approve') return FinancialAuditLog::RISK_MEDIUM;
        return FinancialAuditLog::RISK_LOW;
    }

    /**
     * Get entity type from model
     */
    private static function getEntityType($entity)
    {
        $className = class_basename($entity);

        switch ($className) {
            case 'VendorWithdrawal':
                return FinancialAuditLog::ENTITY_WITHDRAWAL;
            case 'VendorWalletTransaction':
                return FinancialAuditLog::ENTITY_WALLET;
            case 'XenditPayment':
                return FinancialAuditLog::ENTITY_PAYMENT;
            case 'Auction':
                return FinancialAuditLog::ENTITY_AUCTION;
            case 'AdminFeeTransaction':
                return FinancialAuditLog::ENTITY_ADMIN_FEE;
            default:
                return 'unknown';
        }
    }

    /**
     * Get audit logs for vendor
     */
    public static function getVendorLogs($vendorId, $limit = 50)
    {
        return FinancialAuditLog::forVendor($vendorId)
            ->with(['user', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get admin audit logs
     */
    public static function getAdminLogs($limit = 100)
    {
        return FinancialAuditLog::adminLogs()
            ->with(['user', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get high-risk transactions
     */
    public static function getHighRiskTransactions($limit = 50)
    {
        return FinancialAuditLog::highRisk()
            ->with(['user', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // =========================================================================
    // GENERIC AUDIT LOG METHODS
    // =========================================================================

    /**
     * Log a generic activity
     */
    public static function log(string $action, string $description = '', array $extra = []): void
    {
        try {
            $user = auth()->user();

            self::logFinancialTransaction([
                'vendor_id' => $extra['vendor_id'] ?? $user?->vendorUser?->first()?->vendor_id ?? null,
                'action_type' => $action,
                'entity_type' => $extra['entity_type'] ?? class_basename($extra['model'] ?? ''),
                'entity_id' => $extra['entity_id'] ?? $extra['model']?->id ?? null,
                'old_data' => $extra['old_values'] ?? null,
                'new_data' => $extra['new_values'] ?? null,
                'amount' => $extra['amount'] ?? null,
                'status' => $extra['status'] ?? 'completed',
                'notes' => $description,
                'risk_level' => $extra['risk_level'] ?? FinancialAuditLog::RISK_LOW,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create generic audit log: ' . $e->getMessage());
        }
    }

    /**
     * Log model creation
     */
    public static function logCreated($model, string $description = ''): void
    {
        $vendorId = method_exists($model, 'vendor_id') ? $model->vendor_id : null;

        self::log('created', $description ?: class_basename($model) . ' created', [
            'vendor_id' => $vendorId,
            'entity_type' => class_basename($model),
            'entity_id' => $model->id,
            'new_values' => $model->toArray(),
        ]);
    }

    /**
     * Log model update with old and new values
     */
    public static function logUpdated($model, array $oldValues, string $description = ''): void
    {
        $vendorId = method_exists($model, 'vendor_id') ? $model->vendor_id : null;

        self::log('updated', $description ?: class_basename($model) . ' updated', [
            'vendor_id' => $vendorId,
            'entity_type' => class_basename($model),
            'entity_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $model->toArray(),
        ]);
    }

    /**
     * Log model deletion
     */
    public static function logDeleted($model, string $description = ''): void
    {
        $vendorId = method_exists($model, 'vendor_id') ? $model->vendor_id : null;

        self::log('deleted', $description ?: class_basename($model) . ' deleted', [
            'vendor_id' => $vendorId,
            'entity_type' => class_basename($model),
            'entity_id' => $model->id,
            'old_values' => $model->toArray(),
            'risk_level' => FinancialAuditLog::RISK_HIGH,
        ]);
    }

    /**
     * Log status change
     */
    public static function logStatusChange($model, string $oldStatus, string $newStatus): void
    {
        $vendorId = method_exists($model, 'vendor_id') ? $model->vendor_id : null;

        self::log('status_changed', class_basename($model) . " status: {$oldStatus} → {$newStatus}", [
            'vendor_id' => $vendorId,
            'entity_type' => class_basename($model),
            'entity_id' => $model->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $newStatus],
        ]);
    }
}

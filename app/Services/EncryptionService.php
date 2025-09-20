<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EncryptionService
{
    /**
     * Encrypt sensitive financial data
     */
    public static function encryptFinancialData($data)
    {
        try {
            if (is_array($data)) {
                return Crypt::encrypt($data);
            }
            return Crypt::encryptString($data);
        } catch (\Exception $e) {
            Log::error('Encryption failed: ' . $e->getMessage());
            throw new \Exception('Failed to encrypt financial data');
        }
    }

    /**
     * Decrypt sensitive financial data
     */
    public static function decryptFinancialData($encryptedData)
    {
        try {
            return Crypt::decryptString($encryptedData);
        } catch (\Exception $e) {
            Log::error('Decryption failed: ' . $e->getMessage());
            throw new \Exception('Failed to decrypt financial data');
        }
    }

    /**
     * Encrypt withdrawal data
     */
    public static function encryptWithdrawalData($withdrawalData)
    {
        $sensitiveFields = [
            'account_number',
            'account_name',
            'bank_name',
            'ewallet_number',
            'ewallet_name',
            'ewallet_provider'
        ];

        $encryptedData = $withdrawalData;

        foreach ($sensitiveFields as $field) {
            if (isset($withdrawalData[$field]) && !empty($withdrawalData[$field])) {
                $encryptedData[$field] = self::encryptFinancialData($withdrawalData[$field]);
            }
        }

        return $encryptedData;
    }

    /**
     * Decrypt withdrawal data
     */
    public static function decryptWithdrawalData($encryptedData)
    {
        $sensitiveFields = [
            'account_number',
            'account_name',
            'bank_name',
            'ewallet_number',
            'ewallet_name',
            'ewallet_provider'
        ];

        $decryptedData = $encryptedData;

        foreach ($sensitiveFields as $field) {
            if (isset($encryptedData[$field]) && !empty($encryptedData[$field])) {
                try {
                    $decryptedData[$field] = self::decryptFinancialData($encryptedData[$field]);
                } catch (\Exception $e) {
                    // If decryption fails, return masked data
                    $decryptedData[$field] = self::maskSensitiveData($encryptedData[$field]);
                }
            }
        }

        return $decryptedData;
    }

    /**
     * Mask sensitive data for display
     */
    public static function maskSensitiveData($data, $maskChar = '*', $visibleChars = 4)
    {
        if (empty($data)) {
            return $data;
        }

        $length = strlen($data);
        if ($length <= $visibleChars) {
            return str_repeat($maskChar, $length);
        }

        $visible = substr($data, 0, $visibleChars);
        $masked = str_repeat($maskChar, $length - $visibleChars);

        return $visible . $masked;
    }

    /**
     * Hash transaction reference for audit trail
     */
    public static function hashTransactionReference($reference)
    {
        return hash('sha256', $reference . config('app.key'));
    }

    /**
     * Generate secure transaction ID
     */
    public static function generateSecureTransactionId()
    {
        return 'TXN_' . time() . '_' . bin2hex(random_bytes(8));
    }
}

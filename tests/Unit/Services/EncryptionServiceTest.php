<?php

namespace Tests\Unit\Services;

use App\Services\EncryptionService;
use Tests\TestCase;

class EncryptionServiceTest extends TestCase
{
    public function test_encrypts_financial_data()
    {
        $data = '1234567890';
        $encrypted = EncryptionService::encryptFinancialData($data);

        $this->assertNotEquals($data, $encrypted);
        $this->assertIsString($encrypted);
    }

    public function test_decrypts_financial_data()
    {
        $data = '1234567890';
        $encrypted = EncryptionService::encryptFinancialData($data);
        $decrypted = EncryptionService::decryptFinancialData($encrypted);

        $this->assertEquals($data, $decrypted);
    }

    public function test_encrypts_withdrawal_data()
    {
        $withdrawalData = [
            'account_number' => '1234567890',
            'account_name' => 'John Doe',
            'bank_name' => 'BCA',
            'amount' => 1000000
        ];

        $encrypted = EncryptionService::encryptWithdrawalData($withdrawalData);

        $this->assertNotEquals($withdrawalData['account_number'], $encrypted['account_number']);
        $this->assertNotEquals($withdrawalData['account_name'], $encrypted['account_name']);
        $this->assertNotEquals($withdrawalData['bank_name'], $encrypted['bank_name']);
        $this->assertEquals($withdrawalData['amount'], $encrypted['amount']); // Amount should not be encrypted
    }

    public function test_decrypts_withdrawal_data()
    {
        $withdrawalData = [
            'account_number' => '1234567890',
            'account_name' => 'John Doe',
            'bank_name' => 'BCA',
            'amount' => 1000000
        ];

        $encrypted = EncryptionService::encryptWithdrawalData($withdrawalData);
        $decrypted = EncryptionService::decryptWithdrawalData($encrypted);

        $this->assertEquals($withdrawalData['account_number'], $decrypted['account_number']);
        $this->assertEquals($withdrawalData['account_name'], $decrypted['account_name']);
        $this->assertEquals($withdrawalData['bank_name'], $decrypted['bank_name']);
        $this->assertEquals($withdrawalData['amount'], $decrypted['amount']);
    }

    public function test_masks_sensitive_data()
    {
        $data = '1234567890';
        $masked = EncryptionService::maskSensitiveData($data, '*', 4);

        $this->assertEquals('1234******', $masked);
    }

    public function test_generates_secure_transaction_id()
    {
        $id1 = EncryptionService::generateSecureTransactionId();
        $id2 = EncryptionService::generateSecureTransactionId();

        $this->assertStringStartsWith('TXN_', $id1);
        $this->assertStringStartsWith('TXN_', $id2);
        $this->assertNotEquals($id1, $id2);
    }

    public function test_hashes_transaction_reference()
    {
        $reference = 'TXN_123456';
        $hash1 = EncryptionService::hashTransactionReference($reference);
        $hash2 = EncryptionService::hashTransactionReference($reference);

        $this->assertEquals($hash1, $hash2);
        $this->assertNotEquals($reference, $hash1);
    }
}

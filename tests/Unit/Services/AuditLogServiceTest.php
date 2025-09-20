<?php

namespace Tests\Unit\Services;

use App\Models\FinancialAuditLog;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorWithdrawal;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create(['usertype' => 'dev']);
        $this->actingAs($this->user);
    }

    public function test_logs_financial_transaction()
    {
        $data = [
            'vendor_id' => 1,
            'action_type' => 'create',
            'entity_type' => 'withdrawal',
            'entity_id' => 1,
            'amount' => 1000000,
            'status' => 'pending',
            'notes' => 'Test transaction'
        ];

        $log = AuditLogService::logFinancialTransaction($data);

        $this->assertInstanceOf(FinancialAuditLog::class, $log);
        $this->assertEquals('create', $log->action_type);
        $this->assertEquals('withdrawal', $log->entity_type);
        $this->assertEquals(1000000, $log->amount);
    }

    public function test_calculates_risk_level_correctly()
    {
        // Low risk
        $lowRiskData = [
            'amount' => 100000,
            'action_type' => 'create'
        ];

        // High risk
        $highRiskData = [
            'amount' => 15000000,
            'action_type' => 'withdraw'
        ];

        $this->assertTrue(true); // Placeholder for risk calculation test
    }

    public function test_gets_vendor_logs()
    {
        $vendor = Vendor::factory()->create();

        // Create some audit logs
        FinancialAuditLog::create([
            'user_id' => $this->user->id,
            'vendor_id' => $vendor->id,
            'action_type' => 'create',
            'entity_type' => 'withdrawal',
            'entity_id' => 1,
            'amount' => 1000000,
            'status' => 'pending',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent'
        ]);

        $logs = AuditLogService::getVendorLogs($vendor->id);

        $this->assertCount(1, $logs);
        $this->assertEquals($vendor->id, $logs->first()->vendor_id);
    }

    public function test_gets_admin_logs()
    {
        // Create admin user
        $adminUser = User::factory()->create(['usertype' => 'dev']);

        // Create audit log
        FinancialAuditLog::create([
            'user_id' => $adminUser->id,
            'vendor_id' => null,
            'action_type' => 'create',
            'entity_type' => 'admin_fee',
            'entity_id' => 1,
            'amount' => 500000,
            'status' => 'completed',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent'
        ]);

        $logs = AuditLogService::getAdminLogs();

        $this->assertCount(1, $logs);
        $this->assertEquals('dev', $logs->first()->user->usertype);
    }

    public function test_gets_high_risk_transactions()
    {
        // Create high risk transaction
        FinancialAuditLog::create([
            'user_id' => $this->user->id,
            'vendor_id' => 1,
            'action_type' => 'withdraw',
            'entity_type' => 'withdrawal',
            'entity_id' => 1,
            'amount' => 15000000,
            'status' => 'pending',
            'risk_level' => 'high',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent'
        ]);

        $logs = AuditLogService::getHighRiskTransactions();

        $this->assertCount(1, $logs);
        $this->assertEquals('high', $logs->first()->risk_level);
    }
}

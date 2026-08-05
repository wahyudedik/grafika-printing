<?php

namespace Tests\Unit\Services;

use App\Models\AdminFeeSetting;
use App\Models\User;
use App\Services\AdminFeeService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminFeeServiceTest extends TestCase
{
    protected AdminFeeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdminFeeService();
    }

    // ─── calculatePaymentGatewayFees Tests (Pure Calculation, No DB) ───

    public function test_calculate_payment_gateway_fees_credit_card(): void
    {
        $result = $this->service->calculatePaymentGatewayFees(1000000, 'credit_card');

        $this->assertEquals(0.029, $result['fee_rate']);
        $this->assertEquals(29000, $result['fee_amount']);
        $this->assertEquals('credit_card', $result['payment_method']);
    }

    public function test_calculate_payment_gateway_fees_bank_transfer(): void
    {
        $result = $this->service->calculatePaymentGatewayFees(1000000, 'bank_transfer');

        $this->assertEquals(0.015, $result['fee_rate']);
        $this->assertEquals(15000, $result['fee_amount']);
        $this->assertEquals('bank_transfer', $result['payment_method']);
    }

    public function test_calculate_payment_gateway_fees_ewallet(): void
    {
        $result = $this->service->calculatePaymentGatewayFees(1000000, 'ewallet');

        $this->assertEquals(0.02, $result['fee_rate']);
        $this->assertEquals(20000, $result['fee_amount']);
        $this->assertEquals('ewallet', $result['payment_method']);
    }

    public function test_calculate_payment_gateway_fees_retail_outlet(): void
    {
        $result = $this->service->calculatePaymentGatewayFees(1000000, 'retail_outlet');

        $this->assertEquals(0.01, $result['fee_rate']);
        $this->assertEquals(10000, $result['fee_amount']);
        $this->assertEquals('retail_outlet', $result['payment_method']);
    }

    public function test_calculate_payment_gateway_fees_unknown_method_defaults_to_2_percent(): void
    {
        $result = $this->service->calculatePaymentGatewayFees(1000000, 'unknown_method');

        $this->assertEquals(0.02, $result['fee_rate']);
        $this->assertEquals(20000, $result['fee_amount']);
    }

    public function test_calculate_payment_gateway_fees_zero_amount(): void
    {
        $result = $this->service->calculatePaymentGatewayFees(0, 'bank_transfer');

        $this->assertEquals(0, $result['fee_amount']);
    }

    public function test_calculate_payment_gateway_fees_small_amount(): void
    {
        $result = $this->service->calculatePaymentGatewayFees(10000, 'bank_transfer');

        $this->assertEquals(150, $result['fee_amount']);
    }

    public function test_calculate_payment_gateway_fees_large_amount(): void
    {
        $amount = 100000000; // 100 juta
        $result = $this->service->calculatePaymentGatewayFees($amount, 'bank_transfer');

        $this->assertEquals(1500000, $result['fee_amount']); // 1.5%
    }

    public function test_calculate_payment_gateway_fees_fractional_amount(): void
    {
        $result = $this->service->calculatePaymentGatewayFees(1500.50, 'ewallet');

        // 2% of 1500.50 = 30.01
        $this->assertEqualsWithDelta(30.01, $result['fee_amount'], 0.01);
    }

    public function test_all_payment_gateway_fee_rates_are_within_expected_range(): void
    {
        $amount = 1000000;

        $creditCard = $this->service->calculatePaymentGatewayFees($amount, 'credit_card');
        $bankTransfer = $this->service->calculatePaymentGatewayFees($amount, 'bank_transfer');
        $ewallet = $this->service->calculatePaymentGatewayFees($amount, 'ewallet');
        $retailOutlet = $this->service->calculatePaymentGatewayFees($amount, 'retail_outlet');

        // All rates should be between 0% and 10%
        $this->assertGreaterThan(0, $creditCard['fee_rate']);
        $this->assertLessThanOrEqual(0.10, $creditCard['fee_rate']);
        $this->assertGreaterThan(0, $bankTransfer['fee_rate']);
        $this->assertLessThanOrEqual(0.10, $bankTransfer['fee_rate']);
        $this->assertGreaterThan(0, $ewallet['fee_rate']);
        $this->assertLessThanOrEqual(0.10, $ewallet['fee_rate']);
        $this->assertGreaterThan(0, $retailOutlet['fee_rate']);
        $this->assertLessThanOrEqual(0.10, $retailOutlet['fee_rate']);

        // Credit card should be most expensive, retail outlet cheapest
        $this->assertGreaterThan($bankTransfer['fee_rate'], $creditCard['fee_rate']);
        $this->assertGreaterThan($retailOutlet['fee_rate'], $ewallet['fee_rate']);
    }

    // ─── calculateAdminFees Tests (with DB seeding) ────────────────────

    /**
     * Helper: Seed admin fee settings for DB-dependent tests
     */
    protected function seedAdminFeeSettings(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('admin_fee_settings')) {
            $this->markTestSkipped('admin_fee_settings table not found');
        }

        $existingCount = AdminFeeSetting::count();
        if ($existingCount > 0) {
            return;
        }

        // Create a test user first to satisfy foreign key constraint on created_by
        $adminUser = User::firstOrCreate(
            ['email' => 'admin_fee_test@grafika.test'],
            [
                'name' => 'Admin Fee Test User',
                'password' => bcrypt('password'),
                'usertype' => 'dev',
            ]
        );

        AdminFeeSetting::create([
            'name' => 'Admin Fee 5% Auction',
            'description' => 'Admin fee 5% untuk lelang',
            'type' => 'percentage',
            'value' => 5.00,
            'minimum_amount' => 100000,
            'maximum_amount' => null,
            'is_active' => true,
            'category' => 'auction',
            'effective_from' => null,
            'effective_until' => null,
            'created_by' => $adminUser->id,
        ]);

        AdminFeeSetting::create([
            'name' => 'Admin Fee Fixed POS',
            'description' => 'Admin fee tetap Rp 5.000 untuk transaksi POS',
            'type' => 'fixed',
            'value' => 5000,
            'minimum_amount' => 50000,
            'maximum_amount' => null,
            'is_active' => true,
            'category' => 'pos_transaction',
            'effective_from' => null,
            'effective_until' => null,
            'created_by' => $adminUser->id,
        ]);
    }

    public function test_calculate_admin_fees_returns_fee_for_auction(): void
    {
        $this->seedAdminFeeSettings();

        $result = $this->service->calculateAdminFees(1000000, 'auction');

        $this->assertArrayHasKey('total_fee', $result);
        $this->assertArrayHasKey('fee_breakdown', $result);
        $this->assertArrayHasKey('settings_applied', $result);
        // 5% of 1,000,000 = 50,000 (from seeded data)
        $this->assertEquals(50000, $result['total_fee']);
        $this->assertEquals(1, $result['settings_applied']);
    }

    public function test_calculate_admin_fees_returns_fee_for_pos_transaction(): void
    {
        $this->seedAdminFeeSettings();

        $result = $this->service->calculateAdminFees(200000, 'pos_transaction');

        // Fixed 5000 from seeded data
        $this->assertEquals(5000, $result['total_fee']);
        $this->assertEquals(1, $result['settings_applied']);
    }

    public function test_calculate_admin_fees_below_minimum_returns_zero(): void
    {
        $this->seedAdminFeeSettings();

        // Minimum for auction is 100,000
        $result = $this->service->calculateAdminFees(50000, 'auction');

        $this->assertEquals(0, $result['total_fee']);
    }

    public function test_calculate_fees_is_alias_for_calculate_admin_fees(): void
    {
        $this->seedAdminFeeSettings();

        $result = $this->service->calculateFees(500000, 'pos_transaction');

        $this->assertArrayHasKey('total_fee', $result);
        $this->assertArrayHasKey('fee_breakdown', $result);
        $this->assertArrayHasKey('settings_applied', $result);
        // Fixed 5000 from seeded data
        $this->assertEquals(5000, $result['total_fee']);
    }

    public function test_calculate_total_fees_combines_admin_and_gateway_fees(): void
    {
        $this->seedAdminFeeSettings();

        $result = $this->service->calculateTotalFees(1000000, 'bank_transfer');

        // Admin fee: 5% of 1,000,000 = 50,000
        // Payment gateway: 1.5% of 1,000,000 = 15,000
        $this->assertEquals(1000000, $result['auction_amount']);
        $this->assertEquals(50000, $result['admin_fee']);
        $this->assertEquals(15000, $result['payment_gateway_fee']);
        $this->assertEquals(65000, $result['total_fees']);
        $this->assertEquals(1065000, $result['total_amount']);
        $this->assertEquals(1000000, $result['vendor_receives']);
        $this->assertEquals(65000, $result['admin_receives']);
    }

    public function test_get_fee_preview_returns_formatted_data(): void
    {
        $this->seedAdminFeeSettings();

        $result = $this->service->getFeePreview(2000000, 'bank_transfer');

        $this->assertEquals(2000000, $result['auction_amount']);
        // Admin fee: 5% of 2,000,000 = 100,000
        $this->assertEquals(100000, $result['admin_fee']);
        // bank_transfer fee: 1.5% of 2,000,000 = 30,000
        $this->assertEquals(30000, $result['payment_gateway_fee']);
        $this->assertEquals(130000, $result['total_fees']);
        $this->assertEquals(2130000, $result['total_amount']);
        $this->assertEquals(2000000, $result['vendor_receives']);
        $this->assertEquals(5.0, $result['fee_percentage']); // 100000/2000000 * 100
        $this->assertArrayHasKey('breakdown', $result);
        $this->assertArrayHasKey('admin_fees', $result['breakdown']);
        $this->assertArrayHasKey('payment_gateway', $result['breakdown']);
    }

    public function test_get_fee_preview_zero_amount(): void
    {
        $this->seedAdminFeeSettings();

        $result = $this->service->getFeePreview(0, 'bank_transfer');

        $this->assertEquals(0, $result['fee_percentage']);
        $this->assertEquals(0, $result['total_fees']);
    }

    public function test_update_bid_with_fees_returns_correct_structure(): void
    {
        $this->seedAdminFeeSettings();

        $result = $this->service->updateBidWithFees(750000, 'credit_card');

        $this->assertEquals(750000, $result['bid_amount']);
        // credit_card fee: 2.9% of 750000 = 21750
        $this->assertEquals(21750, $result['payment_gateway_fee']);
        $this->assertEquals(750000, $result['vendor_receives']);
        // total_amount = bid_amount + admin_fee + payment_gateway_fee
        $this->assertGreaterThan($result['bid_amount'], $result['total_amount']);
    }
}

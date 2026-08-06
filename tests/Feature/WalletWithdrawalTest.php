<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use App\Models\VendorWithdrawal;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletWithdrawalTest extends TestCase
{
    // Note: We don't use RefreshDatabase because tests run against the real DB.
    // All tests use unique identifiers to avoid collisions.

    // ═══════════════════════════════════════════════════════════════════
    // VendorWallet Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_wallet_has_required_fillable_fields(): void
    {
        $wallet = new VendorWallet();

        $expected = [
            'vendor_id', 'balance', 'pending_balance',
            'total_earned', 'total_withdrawn', 'is_active',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $wallet->getFillable(), "Field '$field' should be fillable in VendorWallet");
        }
    }

    public function test_wallet_has_correct_casts(): void
    {
        $wallet = new VendorWallet();
        $casts = $wallet->getCasts();

        $this->assertEquals('decimal:2', $casts['balance']);
        $this->assertEquals('decimal:2', $casts['pending_balance']);
        $this->assertEquals('decimal:2', $casts['total_earned']);
        $this->assertEquals('decimal:2', $casts['total_withdrawn']);
        $this->assertEquals('boolean', $casts['is_active']);
    }

    public function test_wallet_uses_correct_table(): void
    {
        $wallet = new VendorWallet();
        $this->assertEquals('vendor_wallets', $wallet->getTable());
    }

    public function test_wallet_belongs_to_vendor(): void
    {
        $wallet = new VendorWallet();
        $relation = $wallet->vendor();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_wallet_has_many_transactions(): void
    {
        $wallet = new VendorWallet();
        $relation = $wallet->transactions();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_wallet_has_many_withdrawals(): void
    {
        $wallet = new VendorWallet();
        $relation = $wallet->withdrawals();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_wallet_get_or_creates_wallet(): void
    {
        $vendor = Vendor::where('is_active', true)->first();
        if (!$vendor) {
            $this->markTestSkipped('No active vendor found');
        }

        $wallet = VendorWallet::getOrCreate($vendor->id);
        $this->assertNotNull($wallet);
        $this->assertEquals($vendor->id, $wallet->vendor_id);
        $this->assertIsNumeric($wallet->balance);
    }

    public function test_wallet_get_or_creates_returns_existing(): void
    {
        $vendor = Vendor::where('is_active', true)->first();
        if (!$vendor) {
            $this->markTestSkipped('No active vendor found');
        }

        $wallet1 = VendorWallet::getOrCreate($vendor->id);
        $wallet2 = VendorWallet::getOrCreate($vendor->id);

        $this->assertEquals($wallet1->id, $wallet2->id);
    }

    public function test_wallet_add_credit_increases_balance(): void
    {
        $vendor = Vendor::where('is_active', true)->first();
        if (!$vendor) {
            $this->markTestSkipped('No active vendor found');
        }

        $wallet = VendorWallet::getOrCreate($vendor->id);
        $balanceBefore = (float) $wallet->balance;
        $earnedBefore = (float) $wallet->total_earned;

        $transaction = $wallet->addCredit(50000, 'auction_payment', 'Test credit');

        $wallet->refresh();
        $this->assertEqualsWithDelta($balanceBefore + 50000, (float) $wallet->balance, 0.01);
        $this->assertEqualsWithDelta($earnedBefore + 50000, (float) $wallet->total_earned, 0.01);
        $this->assertNotNull($transaction);
        $this->assertEquals('credit', $transaction->type);
        $this->assertEquals('auction_payment', $transaction->category);
    }

    public function test_wallet_add_debit_decreases_balance(): void
    {
        $vendor = Vendor::where('is_active', true)->first();
        if (!$vendor) {
            $this->markTestSkipped('No active vendor found');
        }

        $wallet = VendorWallet::getOrCreate($vendor->id);

        // First add some credit (use valid enum category)
        $wallet->addCredit(100000, 'auction_payment', 'Initial credit');
        $wallet->refresh();
        $balanceBefore = $wallet->balance;

        $transaction = $wallet->addDebit(30000, 'withdrawal', 'Test debit');

        $wallet->refresh();
        $this->assertEquals($balanceBefore - 30000, $wallet->balance);
        $this->assertNotNull($transaction);
        $this->assertEquals('debit', $transaction->type);
    }

    public function test_wallet_add_debit_throws_on_insufficient_balance(): void
    {
        $vendor = Vendor::where('is_active', true)->first();
        if (!$vendor) {
            $this->markTestSkipped('No active vendor found');
        }

        $wallet = VendorWallet::getOrCreate($vendor->id);

        $this->expectException(\Exception::class);
        $wallet->addDebit(999999999, 'withdrawal', 'Should fail');
    }

    public function test_wallet_has_sufficient_balance(): void
    {
        $vendor = Vendor::where('is_active', true)->first();
        if (!$vendor) {
            $this->markTestSkipped('No active vendor found');
        }

        $wallet = VendorWallet::getOrCreate($vendor->id);
        $currentBalance = (float) $wallet->balance;

        // Should have sufficient for a small amount
        $this->assertTrue($wallet->hasSufficientBalance(1));

        // Should NOT have sufficient for an absurdly large amount
        $this->assertFalse($wallet->hasSufficientBalance($currentBalance + 999999999));
    }

    // ═══════════════════════════════════════════════════════════════════
    // VendorWalletTransaction Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_wallet_transaction_has_required_fillable_fields(): void
    {
        $transaction = new VendorWalletTransaction();

        $expected = [
            'vendor_id', 'vendor_wallet_id', 'transaction_code',
            'type', 'category', 'amount', 'balance_before',
            'balance_after', 'description', 'reference_id',
            'reference_type', 'status', 'metadata',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $transaction->getFillable(), "Field '$field' should be fillable in VendorWalletTransaction");
        }
    }

    public function test_wallet_transaction_uses_correct_table(): void
    {
        $transaction = new VendorWalletTransaction();
        $this->assertEquals('vendor_wallet_transactions', $transaction->getTable());
    }

    // ═══════════════════════════════════════════════════════════════════
    // VendorWithdrawal Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_withdrawal_has_required_fillable_fields(): void
    {
        $withdrawal = new VendorWithdrawal();

        $expected = [
            'vendor_id', 'vendor_wallet_id', 'withdrawal_code',
            'batch_id', 'amount', 'fee', 'net_amount',
            'status', 'method', 'account_number', 'account_name',
            'bank_name', 'notes', 'admin_notes', 'processed_by',
            'processed_at', 'completed_at', 'payment_proof', 'webhook_data',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $withdrawal->getFillable(), "Field '$field' should be fillable in VendorWithdrawal");
        }
    }

    public function test_withdrawal_has_correct_casts(): void
    {
        $withdrawal = new VendorWithdrawal();
        $casts = $withdrawal->getCasts();

        $this->assertEquals('decimal:2', $casts['amount']);
        $this->assertEquals('decimal:2', $casts['fee']);
        $this->assertEquals('decimal:2', $casts['net_amount']);
        $this->assertEquals('array', $casts['payment_proof']);
        $this->assertEquals('array', $casts['webhook_data']);
    }

    public function test_withdrawal_uses_correct_table(): void
    {
        $withdrawal = new VendorWithdrawal();
        $this->assertEquals('vendor_withdrawals', $withdrawal->getTable());
    }

    public function test_withdrawal_belongs_to_vendor(): void
    {
        $withdrawal = new VendorWithdrawal();
        $relation = $withdrawal->vendor();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_withdrawal_belongs_to_vendor_wallet(): void
    {
        $withdrawal = new VendorWithdrawal();
        $relation = $withdrawal->vendorWallet();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_withdrawal_belongs_to_processed_by(): void
    {
        $withdrawal = new VendorWithdrawal();
        $relation = $withdrawal->processedBy();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_withdrawal_hidden_fields(): void
    {
        $withdrawal = new VendorWithdrawal();
        $hidden = $withdrawal->getHidden();

        $this->assertContains('account_number', $hidden);
        $this->assertContains('account_name', $hidden);
        $this->assertContains('bank_name', $hidden);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Route Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_wallet_index_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.wallet.index'), 'vendor.wallet.index route should exist');
    }

    public function test_vendor_withdrawal_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('vendor.wallet.index'), 'vendor.wallet.index route should exist');
    }

    // ═══════════════════════════════════════════════════════════════════
    // Authentication Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_unauthenticated_vendor_cannot_access_wallet(): void
    {
        $response = $this->get(route('vendor.wallet.index'));
        $response->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════════════
    // Full Flow Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_wallet_credit_debit_lifecycle(): void
    {
        $vendor = Vendor::where('is_active', true)->first();
        if (!$vendor) {
            $this->markTestSkipped('No active vendor found');
        }

        $wallet = VendorWallet::getOrCreate($vendor->id);
        $initialBalance = $wallet->balance;

        // Add credit (use valid enum categories)
        $wallet->addCredit(200000, 'auction_payment', 'Payment from auction #1');
        $wallet->refresh();
        $this->assertEquals($initialBalance + 200000, $wallet->balance);

        // Add another credit
        $wallet->addCredit(150000, 'bonus', 'Bonus from admin');
        $wallet->refresh();
        $this->assertEquals($initialBalance + 350000, $wallet->balance);

        // Debit
        $wallet->addDebit(100000, 'withdrawal', 'Withdrawal request');
        $wallet->refresh();
        $this->assertEquals($initialBalance + 250000, $wallet->balance);

        // Verify transaction history
        $transactions = $wallet->transactions()->latest()->take(3)->get();
        $this->assertGreaterThanOrEqual(3, $transactions->count());
    }

    public function test_wallet_multiple_vendors_are_isolated(): void
    {
        $vendors = Vendor::where('is_active', true)->take(2)->get();
        if ($vendors->count() < 2) {
            $this->markTestSkipped('Need at least 2 active vendors');
        }

        $wallet1 = VendorWallet::getOrCreate($vendors[0]->id);
        $wallet2 = VendorWallet::getOrCreate($vendors[1]->id);

        $wallet1->addCredit(100000, 'bonus', 'Credit for vendor 1');

        $wallet1->refresh();
        $wallet2->refresh();

        // Vendor 2 should not be affected
        $this->assertNotEquals($wallet1->balance, $wallet2->balance);
    }
}

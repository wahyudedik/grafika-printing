<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\XenditPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuctionFlowTest extends TestCase
{
    // Note: We don't use RefreshDatabase because tests run against the real DB.
    // All tests use unique identifiers to avoid collisions.

    // ─── Auction Model Tests ───────────────────────────────────────────

    public function test_auction_has_required_fillable_fields(): void
    {
        $auction = new Auction();

        $expected = [
            'user_id', 'kode', 'title', 'description', 'category',
            'quantity', 'budget', 'admin_fee_amount', 'payment_gateway_fee',
            'total_amount_with_fees', 'vendor_receives', 'admin_receives',
            'fee_breakdown', 'fees_calculated', 'deadline', 'file_path',
            'status', 'winner_vendor_id', 'winning_bid', 'specifications',
            'alamat_pengiriman', 'no_telp', 'email_pengiriman', 'catatan_khusus',
            'metode_pembayaran', 'estimasi_selesai', 'progress_percentage',
            'catatan_vendor', 'transaksi_id', 'pos_integrated', 'rejection_reason',
            'rejected_by', 'rejected_at', 'approved_by', 'approved_at',
            'admin_approval_status', 'admin_approval_date', 'admin_approval_notes',
            'delivery_status', 'tracking_number', 'shipping_cost',
            'user_rating', 'user_feedback', 'completion_date',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $auction->getFillable(), "Field '$field' should be fillable");
        }
    }

    public function test_auction_has_correct_casts(): void
    {
        $auction = new Auction();
        $casts = $auction->getCasts();

        $this->assertEquals('date', $casts['deadline']);
        $this->assertEquals('decimal:2', $casts['budget']);
        $this->assertEquals('decimal:2', $casts['winning_bid']);
        $this->assertEquals('integer', $casts['quantity']);
        $this->assertEquals('boolean', $casts['fees_calculated']);
        $this->assertEquals('boolean', $casts['pos_integrated']);
        $this->assertEquals('array', $casts['fee_breakdown']);
    }

    public function test_auction_is_active_method(): void
    {
        // Create auction with future deadline
        $userId = $this->getTestUserId();
        $auction = $this->createTestAuction($userId, [
            'status' => 'active',
            'deadline' => now()->addDays(7),
        ]);

        $this->assertTrue($auction->isActive());

        // Cleanup
        $auction->delete();
    }

    public function test_auction_is_not_active_when_closed(): void
    {
        $userId = $this->getTestUserId();
        $auction = $this->createTestAuction($userId, [
            'status' => 'closed',
            'deadline' => now()->addDays(7),
        ]);

        $this->assertFalse($auction->isActive());

        $auction->delete();
    }

    public function test_auction_is_not_active_when_deadline_passed(): void
    {
        $userId = $this->getTestUserId();
        $auction = $this->createTestAuction($userId, [
            'status' => 'active',
            'deadline' => now()->subDay(),
        ]);

        $this->assertFalse($auction->isActive());

        $auction->delete();
    }

    public function test_auction_is_closed_method(): void
    {
        $userId = $this->getTestUserId();
        $auction = $this->createTestAuction($userId, [
            'status' => 'closed',
            'deadline' => now()->addDays(7),
        ]);

        $this->assertTrue($auction->isClosed());

        $auction->delete();
    }

    public function test_auction_approve_method(): void
    {
        $userId = $this->getTestUserId();
        $adminId = $this->getTestAdminId();
        $auction = $this->createTestAuction($userId, [
            'admin_approval_status' => 'pending',
            'status' => 'pending',
        ]);

        $auction->approve($adminId, 'Approved for testing');

        $this->assertEquals('approved', $auction->fresh()->admin_approval_status);
        $this->assertEquals('active', $auction->fresh()->status);
        $this->assertEquals($adminId, $auction->fresh()->approved_by);
        $this->assertNotNull($auction->fresh()->admin_approval_date);

        $auction->delete();
    }

    public function test_auction_reject_method(): void
    {
        $userId = $this->getTestUserId();
        $adminId = $this->getTestAdminId();
        $auction = $this->createTestAuction($userId, [
            'admin_approval_status' => 'pending',
            'status' => 'pending',
        ]);

        $auction->reject($adminId, 'Not suitable');

        $this->assertEquals('rejected', $auction->fresh()->admin_approval_status);
        $this->assertEquals('rejected', $auction->fresh()->status);
        $this->assertEquals('Not suitable', $auction->fresh()->admin_approval_notes);

        $auction->delete();
    }

    public function test_auction_mark_as_shipped(): void
    {
        $userId = $this->getTestUserId();
        $auction = $this->createTestAuction($userId, [
            'delivery_status' => 'pending',
        ]);

        $auction->markAsShipped('JNE-123456', 15000);

        $fresh = $auction->fresh();
        $this->assertEquals('shipped', $fresh->delivery_status);
        $this->assertEquals('JNE-123456', $fresh->tracking_number);
        $this->assertEquals(15000, $fresh->shipping_cost);

        $auction->delete();
    }

    public function test_auction_mark_as_delivered(): void
    {
        $userId = $this->getTestUserId();
        $auction = $this->createTestAuction($userId, [
            'delivery_status' => 'shipped',
        ]);

        $auction->markAsDelivered();

        $this->assertEquals('delivered', $auction->fresh()->delivery_status);

        $auction->delete();
    }

    public function test_auction_delivery_status_methods(): void
    {
        $userId = $this->getTestUserId();

        $auction1 = $this->createTestAuction($userId, ['delivery_status' => 'shipped']);
        $this->assertTrue($auction1->isShipped());
        $this->assertFalse($auction1->isDelivered());
        $this->assertFalse($auction1->isCompleted());
        $auction1->delete();

        $auction2 = $this->createTestAuction($userId, ['delivery_status' => 'delivered']);
        $this->assertFalse($auction2->isShipped());
        $this->assertTrue($auction2->isDelivered());
        $this->assertFalse($auction2->isCompleted());
        $auction2->delete();

        $auction3 = $this->createTestAuction($userId, ['delivery_status' => 'completed']);
        $this->assertFalse($auction3->isShipped());
        $this->assertFalse($auction3->isDelivered());
        $this->assertTrue($auction3->isCompleted());
        $auction3->delete();
    }

    public function test_auction_approval_status_methods(): void
    {
        $userId = $this->getTestUserId();

        $auction1 = $this->createTestAuction($userId, ['admin_approval_status' => 'pending']);
        $this->assertTrue($auction1->isPendingApproval());
        $this->assertFalse($auction1->isApproved());
        $this->assertFalse($auction1->isRejected());
        $auction1->delete();

        $auction2 = $this->createTestAuction($userId, ['admin_approval_status' => 'approved']);
        $this->assertFalse($auction2->isPendingApproval());
        $this->assertTrue($auction2->isApproved());
        $this->assertFalse($auction2->isRejected());
        $auction2->delete();

        $auction3 = $this->createTestAuction($userId, ['admin_approval_status' => 'rejected']);
        $this->assertFalse($auction3->isPendingApproval());
        $this->assertFalse($auction3->isApproved());
        $this->assertTrue($auction3->isRejected());
        $auction3->delete();
    }

    public function test_auction_belongs_to_user(): void
    {
        $userId = $this->getTestUserId();
        $auction = $this->createTestAuction($userId);

        $this->assertNotNull($auction->user);
        $this->assertEquals($userId, $auction->user->id);

        $auction->delete();
    }

    // ─── AuctionBid Model Tests ────────────────────────────────────────

    public function test_auction_bid_has_required_fields(): void
    {
        $bid = new AuctionBid();

        $expected = ['auction_id', 'vendor_id', 'bid_amount', 'message', 'status'];

        foreach ($expected as $field) {
            $this->assertContains($field, $bid->getFillable(), "Field '$field' should be fillable");
        }
    }

    public function test_auction_bid_is_pending_by_default(): void
    {
        $userId = $this->getTestUserId();
        $vendorId = $this->getTestVendorId();
        $auction = $this->createTestAuction($userId);
        $bid = $this->createTestBid($auction->id, $vendorId, [
            'bid_amount' => 500000,
            'status' => 'pending',
        ]);

        $this->assertTrue($bid->isPending());
        $this->assertFalse($bid->isAccepted());

        $bid->delete();
        $auction->delete();
    }

    public function test_auction_bid_belongs_to_auction(): void
    {
        $userId = $this->getTestUserId();
        $vendorId = $this->getTestVendorId();
        $auction = $this->createTestAuction($userId);
        $bid = $this->createTestBid($auction->id, $vendorId, ['bid_amount' => 500000]);

        $this->assertNotNull($bid->auction);
        $this->assertEquals($auction->id, $bid->auction->id);

        $bid->delete();
        $auction->delete();
    }

    public function test_auction_get_lowest_bid(): void
    {
        $userId = $this->getTestUserId();
        $vendorId = $this->getTestVendorId();
        $auction = $this->createTestAuction($userId);

        $bid1 = $this->createTestBid($auction->id, $vendorId, [
            'bid_amount' => 500000,
            'status' => 'pending',
        ]);
        $bid2 = $this->createTestBid($auction->id, $vendorId, [
            'bid_amount' => 450000,
            'status' => 'pending',
        ]);

        $lowest = $auction->getLowestBid();
        $this->assertEquals(450000, $lowest);

        $bid1->delete();
        $bid2->delete();
        $auction->delete();
    }

    public function test_auction_get_bid_count(): void
    {
        $userId = $this->getTestUserId();
        $vendorId = $this->getTestVendorId();
        $auction = $this->createTestAuction($userId);

        $bid1 = $this->createTestBid($auction->id, $vendorId, ['bid_amount' => 500000, 'status' => 'pending']);
        $bid2 = $this->createTestBid($auction->id, $vendorId, ['bid_amount' => 450000, 'status' => 'pending']);

        $this->assertEquals(2, $auction->getBidCount());

        $bid1->delete();
        $bid2->delete();
        $auction->delete();
    }

    // ─── XenditPayment Model Tests ─────────────────────────────────────

    public function test_xendit_payment_has_required_fields(): void
    {
        $payment = new XenditPayment();

        $expected = [
            'external_id', 'xendit_id', 'amount', 'description',
            'customer_name', 'customer_email', 'status', 'checkout_url',
            'expires_at', 'paid_at', 'payment_method', 'failure_reason',
            'auction_id',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $payment->getFillable(), "Field '$field' should be fillable");
        }
    }

    public function test_xendit_payment_status_text(): void
    {
        $payment = new XenditPayment();

        $payment->status = 'pending';
        $this->assertEquals('Menunggu Pembayaran', $payment->status_text);

        $payment->status = 'paid';
        $this->assertEquals('Berhasil Dibayar', $payment->status_text);

        $payment->status = 'expired';
        $this->assertEquals('Kadaluarsa', $payment->status_text);

        $payment->status = 'failed';
        $this->assertEquals('Gagal', $payment->status_text);
    }

    public function test_xendit_payment_status_badge_class(): void
    {
        $payment = new XenditPayment();

        $payment->status = 'pending';
        $this->assertEquals('badge-warning', $payment->status_badge_class);

        $payment->status = 'paid';
        $this->assertEquals('badge-success', $payment->status_badge_class);

        $payment->status = 'expired';
        $this->assertEquals('badge-danger', $payment->status_badge_class);
    }

    public function test_xendit_payment_is_paid_method(): void
    {
        $payment = new XenditPayment();
        $payment->status = 'paid';
        $this->assertTrue($payment->isPaid());

        $payment->status = 'pending';
        $this->assertFalse($payment->isPaid());
    }

    public function test_xendit_payment_belongs_to_auction(): void
    {
        $userId = $this->getTestUserId();
        $auction = $this->createTestAuction($userId);
        $payment = $this->createTestPayment($auction->id, $userId, [
            'status' => 'pending',
            'amount' => 1000000,
        ]);

        $this->assertNotNull($payment->auction);
        $this->assertEquals($auction->id, $payment->auction->id);

        $payment->delete();
        $auction->delete();
    }

    // ─── Auction Flow Integration Tests ────────────────────────────────

    public function test_auction_full_lifecycle_model_operations(): void
    {
        $userId = $this->getTestUserId();
        $adminId = $this->getTestAdminId();
        $vendorId = $this->getTestVendorId();

        // Step 1: Create auction (simulating AuctionController@store)
        $auction = $this->createTestAuction($userId, [
            'status' => 'pending',
            'admin_approval_status' => 'pending',
            'budget' => 1000000,
        ]);
        $this->assertEquals('pending', $auction->admin_approval_status);
        $this->assertEquals('pending', $auction->status);

        // Step 2: Admin approves auction
        $auction->approve($adminId, 'Looks good');
        $auction = $auction->fresh();
        $this->assertEquals('approved', $auction->admin_approval_status);
        $this->assertEquals('active', $auction->status);
        $this->assertTrue($auction->isActive());

        // Step 3: Vendor places bid
        $bid = $this->createTestBid($auction->id, $vendorId, [
            'bid_amount' => 950000,
            'status' => 'pending',
        ]);
        $this->assertEquals(1, $auction->fresh()->getBidCount());
        $this->assertEquals(950000, $auction->fresh()->getLowestBid());

        // Step 4: User selects winner (update auction)
        $auction->update([
            'winner_vendor_id' => $vendorId,
            'winning_bid' => 950000,
            'status' => 'closed',
        ]);
        $auction = $auction->fresh();
        $this->assertEquals($vendorId, $auction->winner_vendor_id);
        $this->assertEquals(950000, $auction->winning_bid);
        $this->assertEquals('closed', $auction->status);

        // Step 5: Payment created
        $payment = $this->createTestPayment($auction->id, $userId, [
            'status' => 'pending',
            'amount' => 950000,
        ]);
        $this->assertNotNull($auction->fresh()->latestPayment);

        // Step 6: Vendor ships
        $auction->markAsShipped('JNE-99999', 15000);
        $auction = $auction->fresh();
        $this->assertEquals('shipped', $auction->delivery_status);
        $this->assertEquals('JNE-99999', $auction->tracking_number);

        // Step 7: Mark as delivered
        $auction->markAsDelivered();
        $this->assertEquals('delivered', $auction->fresh()->delivery_status);

        // Cleanup
        $payment->delete();
        $bid->delete();
        $auction->delete();
    }

    public function test_auction_rejection_flow(): void
    {
        $userId = $this->getTestUserId();
        $adminId = $this->getTestAdminId();

        $auction = $this->createTestAuction($userId, [
            'admin_approval_status' => 'pending',
            'status' => 'pending',
        ]);

        $auction->reject($adminId, 'Budget terlalu rendah');

        $auction = $auction->fresh();
        $this->assertEquals('rejected', $auction->admin_approval_status);
        $this->assertEquals('rejected', $auction->status);
        $this->assertEquals('Budget terlalu rendah', $auction->admin_approval_notes);

        $auction->delete();
    }

    public function test_multiple_bids_on_same_auction(): void
    {
        $userId = $this->getTestUserId();
        $vendorId = $this->getTestVendorId();
        $auction = $this->createTestAuction($userId, ['status' => 'active']);

        $bid1 = $this->createTestBid($auction->id, $vendorId, ['bid_amount' => 800000, 'status' => 'pending']);
        $bid2 = $this->createTestBid($auction->id, $vendorId, ['bid_amount' => 750000, 'status' => 'pending']);
        $bid3 = $this->createTestBid($auction->id, $vendorId, ['bid_amount' => 900000, 'status' => 'pending']);

        $this->assertEquals(3, $auction->fresh()->getBidCount());
        $this->assertEquals(750000, $auction->fresh()->getLowestBid());

        $bid1->delete();
        $bid2->delete();
        $bid3->delete();
        $auction->delete();
    }

    // ─── Route Tests ───────────────────────────────────────────────────

    public function test_user_auction_routes_are_registered(): void
    {
        // Test that auction resource routes exist for users
        $routeNames = [
            'user.auctions.index',
            'user.auctions.create',
            'user.auctions.store',
            'user.auctions.show',
            'user.auctions.edit',
            'user.auctions.update',
            'user.auctions.destroy',
        ];

        foreach ($routeNames as $routeName) {
            $this->assertNotNull(
                \Route::getRoutes()->getByName($routeName),
                "Route '$routeName' should be registered"
            );
        }
    }

    public function test_admin_auction_routes_are_registered(): void
    {
        $routeNames = [
            'admin.auctions.index',
            'admin.auctions.show',
            'admin.auctions.approve',
            'admin.auctions.reject',
        ];

        foreach ($routeNames as $routeName) {
            $this->assertNotNull(
                \Route::getRoutes()->getByName($routeName),
                "Route '$routeName' should be registered"
            );
        }
    }

    public function test_vendor_auction_bid_routes_are_registered(): void
    {
        $routeNames = [
            'vendor.auctions.index',
            'vendor.auctions.show',
            'vendor.auctions.bid',
            'vendor.auctions.store-bid',
        ];

        foreach ($routeNames as $routeName) {
            $this->assertNotNull(
                \Route::getRoutes()->getByName($routeName),
                "Route '$routeName' should be registered"
            );
        }
    }

    public function test_payment_confirmation_routes_are_registered(): void
    {
        $routeNames = [
            'user.payments.confirmation',
            'user.payments.process',
            'user.payments.success',
            'user.payments.failure',
        ];

        foreach ($routeNames as $routeName) {
            $this->assertNotNull(
                \Route::getRoutes()->getByName($routeName),
                "Route '$routeName' should be registered"
            );
        }
    }

    public function test_delivery_confirmation_routes_are_registered(): void
    {
        $routeNames = [
            'user.delivery-confirmation.index',
            'user.delivery-confirmation.create',
            'user.delivery-confirmation.store',
            'user.delivery-confirmation.show',
        ];

        foreach ($routeNames as $routeName) {
            $this->assertNotNull(
                \Route::getRoutes()->getByName($routeName),
                "Route '$routeName' should be registered"
            );
        }
    }

    // ─── Unauthenticated Access Tests ──────────────────────────────────

    public function test_unauthenticated_user_cannot_access_auction_create(): void
    {
        $response = $this->get(route('user.auctions.create'));
        $response->assertRedirect();
    }

    public function test_unauthenticated_user_cannot_access_admin_auctions(): void
    {
        $response = $this->get(route('admin.auctions.index'));
        $response->assertRedirect();
    }

    public function test_unauthenticated_vendor_cannot_access_vendor_auctions(): void
    {
        $response = $this->get(route('vendor.auctions.index'));
        $response->assertRedirect();
    }

    // ─── Helpers ───────────────────────────────────────────────────────

    /**
     * Get or create a test user with 'user' type.
     */
    protected function getTestUserId(): int
    {
        $user = User::firstOrCreate(
            ['email' => 'auction_flow_test_user@grafika.test'],
            [
                'name' => 'Auction Flow Test User',
                'password' => bcrypt('password'),
                'usertype' => 'user',
            ]
        );

        return $user->id;
    }

    /**
     * Get or create a test admin user with 'dev' type.
     */
    protected function getTestAdminId(): int
    {
        $admin = User::firstOrCreate(
            ['email' => 'auction_flow_test_admin@grafika.test'],
            [
                'name' => 'Auction Flow Test Admin',
                'password' => bcrypt('password'),
                'usertype' => 'dev',
            ]
        );

        return $admin->id;
    }

    /**
     * Get or create a test vendor.
     */
    protected function getTestVendorId(): int
    {
        $vendor = Vendor::withoutGlobalScope('active')->firstOrCreate(
            ['email' => 'auction_flow_test_vendor@grafika.test'],
            [
                'name' => 'Auction Flow Test Vendor',
                'phone' => '081234567890',
                'address' => 'Jl. Test No. 1',
                'is_active' => true,
            ]
        );

        return $vendor->id;
    }

    /**
     * Create a test auction with given overrides.
     */
    protected function createTestAuction(int $userId, array $overrides = []): Auction
    {
        $defaults = [
            'user_id' => $userId,
            'kode' => 'AUCTION-TEST-' . strtoupper(uniqid()),
            'title' => 'Test Auction ' . uniqid(),
            'description' => 'Test auction description',
            'category' => ' cetak',
            'quantity' => 100,
            'budget' => 1000000,
            'deadline' => now()->addDays(7),
            'status' => 'pending',
            'admin_approval_status' => 'pending',
            'alamat_pengiriman' => 'Jl. Test No. 1, Jakarta',
            'no_telp' => '081234567890',
            'metode_pembayaran' => 'auction_win',
            'progress_percentage' => 0,
            'pos_integrated' => false,
        ];

        return Auction::create(array_merge($defaults, $overrides));
    }

    /**
     * Create a test bid.
     */
    protected function createTestBid(int $auctionId, int $vendorId, array $overrides = []): AuctionBid
    {
        $defaults = [
            'auction_id' => $auctionId,
            'vendor_id' => $vendorId,
            'bid_amount' => 500000,
            'message' => 'Test bid',
            'status' => 'pending',
        ];

        return AuctionBid::create(array_merge($defaults, $overrides));
    }

    /**
     * Create a test Xendit payment.
     */
    protected function createTestPayment(int $auctionId, int $userId, array $overrides = []): XenditPayment
    {
        $defaults = [
            'external_id' => 'TEST_PAYMENT_' . strtoupper(uniqid()),
            'xendit_id' => 'xen_test_' . uniqid(),
            'type' => 'payment_link',
            'amount' => 1000000,
            'currency' => 'IDR',
            'description' => 'Test payment',
            'customer' => ['given_name' => 'Test Customer', 'email' => 'customer@test.com'],
            'status' => 'pending',
            'checkout_url' => 'https://checkout.test.com/' . uniqid(),
            'auction_id' => $auctionId,
            'user_id' => $userId,
        ];

        return XenditPayment::create(array_merge($defaults, $overrides));
    }
}

<?php

namespace Tests\Unit\Services;

use App\Models\Auction;
use App\Models\EscrowPayment;
use App\Models\MediationRequest;
use App\Models\OrderTracking;
use App\Models\VendorRating;
use App\Services\OrderTrackingService;
use App\Services\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class OrderTrackingServiceTest extends TestCase
{
    protected OrderTrackingService $service;
    protected $tenantManagerMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManagerMock = Mockery::mock(TenantManager::class);
        $this->service = new OrderTrackingService($this->tenantManagerMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // ─── Status Constants Tests ────────────────────────────────────────

    public function test_order_tracking_has_all_status_constants(): void
    {
        $this->assertEquals('payment_received', OrderTracking::STATUS_PAYMENT_RECEIVED);
        $this->assertEquals('order_accepted', OrderTracking::STATUS_ORDER_ACCEPTED);
        $this->assertEquals('production_started', OrderTracking::STATUS_PRODUCTION_STARTED);
        $this->assertEquals('production_completed', OrderTracking::STATUS_PRODUCTION_COMPLETED);
        $this->assertEquals('quality_check', OrderTracking::STATUS_QUALITY_CHECK);
        $this->assertEquals('packaging', OrderTracking::STATUS_PACKAGING);
        $this->assertEquals('shipped', OrderTracking::STATUS_SHIPPED);
        $this->assertEquals('delivered', OrderTracking::STATUS_DELIVERED);
        $this->assertEquals('completed', OrderTracking::STATUS_COMPLETED);
        $this->assertEquals('mediation', OrderTracking::STATUS_MEDIATION);
    }

    public function test_escrow_payment_has_all_status_constants(): void
    {
        $this->assertEquals('pending', EscrowPayment::STATUS_PENDING);
        $this->assertEquals('released', EscrowPayment::STATUS_RELEASED);
        $this->assertEquals('withdrawn', EscrowPayment::STATUS_WITHDRAWN);
        $this->assertEquals('disputed', EscrowPayment::STATUS_DISPUTED);
        $this->assertEquals('refunded', EscrowPayment::STATUS_REFUNDED);
    }

    // ─── Status Label Tests ────────────────────────────────────────────

    public function test_order_tracking_status_labels(): void
    {
        $statuses = [
            'payment_received' => 'Payment Received',
            'order_accepted' => 'Order Accepted',
            'production_started' => 'Production Started',
            'production_completed' => 'Production Completed',
            'quality_check' => 'Quality Check',
            'packaging' => 'Packaging',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'mediation' => 'Mediation',
        ];

        foreach ($statuses as $status => $expectedLabel) {
            $tracking = new OrderTracking(['status' => $status]);
            $this->assertEquals($expectedLabel, $tracking->status_label);
        }
    }

    public function test_order_tracking_unknown_status_label(): void
    {
        $tracking = new OrderTracking(['status' => 'unknown_status']);
        $this->assertEquals('Unknown', $tracking->status_label);
    }

    // ─── Escrow Status Label Tests ─────────────────────────────────────

    public function test_escrow_payment_status_labels(): void
    {
        $statuses = [
            'pending' => 'Pending Release',
            'released' => 'Released to Vendor',
            'withdrawn' => 'Withdrawn by Vendor',
            'disputed' => 'Under Dispute',
            'refunded' => 'Refunded to User',
        ];

        foreach ($statuses as $status => $expectedLabel) {
            $escrow = new EscrowPayment(['status' => $status]);
            $this->assertEquals($expectedLabel, $escrow->status_label);
        }
    }

    public function test_escrow_payment_status_colors(): void
    {
        $colorMap = [
            'pending' => 'yellow',
            'released' => 'green',
            'withdrawn' => 'blue',
            'disputed' => 'red',
            'refunded' => 'gray',
        ];

        foreach ($colorMap as $status => $expectedColor) {
            $escrow = new EscrowPayment(['status' => $status]);
            $this->assertEquals($expectedColor, $escrow->status_color);
        }
    }

    // ─── EscrowPayment Helper Method Tests ─────────────────────────────

    public function test_escrow_payment_is_pending(): void
    {
        $escrow = new EscrowPayment(['status' => 'pending']);
        $this->assertTrue($escrow->isPending());
    }

    public function test_escrow_payment_is_not_pending_when_released(): void
    {
        $escrow = new EscrowPayment(['status' => 'released']);
        $this->assertFalse($escrow->isPending());
    }

    // ─── DeliveryConfirmation Status Tests ─────────────────────────────

    public function test_delivery_confirmation_status_labels(): void
    {
        $statuses = [
            'pending' => 'Menunggu Konfirmasi',
            'delivered' => 'Barang Diterima',
            'confirmed' => 'Dikonfirmasi',
            'disputed' => 'Ada Masalah',
            'resolved' => 'Selesai',
        ];

        foreach ($statuses as $status => $expectedLabel) {
            $confirmation = new \App\Models\DeliveryConfirmation(['delivery_status' => $status]);
            $this->assertEquals($expectedLabel, $confirmation->status_label);
        }
    }

    public function test_delivery_confirmation_status_colors(): void
    {
        $colorMap = [
            'pending' => 'warning',
            'delivered' => 'info',
            'confirmed' => 'success',
            'disputed' => 'danger',
        ];

        foreach ($colorMap as $status => $expectedColor) {
            $confirmation = new \App\Models\DeliveryConfirmation(['delivery_status' => $status]);
            $this->assertEquals($expectedColor, $confirmation->status_color);
        }
    }

    public function test_delivery_confirmation_helper_methods(): void
    {
        $pending = new \App\Models\DeliveryConfirmation(['delivery_status' => 'pending']);
        $this->assertFalse($pending->isDelivered());
        $this->assertFalse($pending->isConfirmed());
        $this->assertFalse($pending->hasDispute());

        $delivered = new \App\Models\DeliveryConfirmation(['delivery_status' => 'delivered']);
        $this->assertTrue($delivered->isDelivered());
        $this->assertFalse($delivered->isConfirmed());

        $confirmed = new \App\Models\DeliveryConfirmation(['delivery_status' => 'confirmed']);
        $this->assertTrue($confirmed->isConfirmed());

        $disputed = new \App\Models\DeliveryConfirmation(['delivery_status' => 'disputed']);
        $this->assertTrue($disputed->hasDispute());
    }

    // ─── MediationRequest Status Tests ─────────────────────────────────

    public function test_mediation_request_has_pending_status_constant(): void
    {
        $this->assertEquals('pending', MediationRequest::STATUS_PENDING);
    }

    // ─── OrderTracking Model Fillable & Casts ──────────────────────────

    public function test_order_tracking_model_has_correct_fillable_fields(): void
    {
        $model = new OrderTracking();
        $fillable = $model->getFillable();

        $expectedFields = [
            'auction_id', 'vendor_id', 'user_id', 'status',
            'status_description', 'tracking_number', 'estimated_delivery',
            'actual_delivery', 'notes', 'admin_notes',
            'is_mediation_requested', 'mediation_reason',
            'mediation_status', 'mediation_resolution',
            'created_by', 'updated_by',
        ];

        foreach ($expectedFields as $field) {
            $this->assertContains($field, $fillable, "Field '{$field}' should be fillable");
        }
    }

    public function test_order_tracking_model_has_correct_casts(): void
    {
        $model = new OrderTracking();
        $casts = $model->getCasts();

        $this->assertArrayHasKey('estimated_delivery', $casts);
        $this->assertArrayHasKey('actual_delivery', $casts);
        $this->assertArrayHasKey('is_mediation_requested', $casts);
        $this->assertEquals('datetime', $casts['estimated_delivery']);
        $this->assertEquals('boolean', $casts['is_mediation_requested']);
    }

    // ─── EscrowPayment Model Fillable & Casts ──────────────────────────

    public function test_escrow_payment_model_has_correct_fillable_fields(): void
    {
        $model = new EscrowPayment();
        $fillable = $model->getFillable();

        $expectedFields = [
            'auction_id', 'vendor_id', 'user_id', 'amount',
            'admin_fee', 'vendor_amount', 'status',
            'released_at', 'release_reason', 'admin_notes',
            'created_by', 'updated_by',
        ];

        foreach ($expectedFields as $field) {
            $this->assertContains($field, $fillable, "Field '{$field}' should be fillable");
        }
    }

    public function test_escrow_payment_model_has_correct_casts(): void
    {
        $model = new EscrowPayment();
        $casts = $model->getCasts();

        $this->assertEquals('decimal:2', $casts['amount']);
        $this->assertEquals('decimal:2', $casts['admin_fee']);
        $this->assertEquals('decimal:2', $casts['vendor_amount']);
        $this->assertEquals('datetime', $casts['released_at']);
    }

    // ─── DeliveryConfirmation Model Tests ──────────────────────────────

    public function test_delivery_confirmation_model_has_correct_fillable_fields(): void
    {
        $model = new \App\Models\DeliveryConfirmation();
        $fillable = $model->getFillable();

        $expectedFields = [
            'auction_id', 'user_id', 'vendor_id', 'delivery_status',
            'delivery_date', 'delivery_notes', 'user_rating', 'user_feedback',
            'photos', 'confirmed_at', 'dispute_reason', 'dispute_resolved_at',
        ];

        foreach ($expectedFields as $field) {
            $this->assertContains($field, $fillable, "Field '{$field}' should be fillable");
        }
    }

    public function test_delivery_confirmation_model_has_correct_casts(): void
    {
        $model = new \App\Models\DeliveryConfirmation();
        $casts = $model->getCasts();

        $this->assertEquals('datetime', $casts['delivery_date']);
        $this->assertEquals('datetime', $casts['confirmed_at']);
        $this->assertEquals('array', $casts['photos']);
    }

    // ─── OrderTracking Fillable Integrity ──────────────────────────────

    public function test_order_tracking_fillable_does_not_include_timestamps(): void
    {
        $model = new OrderTracking();
        $fillable = $model->getFillable();

        $this->assertNotContains('created_at', $fillable);
        $this->assertNotContains('updated_at', $fillable);
    }

    public function test_escrow_payment_fillable_does_not_include_timestamps(): void
    {
        $model = new EscrowPayment();
        $fillable = $model->getFillable();

        $this->assertNotContains('created_at', $fillable);
        $this->assertNotContains('updated_at', $fillable);
    }
}

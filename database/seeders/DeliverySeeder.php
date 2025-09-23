<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderTracking;
use App\Models\DeliveryConfirmation;
use App\Models\ShippingInvoice;
use App\Models\VendorRating;
use App\Models\Auction;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📦 Creating delivery tracking data...');

        $auctions = Auction::where('status', 'active')->get();

        foreach ($auctions as $auction) {
            $this->createDeliveryData($auction);
        }

        $this->command->info('✅ Delivery seeding completed successfully!');
    }

    private function createDeliveryData($auction)
    {
        $statuses = [
            OrderTracking::STATUS_PAYMENT_RECEIVED,
            OrderTracking::STATUS_ORDER_ACCEPTED,
            OrderTracking::STATUS_PRODUCTION_STARTED,
            OrderTracking::STATUS_PRODUCTION_COMPLETED,
            OrderTracking::STATUS_QUALITY_CHECK,
            OrderTracking::STATUS_PACKAGING,
            OrderTracking::STATUS_SHIPPED,
            OrderTracking::STATUS_DELIVERED,
            OrderTracking::STATUS_COMPLETED
        ];

        $randomStatus = $statuses[array_rand($statuses)];

        // Create order tracking
        $orderTracking = OrderTracking::create([
            'auction_id' => $auction->id,
            'vendor_id' => $auction->bids->first()->vendor_id ?? Vendor::first()->id,
            'user_id' => $auction->user_id,
            'status' => $randomStatus,
            'status_description' => $this->getStatusDescription($randomStatus),
            'tracking_number' => 'TRK-' . strtoupper(Str::random(10)),
            'estimated_delivery' => now()->addDays(rand(1, 7)),
            'notes' => 'Order tracking created automatically',
            'uuid' => Str::uuid()->toString(),
        ]);

        // Create delivery confirmation if delivered
        if (in_array($randomStatus, [OrderTracking::STATUS_DELIVERED, OrderTracking::STATUS_COMPLETED])) {
            DeliveryConfirmation::create([
                'auction_id' => $auction->id,
                'vendor_id' => $orderTracking->vendor_id,
                'user_id' => $auction->user_id,
                'delivery_status' => 'delivered',
                'delivery_notes' => 'Package delivered successfully',
                'delivery_photo' => 'delivery_photo_' . time() . '.jpg',
                'uuid' => Str::uuid()->toString(),
            ]);
        }

        // Create shipping invoice if shipped
        if (in_array($randomStatus, [OrderTracking::STATUS_SHIPPED, OrderTracking::STATUS_DELIVERED, OrderTracking::STATUS_COMPLETED])) {
            ShippingInvoice::create([
                'auction_id' => $auction->id,
                'vendor_id' => $orderTracking->vendor_id,
                'user_id' => $auction->user_id,
                'kode' => 'SHIP-' . strtoupper(Str::random(8)),
                'courier' => ['JNE', 'TIKI', 'POS', 'J&T'][rand(0, 3)],
                'service' => ['REG', 'EXPRESS', 'ONS'][rand(0, 2)],
                'weight' => rand(1, 10),
                'origin_city' => 'Jakarta',
                'destination_city' => 'Bandung',
                'origin_address' => 'Jl. Vendor Address, Jakarta',
                'destination_address' => 'Jl. Customer Address, Bandung',
                'shipping_cost' => rand(10000, 50000),
                'status' => 'paid',
                'uuid' => Str::uuid()->toString(),
            ]);
        }

        // Create vendor rating if completed
        if ($randomStatus === OrderTracking::STATUS_COMPLETED) {
            VendorRating::create([
                'vendor_id' => $orderTracking->vendor_id,
                'user_id' => $auction->user_id,
                'auction_id' => $auction->id,
                'rating' => rand(3, 5),
                'comment' => 'Good service and quality',
                'is_verified' => true,
                'uuid' => Str::uuid()->toString(),
            ]);
        }
    }

    private function getStatusDescription($status)
    {
        $descriptions = [
            OrderTracking::STATUS_PAYMENT_RECEIVED => 'Payment has been received and verified',
            OrderTracking::STATUS_ORDER_ACCEPTED => 'Order has been accepted by vendor',
            OrderTracking::STATUS_PRODUCTION_STARTED => 'Production process has started',
            OrderTracking::STATUS_PRODUCTION_COMPLETED => 'Production process has been completed',
            OrderTracking::STATUS_QUALITY_CHECK => 'Quality check is in progress',
            OrderTracking::STATUS_PACKAGING => 'Order is being packaged for shipping',
            OrderTracking::STATUS_SHIPPED => 'Order has been shipped',
            OrderTracking::STATUS_DELIVERED => 'Order has been delivered',
            OrderTracking::STATUS_COMPLETED => 'Order has been completed'
        ];

        return $descriptions[$status] ?? 'Status updated';
    }
}

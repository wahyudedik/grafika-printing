<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auction;
use App\Models\DeliveryConfirmation;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\VendorWalletTransaction;
use Illuminate\Support\Facades\DB;

class DeliveryConfirmationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            $this->createDeliveryConfirmations();
            $this->processVendorPayments();
            
            DB::commit();
            $this->command->info('✅ Delivery confirmation dummy data created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error creating delivery confirmation dummy data: ' . $e->getMessage());
        }
    }

    private function createDeliveryConfirmations()
    {
        $this->command->info('Creating delivery confirmations...');
        
        $paidAuctions = Auction::where('status', 'paid')->get();
        
        foreach ($paidAuctions as $auction) {
            // 70% chance of having delivery confirmation
            if (rand(1, 100) <= 70) {
                $deliveryStatus = $this->getRandomDeliveryStatus();
                $rating = $this->getRandomRating($deliveryStatus);
                $feedback = $this->getRandomFeedback($deliveryStatus);
                $photos = $this->getRandomPhotos($deliveryStatus);
                
                $confirmation = DeliveryConfirmation::create([
                    'auction_id' => $auction->id,
                    'user_id' => $auction->user_id,
                    'vendor_id' => $auction->winner_vendor_id,
                    'delivery_status' => $deliveryStatus,
                    'delivery_date' => now()->subDays(rand(1, 30)),
                    'delivery_notes' => $this->getRandomDeliveryNotes($deliveryStatus),
                    'user_rating' => $rating,
                    'user_feedback' => $feedback,
                    'photos' => $photos,
                    'confirmed_at' => $deliveryStatus === 'delivered' ? now()->subDays(rand(1, 15)) : null,
                    'dispute_reason' => $deliveryStatus === 'disputed' ? $this->getRandomDisputeReason() : null,
                    'dispute_resolved_at' => $deliveryStatus === 'resolved' ? now()->subDays(rand(1, 10)) : null,
                    'created_at' => now()->subDays(rand(1, 30))
                ]);
                
                // Update auction status based on delivery confirmation
                if ($deliveryStatus === 'delivered') {
                    $auction->update(['status' => 'completed']);
                } elseif ($deliveryStatus === 'disputed') {
                    $auction->update(['status' => 'disputed']);
                }
            }
        }
    }

    private function processVendorPayments()
    {
        $this->command->info('Processing vendor payments...');
        
        $confirmedDeliveries = DeliveryConfirmation::where('delivery_status', 'delivered')->get();
        
        foreach ($confirmedDeliveries as $confirmation) {
            $auction = $confirmation->auction;
            $vendor = $confirmation->vendor;
            
            // Get or create vendor wallet
            $wallet = VendorWallet::firstOrCreate(['vendor_id' => $vendor->id]);
            
            // Calculate amount to transfer (auction amount - admin fees)
            $amount = $auction->winning_bid;
            if ($auction->admin_fee_amount) {
                $amount = $auction->winning_bid - $auction->admin_fee_amount;
            }
            
            // Add to vendor wallet
            $wallet->addCredit(
                $amount,
                'auction_payment',
                'Payment from auction #' . $auction->id . ' (after delivery confirmation)',
                $auction->id,
                'auction',
                [
                    'auction_id' => $auction->id,
                    'user_id' => $auction->user_id,
                    'delivery_confirmation_id' => $confirmation->id,
                    'admin_fee_deducted' => $auction->admin_fee_amount,
                    'shipping_paid_by_user' => 'cash_on_delivery'
                ]
            );
            
            // Create wallet transaction record
            VendorWalletTransaction::create([
                'vendor_wallet_id' => $wallet->id,
                'vendor_id' => $vendor->id,
                'type' => 'credit',
                'amount' => $amount,
                'category' => 'auction_payment',
                'description' => 'Payment from auction #' . $auction->id . ' (after delivery confirmation)',
                'reference_id' => $auction->id,
                'reference_type' => 'auction',
                'metadata' => [
                    'auction_id' => $auction->id,
                    'user_id' => $auction->user_id,
                    'delivery_confirmation_id' => $confirmation->id,
                    'admin_fee_deducted' => $auction->admin_fee_amount,
                    'shipping_paid_by_user' => 'cash_on_delivery'
                ],
                'status' => 'completed',
                'created_at' => $confirmation->confirmed_at
            ]);
        }
    }

    private function getRandomDeliveryStatus()
    {
        $statuses = ['delivered', 'disputed', 'resolved'];
        $weights = [80, 15, 5]; // 80% delivered, 15% disputed, 5% resolved
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'delivered';
    }

    private function getRandomRating($status)
    {
        if ($status === 'delivered') {
            return rand(3, 5); // 3-5 stars for delivered
        } elseif ($status === 'disputed') {
            return rand(1, 2); // 1-2 stars for disputed
        }
        return rand(4, 5); // 4-5 stars for resolved
    }

    private function getRandomFeedback($status)
    {
        $feedbacks = [
            'delivered' => [
                'Barang sesuai dengan pesanan, kualitas bagus!',
                'Pengiriman cepat, hasil cetak memuaskan.',
                'Vendor sangat profesional, hasil sesuai ekspektasi.',
                'Kualitas cetak sangat baik, akan order lagi.',
                'Pelayanan memuaskan, hasil sesuai dengan yang diinginkan.',
                'Vendor responsif dan hasil cetak berkualitas tinggi.',
                'Pengiriman tepat waktu, kualitas sesuai standar.',
                'Hasil cetak sangat memuaskan, terima kasih!'
            ],
            'disputed' => [
                'Barang tidak sesuai dengan pesanan.',
                'Kualitas cetak kurang memuaskan.',
                'Pengiriman terlambat dari yang dijanjikan.',
                'Barang rusak saat diterima.',
                'Hasil cetak tidak sesuai dengan spesifikasi.',
                'Vendor tidak responsif terhadap keluhan.',
                'Barang tidak sesuai dengan yang dipesan.',
                'Kualitas cetak di bawah standar.'
            ],
            'resolved' => [
                'Masalah sudah diselesaikan dengan baik.',
                'Vendor memberikan solusi yang memuaskan.',
                'Setelah revisi, hasil menjadi lebih baik.',
                'Masalah teratasi dengan baik oleh vendor.',
                'Vendor sangat kooperatif dalam menyelesaikan masalah.',
                'Setelah diskusi, masalah berhasil diselesaikan.',
                'Vendor memberikan kompensasi yang sesuai.',
                'Masalah sudah teratasi dengan memuaskan.'
            ]
        ];
        
        $statusFeedbacks = $feedbacks[$status] ?? $feedbacks['delivered'];
        return $statusFeedbacks[array_rand($statusFeedbacks)];
    }

    private function getRandomPhotos($status)
    {
        if ($status === 'delivered') {
            $photos = [
                'delivery_proofs/photo1.jpg',
                'delivery_proofs/photo2.jpg',
                'delivery_proofs/photo3.jpg'
            ];
            return array_slice($photos, 0, rand(1, 3));
        } elseif ($status === 'disputed') {
            $photos = [
                'dispute_proofs/issue1.jpg',
                'dispute_proofs/issue2.jpg',
                'dispute_proofs/issue3.jpg'
            ];
            return array_slice($photos, 0, rand(1, 2));
        }
        return [];
    }

    private function getRandomDeliveryNotes($status)
    {
        $notes = [
            'delivered' => [
                'Barang diterima dengan baik, sesuai pesanan.',
                'Pengiriman tepat waktu, barang dalam kondisi baik.',
                'Barang sesuai dengan spesifikasi yang diminta.',
                'Pengiriman cepat, barang berkualitas.',
                'Barang diterima dengan kondisi sempurna.'
            ],
            'disputed' => [
                'Barang tidak sesuai dengan pesanan.',
                'Kualitas cetak tidak memuaskan.',
                'Pengiriman terlambat dari jadwal.',
                'Barang rusak saat diterima.',
                'Hasil cetak tidak sesuai spesifikasi.'
            ],
            'resolved' => [
                'Masalah sudah diselesaikan dengan baik.',
                'Vendor memberikan solusi yang memuaskan.',
                'Setelah revisi, hasil menjadi lebih baik.',
                'Masalah teratasi dengan baik.',
                'Vendor sangat kooperatif.'
            ]
        ];
        
        $statusNotes = $notes[$status] ?? $notes['delivered'];
        return $statusNotes[array_rand($statusNotes)];
    }

    private function getRandomDisputeReason()
    {
        $reasons = [
            'Barang tidak sesuai dengan pesanan',
            'Kualitas cetak tidak memuaskan',
            'Pengiriman terlambat dari jadwal',
            'Barang rusak saat diterima',
            'Hasil cetak tidak sesuai spesifikasi',
            'Vendor tidak responsif',
            'Barang tidak sesuai dengan yang dipesan',
            'Kualitas cetak di bawah standar'
        ];
        
        return $reasons[array_rand($reasons)];
    }
}

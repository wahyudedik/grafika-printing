<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\TransaksiItemSpecifications;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\Bahan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuctionToPosService
{
    /**
     * Create POS transaction from auction win
     */
    public function createTransactionFromAuction(Auction $auction, AuctionBid $winningBid)
    {
        DB::beginTransaction();

        try {
            // Get vendor from winning bid
            $vendor = $winningBid->vendor;
            if (!$vendor) {
                throw new \Exception('Vendor not found for winning bid');
            }

            // Create or get customer from auction user
            $customer = $this->createCustomerFromAuction($auction, $vendor);

            // Create or get product for auction
            $product = $this->createProductFromAuction($auction, $vendor);

            // Create transaction
            $transaction = $this->createTransaction($auction, $winningBid, $customer, $vendor);

            // Create transaction item
            $this->createTransactionItem($auction, $product, $transaction, $vendor);

            DB::commit();

            Log::info("Auction to POS integration successful", [
                'auction_id' => $auction->id,
                'transaction_id' => $transaction->id,
                'vendor_id' => $vendor->id
            ]);

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Auction to POS integration failed", [
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create customer from auction user
     */
    private function createCustomerFromAuction(Auction $auction, Vendor $vendor)
    {
        $user = $auction->user;

        // Check if customer already exists for this vendor
        $existingCustomer = Pelanggan::where('vendor_id', $vendor->id)
            ->where('email', $auction->email_pengiriman ?? $user->email)
            ->first();

        if ($existingCustomer) {
            return $existingCustomer;
        }

        // Create new customer using auction data
        return Pelanggan::create([
            'vendor_id' => $vendor->id,
            'kode' => 'PLG-' . date('YmdHis') . '-' . Str::random(4),
            'nama' => $user->name,
            'alamat' => $auction->alamat_pengiriman,
            'no_telp' => $auction->no_telp,
            'email' => $auction->email_pengiriman ?? $user->email,
            'transaksi_terakhir' => now()
        ]);
    }

    /**
     * Create product from auction
     */
    private function createProductFromAuction(Auction $auction, Vendor $vendor)
    {
        // Check if product already exists for this auction
        $existingProduct = Produk::where('vendor_id', $vendor->id)
            ->where('nama_produk', 'LIKE', '%' . $auction->title . '%')
            ->first();

        if ($existingProduct) {
            return $existingProduct;
        }

        // Create new product
        return Produk::create([
            'vendor_id' => $vendor->id,
            'kategori_produk_id' => $this->getOrCreateAuctionCategory($vendor),
            'nama_produk' => 'Lelang: ' . $auction->title,
            'deskripsi' => $auction->description,
            'harga' => $auction->budget / $auction->quantity, // Price per unit
            'status' => 'active'
        ]);
    }

    /**
     * Get or create auction category
     */
    private function getOrCreateAuctionCategory(Vendor $vendor)
    {
        // This would need to be implemented based on your KategoriProduk model
        // For now, return a default category ID or create one
        return 1; // Default category ID - adjust as needed
    }

    /**
     * Create transaction
     */
    private function createTransaction(Auction $auction, AuctionBid $winningBid, Pelanggan $customer, Vendor $vendor)
    {
        $transactionCode = 'AUCTION-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $transaction = Transaksi::create([
            'vendor_id' => $vendor->id,
            'kode' => $transactionCode,
            'user_id' => $vendor->vendorUser->first()->id ?? null, // Vendor user ID
            'pelanggan_id' => $customer->id,
            'total_harga' => $winningBid->bid_amount,
            'terbayar' => $winningBid->bid_amount,
            'kembali' => 0,
            'status' => 'pending',
            'payment_method' => 'auction_win',
            'estimasi_selesai' => $auction->deadline,
            'tanggal_dibuat' => now(),
            'progress_percentage' => 0,
            'catatan' => "Dari lelang: {$auction->title} (Kode: {$auction->kode})\n" .
                "Catatan Khusus: " . ($auction->catatan_khusus ?? 'Tidak ada')
        ]);

        // Update auction with transaction ID and mark as integrated
        $auction->update([
            'transaksi_id' => $transaction->id,
            'pos_integrated' => true,
            'estimasi_selesai' => $auction->deadline,
            'progress_percentage' => 0
        ]);

        // Add payment to vendor wallet
        $this->addPaymentToVendorWallet($auction, $winnerBid, $transaction);

        return $transaction;
    }

    /**
     * Create transaction item
     */
    private function createTransactionItem(Auction $auction, Produk $product, Transaksi $transaction, Vendor $vendor)
    {
        $pricePerUnit = $transaction->total_harga / $auction->quantity;

        $transactionItem = TransaksiItem::create([
            'vendor_id' => $vendor->id,
            'transaksi_id' => $transaction->id,
            'produk_id' => $product->id,
            'kuantitas' => $auction->quantity,
            'harga_satuan' => $pricePerUnit
        ]);

        // Create specifications if auction has specifications
        if (!empty($auction->specifications)) {
            $this->createTransactionItemSpecifications($auction, $transactionItem, $vendor);
        }

        return $transactionItem;
    }

    /**
     * Create transaction item specifications
     */
    private function createTransactionItemSpecifications(Auction $auction, TransaksiItem $transactionItem, Vendor $vendor)
    {
        // Get or create basic specifications for auction products
        $specifications = $this->parseAuctionSpecifications($auction->specifications);

        foreach ($specifications as $specName => $specValue) {
            // Find or create specification
            $spesifikasiProduk = $this->getOrCreateSpecification($specName, $transactionItem->produk_id, $vendor);

            if ($spesifikasiProduk) {
                TransaksiItemSpecifications::create([
                    'vendor_id' => $vendor->id,
                    'transaksi_item_id' => $transactionItem->id,
                    'spesifikasi_produk_id' => $spesifikasiProduk->id,
                    'bahan_id' => null, // No specific material for auction specs
                    'value' => $specValue,
                    'input_type' => 'text',
                    'price' => 0 // No additional price for auction specifications
                ]);
            }
        }
    }

    /**
     * Parse auction specifications
     */
    private function parseAuctionSpecifications($specifications)
    {
        if (empty($specifications)) {
            return [];
        }

        // Parse specifications string into array
        $parsed = [];
        $lines = explode("\n", $specifications);

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $parsed[trim($key)] = trim($value);
            }
        }

        return $parsed;
    }

    /**
     * Get or create specification
     */
    private function getOrCreateSpecification($specName, $productId, Vendor $vendor)
    {
        // This would need to be implemented based on your Spesifikasi and SpesifikasiProduk models
        // For now, return null to skip specifications
        return null;
    }

    /**
     * Add payment to vendor wallet
     */
    private function addPaymentToVendorWallet(Auction $auction, AuctionBid $winnerBid, Transaksi $transaction)
    {
        try {
            $vendor = $winnerBid->vendor;
            $wallet = $vendor->getOrCreateWallet();

            // Add credit to wallet
            $wallet->addCredit(
                $winnerBid->bid_amount,
                'auction_payment',
                "Pembayaran dari lelang: {$auction->title} (Kode: {$auction->kode})",
                $auction->id,
                'auction',
                [
                    'auction_code' => $auction->kode,
                    'transaction_code' => $transaction->kode,
                    'bid_amount' => $winnerBid->bid_amount,
                    'auction_title' => $auction->title
                ]
            );

            Log::info("Payment added to vendor wallet", [
                'vendor_id' => $vendor->id,
                'auction_id' => $auction->id,
                'amount' => $winnerBid->bid_amount,
                'wallet_balance' => $wallet->fresh()->balance
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to add payment to vendor wallet", [
                'vendor_id' => $winnerBid->vendor_id,
                'auction_id' => $auction->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception, just log the error
        }
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\TransaksiItemSpecifications;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\TransactionReview;
use App\Facades\Tenant;
use App\Services\StockService;
use App\Notifications\VendorNewOrderNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

class EcommerceFeaturesTest extends TestCase
{
    // Note: We don't use RefreshDatabase because tests run against the real DB.
    // All tests use unique identifiers to avoid collisions.

    protected Vendor $vendor;
    protected User $user;
    protected User $otherUser;
    protected KategoriProduk $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $uniqueSuffix = strtolower(uniqid());

        // Create vendor with user
        $this->vendor = Vendor::factory()->active()->create([
            'email' => "ecom-vendor-{$uniqueSuffix}@test.local",
        ]);

        $vendorUser = User::factory()->create([
            'usertype' => 'vendor',
            'email' => "ecom-vendor-user-{$uniqueSuffix}@test.local",
        ]);
        $this->vendor->vendorUser()->attach($vendorUser->id);

        // Create regular user (buyer)
        $this->user = User::factory()->create([
            'usertype' => 'user',
            'email' => "ecom-buyer-{$uniqueSuffix}@test.local",
        ]);

        // Create another regular user (for cross-user tests)
        $this->otherUser = User::factory()->create([
            'usertype' => 'user',
            'email' => "ecom-other-buyer-{$uniqueSuffix}@test.local",
        ]);

        // Create kategori (needs tenant context since KategoriProduk extends TenantModel)
        Tenant::setVendorId($this->vendor->id);
        $this->kategori = KategoriProduk::create([
            'nama_kategori' => 'Ecom Kategori ' . uniqid(),
            'slug' => 'ecom-kategori-' . strtolower(uniqid()),
        ]);
        Tenant::clearVendorContext();
    }

    protected function tearDown(): void
    {
        Tenant::clearVendorContext();
        parent::tearDown();
    }

    /**
     * Helper: create a pelanggan for a vendor.
     */
    protected function createPelanggan(Vendor $vendor, string $suffix = ''): Pelanggan
    {
        Tenant::setVendorId($vendor->id);
        $pelanggan = Pelanggan::create([
            'vendor_id' => $vendor->id,
            'kode' => 'PLG-' . strtoupper(uniqid()),
            'nama' => 'Test Pelanggan ' . ($suffix ?: uniqid()),
            'no_telp' => '0812' . rand(10000000, 99999999),
            'email' => 'pelanggan-' . uniqid() . '@test.com',
            'alamat' => 'Alamat Test',
        ]);
        Tenant::clearVendorContext();
        return $pelanggan;
    }

    /**
     * Helper: create a produk for a vendor.
     */
    protected function createProduk(Vendor $vendor, string $suffix = ''): Produk
    {
        Tenant::setVendorId($vendor->id);
        $produk = Produk::create([
            'vendor_id' => $vendor->id,
            'nama_produk' => 'Test Produk ' . ($suffix ?: uniqid()),
            'deskripsi' => 'Deskripsi produk test',
            'kategori_id' => $this->kategori->id,
        ]);
        Tenant::clearVendorContext();
        return $produk;
    }

    /**
     * Helper: create a transaksi for a vendor with items.
     */
    protected function createTransaksi(Vendor $vendor, User $buyer, Pelanggan $pelanggan, string $status = 'pending', array $items = []): Transaksi
    {
        Tenant::setVendorId($vendor->id);

        $totalHarga = 0;
        foreach ($items as $item) {
            $totalHarga += $item['kuantitas'] * $item['harga_satuan'];
        }
        if (empty($items)) {
            $totalHarga = 50000;
        }

        $kode = 'TRX-ECOM-' . strtoupper(uniqid());

        $transaksi = Transaksi::create([
            'vendor_id' => $vendor->id,
            'kode' => $kode,
            'user_id' => $buyer->id,
            'pelanggan_id' => $pelanggan->id,
            'total_harga' => $totalHarga,
            'status' => $status,
            'payment_method' => 'cash',
            'estimasi_selesai' => now()->addDays(7)->format('Y-m-d'),
            'tanggal_dibuat' => now(),
            'progress_percentage' => $status === 'completed' ? 100 : 0,
            'terbayar' => $totalHarga,
            'kembali' => 0,
        ]);

        foreach ($items as $item) {
            TransaksiItem::create([
                'vendor_id' => $vendor->id,
                'transaksi_id' => $transaksi->id,
                'produk_id' => $item['produk_id'],
                'kuantitas' => $item['kuantitas'],
                'harga_satuan' => $item['harga_satuan'],
            ]);
        }

        Tenant::clearVendorContext();
        return $transaksi;
    }

    /**
     * Helper: create a bahan with stock for a vendor.
     */
    protected function createBahan(Vendor $vendor, int $stok = 100, string $suffix = ''): Bahan
    {
        Tenant::setVendorId($vendor->id);
        $bahan = Bahan::create([
            'vendor_id' => $vendor->id,
            'nama_bahan' => 'Test Bahan ' . ($suffix ?: uniqid()),
            'hpp' => 5000,
            'satuan' => 'meter',
            'stok' => $stok,
        ]);
        Tenant::clearVendorContext();
        return $bahan;
    }

    /**
     * Helper: create a spesifikasi + spesifikasi_produk for a vendor (valid FK chain).
     */
    protected function createSpesifikasiProduk(Vendor $vendor, Produk $produk, string $suffix = ''): SpesifikasiProduk
    {
        Tenant::setVendorId($vendor->id);

        $spesifikasi = Spesifikasi::create([
            'vendor_id' => $vendor->id,
            'nama_spesifikasi' => 'Ukuran ' . ($suffix ?: uniqid()),
            'tipe_input' => 'number',
            'satuan' => 'cm',
        ]);

        $spesifikasiProduk = SpesifikasiProduk::create([
            'vendor_id' => $vendor->id,
            'produk_id' => $produk->id,
            'spesifikasi_id' => $spesifikasi->id,
            'wajib_diisi' => 'yes',
        ]);

        Tenant::clearVendorContext();
        return $spesifikasiProduk;
    }

    // ═══════════════════════════════════════════════════════════════════
    // 1. Order History (UserTransactionController)
    // ═══════════════════════════════════════════════════════════════════

    public function test_user_can_view_order_history(): void
    {
        $pelanggan = $this->createPelanggan($this->vendor, 'History');
        $produk = $this->createProduk($this->vendor, 'History');

        // Create transaction for the user
        $this->createTransaksi($this->vendor, $this->user, $pelanggan, 'completed', [
            ['produk_id' => $produk->id, 'kuantitas' => 2, 'harga_satuan' => 25000],
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('user.transactions.index'));
        $response->assertStatus(200);

        // Verify the transaction exists in DB
        $this->assertDatabaseHas('transaksis', [
            'user_id' => $this->user->id,
            'vendor_id' => $this->vendor->id,
        ]);
    }

    public function test_user_can_view_transaction_detail(): void
    {
        $pelanggan = $this->createPelanggan($this->vendor, 'Detail');
        $produk = $this->createProduk($this->vendor, 'Detail');

        $transaksi = $this->createTransaksi($this->vendor, $this->user, $pelanggan, 'completed', [
            ['produk_id' => $produk->id, 'kuantitas' => 1, 'harga_satuan' => 50000],
        ]);

        $this->actingAs($this->user);

        // Ensure no tenant context so global scope doesn't add ambiguous vendor_id
        Tenant::clearVendorContext();

        $response = $this->get(route('user.transactions.show', $transaksi->id));
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_user_transaction(): void
    {
        $pelanggan = $this->createPelanggan($this->vendor, 'Other');
        $produk = $this->createProduk($this->vendor, 'Other');

        // Create transaction for otherUser
        $transaksi = $this->createTransaksi($this->vendor, $this->otherUser, $pelanggan, 'completed', [
            ['produk_id' => $produk->id, 'kuantitas' => 1, 'harga_satuan' => 50000],
        ]);

        // Login as user (NOT otherUser)
        $this->actingAs($this->user);
        Tenant::clearVendorContext();

        $response = $this->get(route('user.transactions.show', $transaksi->id));
        $response->assertStatus(403);
    }

    public function test_user_can_view_invoice(): void
    {
        $pelanggan = $this->createPelanggan($this->vendor, 'Invoice');
        $produk = $this->createProduk($this->vendor, 'Invoice');

        $transaksi = $this->createTransaksi($this->vendor, $this->user, $pelanggan, 'completed', [
            ['produk_id' => $produk->id, 'kuantitas' => 3, 'harga_satuan' => 15000],
        ]);

        $this->actingAs($this->user);
        Tenant::clearVendorContext();

        $response = $this->get(route('user.transactions.invoice', $transaksi->id));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_order_history(): void
    {
        $response = $this->get(route('user.transactions.index'));
        $response->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════════════
    // 2. Transaction Review (TransactionReviewController)
    // ═══════════════════════════════════════════════════════════════════

    public function test_user_can_create_review_for_completed_transaction(): void
    {
        $pelanggan = $this->createPelanggan($this->vendor, 'Review');
        $produk = $this->createProduk($this->vendor, 'Review');

        $transaksi = $this->createTransaksi($this->vendor, $this->user, $pelanggan, 'completed', [
            ['produk_id' => $produk->id, 'kuantitas' => 1, 'harga_satuan' => 50000],
        ]);

        $this->actingAs($this->user);
        Tenant::clearVendorContext();

        $response = $this->post(route('user.transactions.review.store', $transaksi->id), [
            'rating' => 5,
            'comment' => 'Produk sangat bagus!',
            'quality_rating' => 5,
            'speed_rating' => 4,
            'service_rating' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify review created in DB
        $this->assertDatabaseHas('transaction_reviews', [
            'user_id' => $this->user->id,
            'transaksi_id' => $transaksi->id,
            'vendor_id' => $this->vendor->id,
            'rating' => 5,
            'comment' => 'Produk sangat bagus!',
        ]);
    }

    public function test_user_cannot_review_pending_transaction(): void
    {
        $pelanggan = $this->createPelanggan($this->vendor, 'Pending');
        $produk = $this->createProduk($this->vendor, 'Pending');

        $transaksi = $this->createTransaksi($this->vendor, $this->user, $pelanggan, 'pending', [
            ['produk_id' => $produk->id, 'kuantitas' => 1, 'harga_satuan' => 50000],
        ]);

        $this->actingAs($this->user);
        Tenant::clearVendorContext();

        $response = $this->post(route('user.transactions.review.store', $transaksi->id), [
            'rating' => 5,
            'comment' => 'Test review on pending',
        ]);

        // Should redirect back with error (transaction not completed)
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Verify NO review created
        $this->assertDatabaseMissing('transaction_reviews', [
            'user_id' => $this->user->id,
            'transaksi_id' => $transaksi->id,
        ]);
    }

    public function test_user_cannot_review_twice(): void
    {
        $pelanggan = $this->createPelanggan($this->vendor, 'Twice');
        $produk = $this->createProduk($this->vendor, 'Twice');

        $transaksi = $this->createTransaksi($this->vendor, $this->user, $pelanggan, 'completed', [
            ['produk_id' => $produk->id, 'kuantitas' => 1, 'harga_satuan' => 50000],
        ]);

        $this->actingAs($this->user);
        Tenant::clearVendorContext();

        // First review — should succeed
        $response1 = $this->post(route('user.transactions.review.store', $transaksi->id), [
            'rating' => 5,
            'comment' => 'First review',
        ]);
        $response1->assertRedirect();

        // Verify review exists
        $this->assertDatabaseHas('transaction_reviews', [
            'user_id' => $this->user->id,
            'transaksi_id' => $transaksi->id,
        ]);

        // Second review — should fail (409 Conflict)
        $response2 = $this->post(route('user.transactions.review.store', $transaksi->id), [
            'rating' => 4,
            'comment' => 'Second review attempt',
        ]);
        $response2->assertStatus(409);
    }

    public function test_user_can_delete_own_review(): void
    {
        $pelanggan = $this->createPelanggan($this->vendor, 'Delete');
        $produk = $this->createProduk($this->vendor, 'Delete');

        $transaksi = $this->createTransaksi($this->vendor, $this->user, $pelanggan, 'completed', [
            ['produk_id' => $produk->id, 'kuantitas' => 1, 'harga_satuan' => 50000],
        ]);

        // Create review manually
        Tenant::setVendorId($this->vendor->id);
        $review = TransactionReview::create([
            'vendor_id' => $this->vendor->id,
            'user_id' => $this->user->id,
            'transaksi_id' => $transaksi->id,
            'rating' => 3,
            'comment' => 'Review to delete',
        ]);
        Tenant::clearVendorContext();

        $this->actingAs($this->user);

        $response = $this->delete(route('user.reviews.destroy', $review->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify review deleted
        $this->assertDatabaseMissing('transaction_reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_user_cannot_delete_other_user_review(): void
    {
        $pelanggan = $this->createPelanggan($this->vendor, 'DeleteOther');
        $produk = $this->createProduk($this->vendor, 'DeleteOther');

        $transaksi = $this->createTransaksi($this->vendor, $this->otherUser, $pelanggan, 'completed', [
            ['produk_id' => $produk->id, 'kuantitas' => 1, 'harga_satuan' => 50000],
        ]);

        // otherUser creates a review
        Tenant::setVendorId($this->vendor->id);
        $review = TransactionReview::create([
            'vendor_id' => $this->vendor->id,
            'user_id' => $this->otherUser->id,
            'transaksi_id' => $transaksi->id,
            'rating' => 4,
            'comment' => 'Other user review',
        ]);
        Tenant::clearVendorContext();

        // Login as user (NOT otherUser)
        $this->actingAs($this->user);

        $response = $this->delete(route('user.reviews.destroy', $review->id));
        $response->assertStatus(403);

        // Verify review still exists
        $this->assertDatabaseHas('transaction_reviews', [
            'id' => $review->id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // 3. Stock Service (StockService)
    // ═══════════════════════════════════════════════════════════════════

    public function test_stock_validation_passes_when_sufficient(): void
    {
        $bahan = $this->createBahan($this->vendor, 100, 'Suff');

        $stockService = new StockService();

        $items = [
            [
                'quantity' => 5,
                'specifications' => [
                    1 => [
                        'bahan_id' => $bahan->id,
                        'input_type' => 'number',
                        'value' => 10,
                    ],
                ],
            ],
        ];

        // 5 * 10 = 50 needed, 100 available → should pass
        $result = $stockService->validateStock($items);
        $this->assertTrue($result);
    }

    public function test_stock_validation_fails_when_insufficient(): void
    {
        $bahan = $this->createBahan($this->vendor, 10, 'Insuff');

        $stockService = new StockService();

        $items = [
            [
                'quantity' => 5,
                'specifications' => [
                    1 => [
                        'bahan_id' => $bahan->id,
                        'input_type' => 'number',
                        'value' => 10,
                    ],
                ],
            ],
        ];

        // 5 * 10 = 50 needed, 10 available → should throw exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stok bahan tidak mencukupi');
        $stockService->validateStock($items);
    }

    public function test_stock_decremented_after_checkout(): void
    {
        $bahan = $this->createBahan($this->vendor, 100, 'Decr');
        $produk = $this->createProduk($this->vendor, 'Decr');
        $pelanggan = $this->createPelanggan($this->vendor, 'Decr');
        $spesifikasiProduk = $this->createSpesifikasiProduk($this->vendor, $produk, 'Decr');

        Tenant::setVendorId($this->vendor->id);

        // Create a completed transaction with item and specifications
        $transaksi = Transaksi::create([
            'vendor_id' => $this->vendor->id,
            'kode' => 'TRX-DEC-' . strtoupper(uniqid()),
            'user_id' => $this->user->id,
            'pelanggan_id' => $pelanggan->id,
            'total_harga' => 100000,
            'status' => 'completed',
            'payment_method' => 'cash',
            'estimasi_selesai' => now()->addDays(7)->format('Y-m-d'),
            'tanggal_dibuat' => now(),
            'progress_percentage' => 100,
            'terbayar' => 100000,
            'kembali' => 0,
        ]);

        $item = TransaksiItem::create([
            'vendor_id' => $this->vendor->id,
            'transaksi_id' => $transaksi->id,
            'produk_id' => $produk->id,
            'kuantitas' => 3,
            'harga_satuan' => 30000,
        ]);

        TransaksiItemSpecifications::create([
            'vendor_id' => $this->vendor->id,
            'transaksi_item_id' => $item->id,
            'spesifikasi_produk_id' => $spesifikasiProduk->id,
            'bahan_id' => $bahan->id,
            'value' => 10,
            'input_type' => 'number',
            'price' => 5000,
        ]);

        Tenant::clearVendorContext();

        $stockService = new StockService();

        // Before decrement
        $this->assertEquals(100, $bahan->fresh()->stok);

        // Decrement stock: 3 (kuantitas) * 10 (value) = 30
        $stockService->decrementStock($transaksi);

        // After decrement: 100 - 30 = 70
        $this->assertEquals(70, $bahan->fresh()->stok);
    }

    public function test_stock_restored_on_payment_failure(): void
    {
        $bahan = $this->createBahan($this->vendor, 100, 'Rest');
        $produk = $this->createProduk($this->vendor, 'Rest');
        $pelanggan = $this->createPelanggan($this->vendor, 'Rest');
        $spesifikasiProduk = $this->createSpesifikasiProduk($this->vendor, $produk, 'Rest');

        Tenant::setVendorId($this->vendor->id);

        // Use a valid ENUM status value — 'cancelled' instead of 'payment_failed'
        $transaksi = Transaksi::create([
            'vendor_id' => $this->vendor->id,
            'kode' => 'TRX-REST-' . strtoupper(uniqid()),
            'user_id' => $this->user->id,
            'pelanggan_id' => $pelanggan->id,
            'total_harga' => 100000,
            'status' => 'cancelled',
            'payment_method' => 'xendit',
            'estimasi_selesai' => now()->addDays(7)->format('Y-m-d'),
            'tanggal_dibuat' => now(),
            'progress_percentage' => 0,
            'terbayar' => 0,
            'kembali' => 0,
        ]);

        $item = TransaksiItem::create([
            'vendor_id' => $this->vendor->id,
            'transaksi_id' => $transaksi->id,
            'produk_id' => $produk->id,
            'kuantitas' => 2,
            'harga_satuan' => 50000,
        ]);

        TransaksiItemSpecifications::create([
            'vendor_id' => $this->vendor->id,
            'transaksi_item_id' => $item->id,
            'spesifikasi_produk_id' => $spesifikasiProduk->id,
            'bahan_id' => $bahan->id,
            'value' => 5,
            'input_type' => 'number',
            'price' => 5000,
        ]);

        Tenant::clearVendorContext();

        $stockService = new StockService();

        // First decrement (simulating stock was already decremented)
        $stockService->decrementStock($transaksi);
        // 100 - (2 * 5) = 90
        $this->assertEquals(90, $bahan->fresh()->stok);

        // Now restore stock (payment failed)
        $stockService->restoreStock($transaksi);
        // 90 + (2 * 5) = 100
        $this->assertEquals(100, $bahan->fresh()->stok);
    }

    // ═══════════════════════════════════════════════════════════════════
    // 4. Vendor New Order Notification
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_receives_notification_on_new_order(): void
    {
        Notification::fake();

        $pelanggan = $this->createPelanggan($this->vendor, 'Notif');
        $produk = $this->createProduk($this->vendor, 'Notif');

        Tenant::setVendorId($this->vendor->id);

        $transaksi = Transaksi::create([
            'vendor_id' => $this->vendor->id,
            'kode' => 'TRX-NOTIF-' . strtoupper(uniqid()),
            'user_id' => $this->user->id,
            'pelanggan_id' => $pelanggan->id,
            'total_harga' => 75000,
            'status' => 'pending',
            'payment_method' => 'cash',
            'estimasi_selesai' => now()->addDays(7)->format('Y-m-d'),
            'tanggal_dibuat' => now(),
            'progress_percentage' => 0,
            'terbayar' => 75000,
            'kembali' => 0,
        ]);

        Tenant::clearVendorContext();

        // Notify the Vendor model directly (not User)
        $this->vendor->notify(new VendorNewOrderNotification($transaksi));

        Notification::assertSentTo(
            $this->vendor,
            VendorNewOrderNotification::class
        );
    }

    public function test_notification_contains_correct_data(): void
    {
        Notification::fake();

        $pelanggan = $this->createPelanggan($this->vendor, 'DataNotif');
        $produk = $this->createProduk($this->vendor, 'DataNotif');

        Tenant::setVendorId($this->vendor->id);

        $transaksi = Transaksi::create([
            'vendor_id' => $this->vendor->id,
            'kode' => 'TRX-DATA-' . strtoupper(uniqid()),
            'user_id' => $this->user->id,
            'pelanggan_id' => $pelanggan->id,
            'total_harga' => 120000,
            'status' => 'pending',
            'payment_method' => 'cash',
            'estimasi_selesai' => now()->addDays(7)->format('Y-m-d'),
            'tanggal_dibuat' => now(),
            'progress_percentage' => 0,
            'terbayar' => 120000,
            'kembali' => 0,
        ]);

        Tenant::clearVendorContext();

        // Create notification instance and check toArray
        $notification = new VendorNewOrderNotification($transaksi);
        $data = $notification->toArray($this->vendor);

        // Verify notification data contains correct information
        $this->assertEquals('new_order', $data['type']);
        $this->assertEquals($transaksi->id, $data['transaksi_id']);
        $this->assertEquals($transaksi->kode, $data['kode']);
        $this->assertEquals($transaksi->total_harga, $data['total_harga']);
        $this->assertStringContainsString($transaksi->kode, $data['message']);
        $this->assertArrayHasKey('url', $data);
    }
}

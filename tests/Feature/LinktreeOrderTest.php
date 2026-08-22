<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor\LinktreeOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * LinktreeOrderTest - Test untuk Linktree Order Flow
 *
 * Test ini cover seluruh alur pesanan dari linktree:
 * 1. Public page menampilkan produk
 * 2. Product detail API
 * 3. Store order
 * 4. Order validation
 * 5. Order success page
 * 6. Vendor orders list
 * 7. Vendor order detail
 * 8. Vendor update order status
 * 9. Vendor update payment status
 * 10. WhatsApp URL generation
 *
 * Note: Tidak menggunakan RefreshDatabase karena test berjalan melawan real DB.
 * Semua test menggunakan unique identifier untuk menghindari kolisi.
 */
class LinktreeOrderTest extends TestCase
{
    protected int $vendorId;
    protected int $userId;
    protected int $linktreeId;
    protected int $linktreeProductId;
    protected int $produkId;
    protected string $customUrl;
    protected ?string $orderUuid = null;
    protected string $uniqueSuffix;
    protected string $produkName;
    protected ?User $vendorUser = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Create unique identifiers untuk menghindari kolisi
        $this->uniqueSuffix = 'ltorder_' . substr(md5(uniqid(mt_rand(), true)), 0, 8);

        // Create vendor via DB (bypass TenantModel)
        $this->vendorId = DB::table('vendors')->insertGetId([
            'name' => "Test Vendor {$this->uniqueSuffix}",
            'email' => "{$this->uniqueSuffix}@test.com",
            'phone' => '08' . substr(md5($this->uniqueSuffix), 0, 10),
            'address' => 'Jl. Test No. 1',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create user via factory
        $this->vendorUser = User::factory()->create([
            'usertype' => 'vendor',
            'name' => "Vendor User {$this->uniqueSuffix}",
        ]);
        $this->userId = $this->vendorUser->id;

        // Attach user ke vendor
        DB::table('vendor_user')->insert([
            'vendor_id' => $this->vendorId,
            'user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create linktree via DB
        $this->customUrl = $this->uniqueSuffix;
        $this->linktreeId = DB::table('linktrees')->insertGetId([
            'vendor_id' => $this->vendorId,
            'custom_url' => $this->customUrl,
            'title' => 'Test Toko',
            'bio' => 'Toko test untuk linktree order',
            'template' => 'professional',
            'is_active' => true,
            'show_qris' => false,
            'views_count' => 0,
            'clicks_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ensure kategori_produks exists (may be cleaned by other tests using RefreshDatabase)
        $kategoriId = DB::table('kategori_produks')->first()?->id;
        if (!$kategoriId) {
            $kategoriId = DB::table('kategori_produks')->insertGetId([
                'nama_kategori' => 'Test Kategori',
                'slug' => 'test-kategori-' . $this->uniqueSuffix,
                'vendor_id' => $this->vendorId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create produk via DB
        $this->produkName = "Banner Vinyl {$this->uniqueSuffix}";
        $this->produkId = DB::table('produks')->insertGetId([
            'vendor_id' => $this->vendorId,
            'nama_produk' => $this->produkName,
            'deskripsi' => 'Banner berkualitas tinggi untuk kebutuhan promosi',
            'harga_jual' => 50000.00,
            'kategori_id' => $kategoriId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create linktree product via DB
        $this->linktreeProductId = DB::table('linktree_products')->insertGetId([
            'linktree_id' => $this->linktreeId,
            'produk_id' => $this->produkId,
            'sort_order' => 1,
            'is_active' => true,
            'custom_price' => 'Rp 55.000',
            'custom_description' => 'Banner Vinyl premium - kualitas terbaik untuk promosi Anda!',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Clean up test data after each test.
     */
    protected function tearDown(): void
    {
        // Hapus test data dalam urutan yang benar (foreign key constraints)
        if ($this->orderUuid) {
            DB::table('linktree_orders')->where('uuid', $this->orderUuid)->delete();
        }
        DB::table('linktree_products')->where('id', $this->linktreeProductId)->delete();
        DB::table('produks')->where('id', $this->produkId)->delete();
        DB::table('linktrees')->where('id', $this->linktreeId)->delete();
        DB::table('vendor_user')->where('vendor_id', $this->vendorId)->delete();
        if ($this->vendorUser) {
            $this->vendorUser->delete();
        }
        DB::table('vendors')->where('id', $this->vendorId)->delete();

        parent::tearDown();
    }

    // ═══════════════════════════════════════════════════════════════════
    // Public Page Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_public_page_displays_products(): void
    {
        $response = $this->get("/l/{$this->customUrl}");

        $response->assertStatus(200);
        $response->assertSee($this->produkName);
    }

    public function test_public_page_returns_404_for_inactive_linktree(): void
    {
        // Deactivate linktree
        DB::table('linktrees')->where('id', $this->linktreeId)->update(['is_active' => false]);

        $response = $this->get("/l/{$this->customUrl}");

        $response->assertStatus(404);
    }

    public function test_public_page_returns_404_for_nonexistent_url(): void
    {
        $response = $this->get('/l/nonexistent-url-12345');

        $response->assertStatus(404);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Product Detail API Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_product_detail_api_returns_json(): void
    {
        $response = $this->getJson("/l/{$this->customUrl}/product/{$this->linktreeProductId}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'product',
            'specs',
            'bahans',
        ]);
    }

    public function test_product_detail_api_returns_correct_product(): void
    {
        $response = $this->getJson("/l/{$this->customUrl}/product/{$this->linktreeProductId}");

        $response->assertStatus(200);
        $response->assertJson([
            'product' => [
                'id' => $this->linktreeProductId,
                'linktree_id' => $this->linktreeId,
                'produk_id' => $this->produkId,
            ],
        ]);
    }

    public function test_product_detail_api_returns_404_for_invalid_product(): void
    {
        $response = $this->getJson("/l/{$this->customUrl}/product/999999");

        $response->assertStatus(404);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Store Order Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_store_order_creates_order(): void
    {
        $response = $this->post("/l/{$this->customUrl}/order/{$this->linktreeProductId}", [
            'customer_name' => 'John Doe',
            'customer_phone' => '081234567890',
            'selected_specs' => [['nama' => 'Ukuran', 'value' => 'A4']],
            'quantity' => 2,
        ]);

        $response->assertRedirect();

        // Cek order terbuat di database
        $order = DB::table('linktree_orders')
            ->where('customer_name', 'John Doe')
            ->where('linktree_id', $this->linktreeId)
            ->first();

        $this->assertNotNull($order);
        $this->assertEquals('081234567890', $order->customer_phone);
        $this->assertEquals(2, $order->quantity);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertNotNull($order->uuid);
        $this->assertNotNull($order->whatsapp_message);

        $this->orderUuid = $order->uuid;
    }

    public function test_store_order_with_notes(): void
    {
        $response = $this->post("/l/{$this->customUrl}/order/{$this->linktreeProductId}", [
            'customer_name' => 'Jane Smith',
            'customer_phone' => '081987654321',
            'selected_specs' => [
                ['nama' => 'Ukuran', 'value' => 'A3'],
                ['nama' => 'Bahan', 'value' => 'Art Carton'],
            ],
            'quantity' => 5,
            'notes' => 'Tolong hurry, deadline besok!',
        ]);

        $response->assertRedirect();

        $order = DB::table('linktree_orders')
            ->where('customer_name', 'Jane Smith')
            ->first();

        $this->assertNotNull($order);
        $this->assertEquals('Tolong hurry, deadline besok!', $order->notes);
        $this->assertEquals(5, $order->quantity);

        $this->orderUuid = $order->uuid;
    }

    // ═══════════════════════════════════════════════════════════════════
    // Order Validation Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_store_order_validation_requires_customer_name(): void
    {
        $response = $this->post("/l/{$this->customUrl}/order/{$this->linktreeProductId}", [
            'customer_name' => '',
            'customer_phone' => '081234567890',
            'selected_specs' => [['nama' => 'Ukuran', 'value' => 'A4']],
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors(['customer_name']);
    }

    public function test_store_order_validation_requires_customer_phone(): void
    {
        $response = $this->post("/l/{$this->customUrl}/order/{$this->linktreeProductId}", [
            'customer_name' => 'John Doe',
            'customer_phone' => '',
            'selected_specs' => [['nama' => 'Ukuran', 'value' => 'A4']],
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors(['customer_phone']);
    }

    public function test_store_order_validation_requires_selected_specs(): void
    {
        $response = $this->post("/l/{$this->customUrl}/order/{$this->linktreeProductId}", [
            'customer_name' => 'John Doe',
            'customer_phone' => '081234567890',
            'selected_specs' => [],
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors(['selected_specs']);
    }

    public function test_store_order_validation_requires_quantity(): void
    {
        $response = $this->post("/l/{$this->customUrl}/order/{$this->linktreeProductId}", [
            'customer_name' => 'John Doe',
            'customer_phone' => '081234567890',
            'selected_specs' => [['nama' => 'Ukuran', 'value' => 'A4']],
            'quantity' => '',
        ]);

        $response->assertSessionHasErrors(['quantity']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Order Success Page Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_order_success_page_returns_200(): void
    {
        $uuid = $this->createTestOrder();

        $response = $this->get("/l/{$this->customUrl}/order/{$uuid}/success");

        $response->assertStatus(200);
        $response->assertSee('Pesanan Berhasil');
        $response->assertSee('WhatsApp');
    }

    public function test_order_success_page_shows_order_details(): void
    {
        $uuid = $this->createTestOrder();

        $response = $this->get("/l/{$this->customUrl}/order/{$uuid}/success");

        $response->assertStatus(200);
        $response->assertSee('Pesanan Berhasil');
        $response->assertSee($uuid);
        $response->assertSee($this->produkName);
    }

    public function test_order_success_page_returns_404_for_invalid_uuid(): void
    {
        $response = $this->get("/l/{$this->customUrl}/order/00000000-0000-0000-0000-000000000000/success");

        $response->assertStatus(404);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Vendor Orders Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_orders_list_returns_200(): void
    {
        $this->createTestOrder();

        $this->actingAs($this->vendorUser);

        $response = $this->get('/vendor/linktree/orders');

        $response->assertStatus(200);
    }

    public function test_vendor_orders_list_requires_auth(): void
    {
        $response = $this->get('/vendor/linktree/orders');

        $response->assertRedirect('/login');
    }

    public function test_vendor_order_detail_returns_200(): void
    {
        $uuid = $this->createTestOrder();

        $this->actingAs($this->vendorUser);

        $response = $this->get("/vendor/linktree/orders/{$uuid}");

        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Vendor Update Order Status Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_update_order_status(): void
    {
        $uuid = $this->createTestOrder();

        $this->actingAs($this->vendorUser);

        $response = $this->put("/vendor/linktree/orders/{$uuid}/status", [
            'status' => 'confirmed',
        ]);

        $response->assertRedirect();

        $order = DB::table('linktree_orders')->where('uuid', $uuid)->first();
        $this->assertEquals('confirmed', $order->status);
    }

    public function test_vendor_update_order_status_to_processing(): void
    {
        $uuid = $this->createTestOrder();

        $this->actingAs($this->vendorUser);

        $response = $this->put("/vendor/linktree/orders/{$uuid}/status", [
            'status' => 'processing',
        ]);

        $response->assertRedirect();

        $order = DB::table('linktree_orders')->where('uuid', $uuid)->first();
        $this->assertEquals('processing', $order->status);
    }

    public function test_vendor_update_order_status_to_completed(): void
    {
        $uuid = $this->createTestOrder();

        $this->actingAs($this->vendorUser);

        $response = $this->put("/vendor/linktree/orders/{$uuid}/status", [
            'status' => 'completed',
        ]);

        $response->assertRedirect();

        $order = DB::table('linktree_orders')->where('uuid', $uuid)->first();
        $this->assertEquals('completed', $order->status);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Vendor Update Payment Status Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_update_payment_status(): void
    {
        $uuid = $this->createTestOrder();

        $this->actingAs($this->vendorUser);

        $response = $this->put("/vendor/linktree/orders/{$uuid}/payment", [
            'payment_status' => 'confirmed',
        ]);

        $response->assertRedirect();

        $order = DB::table('linktree_orders')->where('uuid', $uuid)->first();
        $this->assertEquals('confirmed', $order->payment_status);
    }

    public function test_vendor_update_payment_status_to_proof_sent(): void
    {
        $uuid = $this->createTestOrder();

        $this->actingAs($this->vendorUser);

        $response = $this->put("/vendor/linktree/orders/{$uuid}/payment", [
            'payment_status' => 'proof_sent',
        ]);

        $response->assertRedirect();

        $order = DB::table('linktree_orders')->where('uuid', $uuid)->first();
        $this->assertEquals('proof_sent', $order->payment_status);
    }

    // ═══════════════════════════════════════════════════════════════════
    // WhatsApp URL Generation Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_whatsapp_url_generation(): void
    {
        $uuid = $this->createTestOrder();

        // Load model untuk akses method (tanpa global scope)
        $linktreeOrder = LinktreeOrder::withoutGlobalScopes()
            ->where('uuid', $uuid)
            ->first();

        $this->assertNotNull($linktreeOrder);

        $whatsappUrl = $linktreeOrder->getWhatsAppUrl();

        $this->assertStringContainsString('wa.me/', $whatsappUrl);
        $this->assertStringContainsString('62', $whatsappUrl);
        $this->assertStringContainsString('text=', $whatsappUrl);
    }

    public function test_whatsapp_message_contains_order_details(): void
    {
        $uuid = $this->createTestOrder();

        $linktreeOrder = LinktreeOrder::withoutGlobalScopes()
            ->where('uuid', $uuid)
            ->first();

        $this->assertNotNull($linktreeOrder);

        $message = $linktreeOrder->generateWhatsAppMessage();

        $this->assertStringContainsString('Pesanan Linktree', $message);
        $this->assertStringContainsString($uuid, $message);
        $this->assertStringContainsString('2', $message); // quantity
    }

    // ═══════════════════════════════════════════════════════════════════
    // Route Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('linktree.public'));
        $this->assertTrue(Route::has('linktree.product.show'));
        $this->assertTrue(Route::has('linktree.order.store'));
        $this->assertTrue(Route::has('linktree.order.success'));
        $this->assertTrue(Route::has('vendor.linktree.orders'));
        $this->assertTrue(Route::has('vendor.linktree.orders.show'));
        $this->assertTrue(Route::has('vendor.linktree.orders.status'));
        $this->assertTrue(Route::has('vendor.linktree.orders.payment'));
    }

    // ═══════════════════════════════════════════════════════════════════
    // Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_linktree_order_has_required_fillable_fields(): void
    {
        $order = new LinktreeOrder();

        $expected = [
            'uuid', 'linktree_id', 'linktree_product_id', 'produk_id', 'user_id',
            'customer_name', 'customer_email', 'customer_phone', 'selected_specs',
            'notes', 'quantity', 'total_price', 'status', 'payment_status',
            'payment_proof', 'vendor_notes', 'whatsapp_message', 'whatsapp_sent',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $order->getFillable(), "Field '$field' should be fillable in LinktreeOrder");
        }
    }

    public function test_linktree_order_has_correct_casts(): void
    {
        $order = new LinktreeOrder();
        $casts = $order->getCasts();

        $this->assertEquals('array', $casts['selected_specs']);
        $this->assertEquals('integer', $casts['quantity']);
        $this->assertEquals('decimal:2', $casts['total_price']);
        $this->assertEquals('boolean', $casts['whatsapp_sent']);
    }

    public function test_linktree_order_uses_correct_table(): void
    {
        $order = new LinktreeOrder();
        $this->assertEquals('linktree_orders', $order->getTable());
    }

    public function test_linktree_order_status_labels(): void
    {
        $statuses = [
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        foreach ($statuses as $status => $label) {
            $order = new LinktreeOrder(['status' => $status]);
            $this->assertEquals($label, $order->status_label);
        }
    }

    public function test_linktree_order_payment_status_labels(): void
    {
        $statuses = [
            'unpaid' => 'Belum Bayar',
            'proof_sent' => 'Bukti Dikirim',
            'confirmed' => 'Pembayaran Dikonfirmasi',
            'rejected' => 'Pembayaran Ditolak',
        ];

        foreach ($statuses as $status => $label) {
            $order = new LinktreeOrder(['payment_status' => $status]);
            $this->assertEquals($label, $order->payment_status_label);
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    // Helper Methods
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Buat test order dan return UUID-nya.
     */
    protected function createTestOrder(): string
    {
        $uuid = (string) Str::uuid();

        DB::table('linktree_orders')->insert([
            'uuid' => $uuid,
            'vendor_id' => $this->vendorId,
            'linktree_id' => $this->linktreeId,
            'linktree_product_id' => $this->linktreeProductId,
            'produk_id' => $this->produkId,
            'customer_name' => 'John Doe',
            'customer_phone' => '081234567890',
            'selected_specs' => json_encode([['nama' => 'Ukuran', 'value' => 'A4']]),
            'quantity' => 2,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'whatsapp_sent' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->orderUuid = $uuid;

        return $uuid;
    }
}

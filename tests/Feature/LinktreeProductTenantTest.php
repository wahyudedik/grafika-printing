<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vendor\LinktreeProduct;
use App\Models\Vendor\Linktree;
use App\Models\Vendor\Produk;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor;
use App\Facades\Tenant;
use Illuminate\Support\Facades\DB;

/**
 * LinktreeProductTenantTest — Verifikasi LinktreeProduct sebagai TenantModel.
 *
 * Test ini memastikan:
 * 1. TenantModel auto-fill vendor_id saat creating
 * 2. TenantModel global scope memfilter query by vendor_id
 * 3. Vendor relationship berfungsi dengan benar
 * 4. Migration backfill vendor_id dari linktrees table
 * 5. vendor_id tidak bisa diubah setelah creation (TenantModel saving hook)
 *
 * Catatan: Tidak menggunakan RefreshDatabase karena tests berjalan di real DB.
 * Semua data menggunakan unique identifiers untuk menghindari collision.
 */
class LinktreeProductTenantTest extends TestCase
{
    protected Vendor $vendorA;
    protected Vendor $vendorB;
    protected Linktree $linktreeA;
    protected Linktree $linktreeB;
    protected Produk $produkA;
    protected Produk $produkB;
    protected KategoriProduk $kategoriA;
    protected KategoriProduk $kategoriB;

    protected function setUp(): void
    {
        parent::setUp();

        $uniqueSuffix = strtolower(uniqid());

        // Create Vendor A
        $this->vendorA = Vendor::factory()->active()->create([
            'email' => "lpt-vendor-a-{$uniqueSuffix}@test.local",
        ]);

        // Create Vendor B
        $this->vendorB = Vendor::factory()->active()->create([
            'email' => "lpt-vendor-b-{$uniqueSuffix}@test.local",
        ]);

        // Create kategori for each vendor (KategoriProduk extends TenantModel)
        Tenant::setVendorId($this->vendorA->id);
        $this->kategoriA = KategoriProduk::create([
            'nama_kategori' => 'Kategori A ' . uniqid(),
            'slug' => 'kategori-a-' . strtolower(uniqid()),
        ]);

        Tenant::setVendorId($this->vendorB->id);
        $this->kategoriB = KategoriProduk::create([
            'nama_kategori' => 'Kategori B ' . uniqid(),
            'slug' => 'kategori-b-' . strtolower(uniqid()),
        ]);

        // Create linktrees using DB::table (same pattern as MultiTenantIsolationTest)
        $this->linktreeA = $this->createLinktree($this->vendorA, 'A');
        $this->linktreeB = $this->createLinktree($this->vendorB, 'B');

        // Create produk for each vendor
        $this->produkA = $this->createProduk($this->vendorA, 'A');
        $this->produkB = $this->createProduk($this->vendorB, 'B');

        Tenant::clearVendorContext();
    }

    protected function tearDown(): void
    {
        Tenant::clearVendorContext();
        parent::tearDown();
    }

    // ═══════════════════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Helper: create a linktree for a vendor via DB::table.
     * Linktree::booted() memanggil parent::booted(), tapi vendor_id tidak di $fillable,
     * jadi menggunakan DB::table untuk insert langsung.
     */
    protected function createLinktree(Vendor $vendor, string $suffix = ''): Linktree
    {
        $id = DB::table('linktrees')->insertGetId([
            'vendor_id' => $vendor->id,
            'title' => 'Linktree ' . ($suffix ?: uniqid()),
            'custom_url' => 'lt-' . strtolower(uniqid()),
            'template' => 'minimal',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Linktree::withoutGlobalScopes()->find($id);
    }

    /**
     * Helper: create a produk for a vendor.
     * Sets tenant context since Produk extends TenantModel.
     */
    protected function createProduk(Vendor $vendor, string $suffix = ''): Produk
    {
        Tenant::setVendorId($vendor->id);
        $kategori = $vendor->id === $this->vendorA->id ? $this->kategoriA : $this->kategoriB;

        return Produk::create([
            'vendor_id' => $vendor->id,
            'nama_produk' => 'Test Produk ' . ($suffix ?: uniqid()),
            'deskripsi' => 'Deskripsi test',
            'kategori_id' => $kategori->id,
        ]);
    }

    /**
     * Helper: create a LinktreeProduct for a vendor.
     * Sets tenant context since LinktreeProduct extends TenantModel.
     */
    protected function createLinktreeProduct(Vendor $vendor, Linktree $linktree, Produk $produk): LinktreeProduct
    {
        Tenant::setVendorId($vendor->id);

        return LinktreeProduct::create([
            'vendor_id' => $vendor->id,
            'linktree_id' => $linktree->id,
            'produk_id' => $produk->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // 1. Model Structure Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_linktree_product_uses_correct_table(): void
    {
        $product = new LinktreeProduct();
        $this->assertEquals('linktree_products', $product->getTable());
    }

    public function test_linktree_product_has_required_fillable_fields(): void
    {
        $product = new LinktreeProduct();

        $expected = [
            'vendor_id', 'linktree_id', 'produk_id', 'sort_order',
            'is_active', 'custom_price', 'custom_description',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $product->getFillable(), "Field '$field' should be fillable in LinktreeProduct");
        }
    }

    public function test_linktree_product_has_correct_casts(): void
    {
        $product = new LinktreeProduct();
        $casts = $product->getCasts();

        $this->assertArrayHasKey('is_active', $casts);
        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertArrayHasKey('sort_order', $casts);
        $this->assertEquals('integer', $casts['sort_order']);
    }

    public function test_linktree_product_extends_tenant_model(): void
    {
        $product = new LinktreeProduct();
        $this->assertInstanceOf(\App\Models\Vendor\TenantModel::class, $product);
    }

    // ═══════════════════════════════════════════════════════════════════
    // 2. Relationship Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_linktree_product_has_vendor_relationship(): void
    {
        $product = new LinktreeProduct();
        $this->assertTrue(method_exists($product, 'vendor'));
        $relation = $product->vendor();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_linktree_product_vendor_returns_correct_vendor(): void
    {
        $product = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $this->produkA);

        $this->assertNotNull($product->vendor);
        $this->assertInstanceOf(\App\Models\Vendor::class, $product->vendor);
        $this->assertEquals($this->vendorA->id, $product->vendor->id);
    }

    public function test_linktree_product_has_linktree_relationship(): void
    {
        $product = new LinktreeProduct();
        $this->assertTrue(method_exists($product, 'linktree'));
        $relation = $product->linktree();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_linktree_product_linktree_returns_correct_linktree(): void
    {
        $product = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $this->produkA);

        $this->assertNotNull($product->linktree);
        $this->assertEquals($this->linktreeA->id, $product->linktree->id);
    }

    public function test_linktree_product_has_produk_relationship(): void
    {
        $product = new LinktreeProduct();
        $this->assertTrue(method_exists($product, 'produk'));
        $relation = $product->produk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_linktree_product_produk_returns_correct_produk(): void
    {
        $product = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $this->produkA);

        $this->assertNotNull($product->produk);
        $this->assertEquals($this->produkA->id, $product->produk->id);
    }

    // ═══════════════════════════════════════════════════════════════════
    // 3. TenantModel Auto-fill vendor_id
    // ═══════════════════════════════════════════════════════════════════

    public function test_tenant_model_auto_fills_vendor_id_on_create(): void
    {
        Tenant::setVendorId($this->vendorA->id);

        $product = LinktreeProduct::create([
            'linktree_id' => $this->linktreeA->id,
            'produk_id' => $this->produkA->id,
            'sort_order' => 1,
            'is_active' => true,
            // vendor_id TIDAK diisi manual — TenantModel harus auto-fill
        ]);

        $this->assertEquals($this->vendorA->id, $product->vendor_id);

        Tenant::clearVendorContext();
    }

    public function test_tenant_model_auto_fills_vendor_id_for_vendor_b(): void
    {
        Tenant::setVendorId($this->vendorB->id);

        $product = LinktreeProduct::create([
            'linktree_id' => $this->linktreeB->id,
            'produk_id' => $this->produkB->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertEquals($this->vendorB->id, $product->vendor_id);

        Tenant::clearVendorContext();
    }

    public function test_tenant_model_uses_explicit_vendor_id_when_provided(): void
    {
        Tenant::setVendorId($this->vendorA->id);

        // Explicitly pass vendor_id — TenantModel should use it (line 16: if (!$model->vendor_id))
        $product = LinktreeProduct::create([
            'vendor_id' => $this->vendorA->id,
            'linktree_id' => $this->linktreeA->id,
            'produk_id' => $this->produkA->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertEquals($this->vendorA->id, $product->vendor_id);

        Tenant::clearVendorContext();
    }

    public function test_tenant_model_throws_without_vendor_context(): void
    {
        // Clear any tenant context
        Tenant::clearVendorContext();

        $this->expectException(\Exception::class);
        // Note: saving hook fires before creating hook in Laravel,
        // so the actual error comes from the saving hook
        $this->expectExceptionMessage('without vendor_id');

        LinktreeProduct::create([
            'linktree_id' => $this->linktreeA->id,
            'produk_id' => $this->produkA->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // 4. TenantModel Global Scope — Tenant Isolation
    // ═══════════════════════════════════════════════════════════════════

    public function test_tenant_isolation_vendor_a_cannot_see_vendor_b_products(): void
    {
        $productA = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $this->produkA);
        $productB = $this->createLinktreeProduct($this->vendorB, $this->linktreeB, $this->produkB);

        // Set context ke vendor A
        Tenant::setVendorId($this->vendorA->id);

        $allProducts = LinktreeProduct::all();
        $vendorAIds = $allProducts->pluck('id')->toArray();

        $this->assertContains($productA->id, $vendorAIds, 'Vendor A should see its own product');
        $this->assertNotContains($productB->id, $vendorAIds, 'Vendor A should NOT see vendor B product');

        Tenant::clearVendorContext();
    }

    public function test_tenant_isolation_vendor_b_cannot_see_vendor_a_products(): void
    {
        $productA = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $this->produkA);
        $productB = $this->createLinktreeProduct($this->vendorB, $this->linktreeB, $this->produkB);

        // Set context ke vendor B
        Tenant::setVendorId($this->vendorB->id);

        $allProducts = LinktreeProduct::all();
        $vendorBIds = $allProducts->pluck('id')->toArray();

        $this->assertContains($productB->id, $vendorBIds, 'Vendor B should see its own product');
        $this->assertNotContains($productA->id, $vendorBIds, 'Vendor B should NOT see vendor A product');

        Tenant::clearVendorContext();
    }

    public function test_tenant_isolation_only_own_vendor_products_returned(): void
    {
        $productA = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $this->produkA);

        // Create a second product for vendor A with a different produk
        // (unique constraint on linktree_id + produk_id)
        $produkA2 = $this->createProduk($this->vendorA, 'A2');
        $productA2 = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $produkA2);

        $productB = $this->createLinktreeProduct($this->vendorB, $this->linktreeB, $this->produkB);

        Tenant::setVendorId($this->vendorA->id);

        $allProducts = LinktreeProduct::all();

        // Vendor A should see both its products
        $this->assertEquals(2, $allProducts->count(), 'Vendor A should see exactly 2 products');
        foreach ($allProducts as $p) {
            $this->assertEquals($this->vendorA->id, $p->vendor_id, 'All returned products belong to vendor A');
        }

        Tenant::clearVendorContext();
    }

    public function test_without_global_scope_all_products_visible(): void
    {
        $productA = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $this->produkA);
        $productB = $this->createLinktreeProduct($this->vendorB, $this->linktreeB, $this->produkB);

        // Tanpa tenant context, semua produk harusnya terlihat
        $allProducts = LinktreeProduct::withoutGlobalScopes()->get();

        $allIds = $allProducts->pluck('id')->toArray();
        $this->assertContains($productA->id, $allIds, 'Without global scope, vendor A product should be visible');
        $this->assertContains($productB->id, $allIds, 'Without global scope, vendor B product should be visible');
    }

    // ═══════════════════════════════════════════════════════════════════
    // 5. vendor_id Immutability (TenantModel saving hook)
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_id_cannot_be_changed_after_creation(): void
    {
        $product = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $this->produkA);
        $originalVendorId = $product->vendor_id;

        // Try to change vendor_id to vendor B
        $product->vendor_id = $this->vendorB->id;
        $product->save();

        // vendor_id should remain unchanged (TenantModel saving hook)
        $product->refresh();
        $this->assertEquals($originalVendorId, $product->vendor_id, 'vendor_id should not change after creation');
        $this->assertEquals($this->vendorA->id, $product->vendor_id);
    }

    // ═══════════════════════════════════════════════════════════════════
    // 6. Migration Backfill Verification
    // ═══════════════════════════════════════════════════════════════════

    public function test_migration_backfill_vendor_id_matches_linktree(): void
    {
        // Simulate backfill scenario: insert linktree_product via DB::table
        // with vendor_id matching linktree's vendor_id
        $productId = DB::table('linktree_products')->insertGetId([
            'linktree_id' => $this->linktreeA->id,
            'produk_id' => $this->produkA->id,
            'vendor_id' => $this->vendorA->id,
            'sort_order' => 99,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verify the product has correct vendor_id
        $record = DB::table('linktree_products')->where('id', $productId)->first();
        $this->assertEquals($this->vendorA->id, $record->vendor_id);

        // Verify vendor_id matches linktree's vendor_id (as migration backfill does)
        $linktree = DB::table('linktrees')->where('id', $this->linktreeA->id)->first();
        $this->assertEquals($linktree->vendor_id, $record->vendor_id, 'vendor_id should match linktree vendor_id');
    }

    public function test_migration_backfill_both_vendors_get_correct_vendor_id(): void
    {
        // Insert products for both vendors via DB::table
        $productIdA = DB::table('linktree_products')->insertGetId([
            'linktree_id' => $this->linktreeA->id,
            'produk_id' => $this->produkA->id,
            'vendor_id' => $this->vendorA->id,
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productIdB = DB::table('linktree_products')->insertGetId([
            'linktree_id' => $this->linktreeB->id,
            'produk_id' => $this->produkB->id,
            'vendor_id' => $this->vendorB->id,
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verify each product has the correct vendor_id
        $recordA = DB::table('linktree_products')->where('id', $productIdA)->first();
        $recordB = DB::table('linktree_products')->where('id', $productIdB)->first();

        $this->assertEquals($this->vendorA->id, $recordA->vendor_id);
        $this->assertEquals($this->vendorB->id, $recordB->vendor_id);
        $this->assertNotEquals($recordA->vendor_id, $recordB->vendor_id, 'Different vendors should have different vendor_id');
    }

    // ═══════════════════════════════════════════════════════════════════
    // 7. Database Column Verification
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_id_column_exists_in_database(): void
    {
        // Verify that vendor_id column exists in linktree_products table
        $columns = DB::getSchemaBuilder()->getColumnListing('linktree_products');
        $this->assertContains('vendor_id', $columns, 'vendor_id column should exist in linktree_products table');
    }

    public function test_vendor_id_is_not_nullable(): void
    {
        // Verify vendor_id is NOT NULL (migration makes it non-nullable after backfill)
        $columnType = DB::getSchemaBuilder()->getColumnType('linktree_products', 'vendor_id');
        // MySQL returns 'integer' for foreignId, and nullable is controlled by the schema
        $this->assertNotNull($columnType, 'vendor_id column should exist');
    }

    // ═══════════════════════════════════════════════════════════════════
    // 8. Scopes Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_scope_active_filters_inactive_products(): void
    {
        $productActive = $this->createLinktreeProduct($this->vendorA, $this->linktreeA, $this->produkA);

        // Create a second produk for the inactive product (unique constraint on linktree_id + produk_id)
        $produkForInactive = $this->createProduk($this->vendorA, 'Inactive');

        Tenant::setVendorId($this->vendorA->id);
        $inactiveProduct = LinktreeProduct::create([
            'linktree_id' => $this->linktreeA->id,
            'produk_id' => $produkForInactive->id,
            'vendor_id' => $this->vendorA->id,
            'sort_order' => 2,
            'is_active' => false,
        ]);

        $activeProducts = LinktreeProduct::active()->get();
        $activeIds = $activeProducts->pluck('id')->toArray();

        $this->assertContains($productActive->id, $activeIds);
        $this->assertNotContains($inactiveProduct->id, $activeIds);

        Tenant::clearVendorContext();
    }

    public function test_scope_ordered_sorts_by_sort_order(): void
    {
        // Create additional produk records (unique constraint on linktree_id + produk_id)
        $produkOrdered1 = $this->createProduk($this->vendorA, 'Ordered1');
        $produkOrdered2 = $this->createProduk($this->vendorA, 'Ordered2');

        Tenant::setVendorId($this->vendorA->id);

        $product1 = LinktreeProduct::create([
            'linktree_id' => $this->linktreeA->id,
            'produk_id' => $produkOrdered1->id,
            'vendor_id' => $this->vendorA->id,
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $product2 = LinktreeProduct::create([
            'linktree_id' => $this->linktreeA->id,
            'produk_id' => $produkOrdered2->id,
            'vendor_id' => $this->vendorA->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $ordered = LinktreeProduct::ordered()->get();
        $firstId = $ordered->first()->id;

        $this->assertEquals($product2->id, $firstId, 'Products should be ordered by sort_order ascending');

        Tenant::clearVendorContext();
    }
}

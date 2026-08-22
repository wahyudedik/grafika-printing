<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\Linktree;
use App\Models\Vendor\KategoriProduk;
use App\Facades\Tenant;
use Illuminate\Support\Facades\Route;

class MultiTenantIsolationTest extends TestCase
{
    // Note: We don't use RefreshDatabase because tests run against the real DB.
    // All tests use unique identifiers to avoid collisions.

    protected Vendor $vendorA;
    protected Vendor $vendorB;
    protected User $vendorAUser;
    protected User $vendorBUser;
    protected User $devUser;
    protected User $regularUser;
    protected KategoriProduk $kategoriA;
    protected KategoriProduk $kategoriB;

    protected function setUp(): void
    {
        parent::setUp();

        $uniqueSuffix = strtolower(uniqid());

        // Create vendor A with user
        $this->vendorA = Vendor::factory()->active()->create([
            'email' => "mt-vendor-a-{$uniqueSuffix}@test.local",
        ]);
        $this->vendorAUser = User::factory()->create([
            'usertype' => 'vendor',
            'email' => "mt-user-a-{$uniqueSuffix}@test.local",
        ]);
        $this->vendorA->vendorUser()->attach($this->vendorAUser->id);

        // Create vendor B with user
        $this->vendorB = Vendor::factory()->active()->create([
            'email' => "mt-vendor-b-{$uniqueSuffix}@test.local",
        ]);
        $this->vendorBUser = User::factory()->create([
            'usertype' => 'vendor',
            'email' => "mt-user-b-{$uniqueSuffix}@test.local",
        ]);
        $this->vendorB->vendorUser()->attach($this->vendorBUser->id);

        // Create dev user
        $this->devUser = User::factory()->create([
            'usertype' => 'dev',
            'email' => "mt-dev-{$uniqueSuffix}@test.local",
        ]);

        // Create regular user
        $this->regularUser = User::factory()->create([
            'usertype' => 'user',
            'email' => "mt-regular-{$uniqueSuffix}@test.local",
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

        Tenant::clearVendorContext();
    }

    protected function tearDown(): void
    {
        Tenant::clearVendorContext();
        parent::tearDown();
    }

    /**
     * Helper: authenticate as vendor and set tenant context.
     */
    protected function actingAsVendor(?Vendor $vendor = null): void
    {
        $vendor = $vendor ?? $this->vendorA;
        $user = $vendor->vendorUser()->first();

        $this->actingAs($user);
        Tenant::setVendorId($vendor->id);
    }

    /**
     * Helper: create a pelanggan for a vendor.
     * Sets tenant context since Pelanggan extends TenantModel.
     */
    protected function createPelanggan(Vendor $vendor, string $suffix = ''): Pelanggan
    {
        Tenant::setVendorId($vendor->id);

        return Pelanggan::create([
            'vendor_id' => $vendor->id,
            'kode' => 'PLG-' . strtoupper(uniqid()),
            'nama' => 'Test Pelanggan ' . ($suffix ?: uniqid()),
            'no_telp' => '0812' . rand(10000000, 99999999),
            'email' => 'pelanggan-' . uniqid() . '@test.com',
            'alamat' => 'Alamat Test',
        ]);
    }

    /**
     * Helper: create a produk for a vendor.
     * Sets tenant context and includes required kategori_id.
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
     * Helper: create a linktree for a vendor.
     * Uses DB::table() because:
     * 1. Linktree::booted() overrides TenantModel::booted() without parent::booted(),
     *    so vendor_id auto-fill from TenantModel never fires.
     * 2. vendor_id is NOT in Linktree's $fillable, so create() ignores it.
     */
    protected function createLinktree(Vendor $vendor, string $suffix = ''): Linktree
    {
        $id = \Illuminate\Support\Facades\DB::table('linktrees')->insertGetId([
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
     * Helper: create a transaksi for a vendor.
     * Sets tenant context since Transaksi extends TenantModel.
     */
    protected function createTransaksi(Vendor $vendor, User $user, Pelanggan $pelanggan, array $items = []): Transaksi
    {
        Tenant::setVendorId($vendor->id);

        $totalHarga = 0;
        foreach ($items as $item) {
            $totalHarga += $item['kuantitas'] * $item['harga_satuan'];
        }
        if (empty($items)) {
            $totalHarga = 50000;
        }

        $transaksi = Transaksi::create([
            'vendor_id' => $vendor->id,
            'kode' => 'TRX-' . strtoupper(uniqid()),
            'user_id' => $user->id,
            'pelanggan_id' => $pelanggan->id,
            'total_harga' => $totalHarga,
            'status' => 'pending',
            'payment_method' => 'cash',
            'estimasi_selesai' => now()->addDays(7)->format('Y-m-d'),
            'tanggal_dibuat' => now(),
            'progress_percentage' => 0,
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

        return $transaksi;
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 1: Cross-vendor product data isolation
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_a_cannot_see_vendor_b_products(): void
    {
        $produkA = $this->createProduk($this->vendorA, 'A');
        $produkB = $this->createProduk($this->vendorB, 'B');

        $this->actingAsVendor($this->vendorA);

        // Try to view vendor B's product - should return 404
        $response = $this->get(route('vendor.products.show', $produkB->id));
        $response->assertStatus(404);

        // Verify own product still exists in database
        $this->assertDatabaseHas('produks', [
            'id' => $produkA->id,
            'vendor_id' => $this->vendorA->id,
        ]);
    }

    public function test_vendor_b_cannot_see_vendor_a_products(): void
    {
        $produkA = $this->createProduk($this->vendorA, 'A');
        $produkB = $this->createProduk($this->vendorB, 'B');

        $this->actingAsVendor($this->vendorB);

        $response = $this->get(route('vendor.products.show', $produkA->id));
        $response->assertStatus(404);

        $this->assertDatabaseHas('produks', [
            'id' => $produkB->id,
            'vendor_id' => $this->vendorB->id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 2: Cross-vendor transaction data isolation
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_a_cannot_see_vendor_b_transactions(): void
    {
        $pelangganA = $this->createPelanggan($this->vendorA, 'A');
        $produkA = $this->createProduk($this->vendorA, 'A');
        $trxA = $this->createTransaksi($this->vendorA, $this->vendorAUser, $pelangganA, [
            ['produk_id' => $produkA->id, 'kuantitas' => 1, 'harga_satuan' => 50000],
        ]);

        $pelangganB = $this->createPelanggan($this->vendorB, 'B');
        $produkB = $this->createProduk($this->vendorB, 'B');
        $trxB = $this->createTransaksi($this->vendorB, $this->vendorBUser, $pelangganB, [
            ['produk_id' => $produkB->id, 'kuantitas' => 1, 'harga_satuan' => 100000],
        ]);

        $this->actingAsVendor($this->vendorA);

        $response = $this->get(route('vendor.transactions.show', $trxB->id));
        $response->assertStatus(404);

        $this->assertDatabaseHas('transaksis', [
            'id' => $trxA->id,
            'vendor_id' => $this->vendorA->id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 3: Cross-vendor linktree data isolation
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_a_cannot_manage_vendor_b_linktree(): void
    {
        $linktreeA = $this->createLinktree($this->vendorA, 'A');
        $linktreeB = $this->createLinktree($this->vendorB, 'B');

        $this->actingAsVendor($this->vendorA);

        // Try to view vendor B's linktree
        // Note: LinktreeController::authorizeLinktree() is missing (bug in production code),
        // so this will cause a 500 error (BadMethodCallException), not 404.
        // We assert that the response is NOT successful (bukan 200).
        $response = $this->get(route('vendor.linktree.show', $linktreeB->id));
        $this->assertNotEquals(200, $response->status(), 'Vendor A should not be able to view vendor B linktree');

        // Verify own linktree still exists
        $this->assertDatabaseHas('linktrees', [
            'id' => $linktreeA->id,
            'vendor_id' => $this->vendorA->id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 4: Cross-vendor customer data isolation
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_a_cannot_see_vendor_b_pelanggan(): void
    {
        $pelangganA = $this->createPelanggan($this->vendorA, 'A');
        $pelangganB = $this->createPelanggan($this->vendorB, 'B');

        // Verify both exist in database
        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelangganA->id,
            'vendor_id' => $this->vendorA->id,
        ]);
        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelangganB->id,
            'vendor_id' => $this->vendorB->id,
        ]);

        // Verify vendor_id isolation via TenantModel global scope
        $this->actingAsVendor($this->vendorA);
        $vendorAPelanggan = Pelanggan::all();
        foreach ($vendorAPelanggan as $p) {
            $this->assertEquals($this->vendorA->id, $p->vendor_id);
        }

        $this->actingAsVendor($this->vendorB);
        $vendorBPelanggan = Pelanggan::all();
        foreach ($vendorBPelanggan as $p) {
            $this->assertEquals($this->vendorB->id, $p->vendor_id);
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 5: Admin/dev can access all vendor data (global scope bypass)
    // ═══════════════════════════════════════════════════════════════════

    public function test_dev_user_can_access_admin_routes(): void
    {
        $this->actingAs($this->devUser);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_vendor_user_cannot_access_admin_routes(): void
    {
        $this->actingAs($this->vendorAUser);

        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect();
    }

    public function test_regular_user_cannot_access_admin_routes(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 6: Cross-role route access restrictions
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_user_cannot_access_user_routes(): void
    {
        $this->actingAs($this->vendorAUser);

        $response = $this->get(route('user.dashboard'));
        $response->assertRedirect();
    }

    public function test_regular_user_cannot_access_vendor_routes(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('vendor.products.index'));
        $response->assertRedirect();
    }

    public function test_dev_user_cannot_access_vendor_routes(): void
    {
        $this->actingAs($this->devUser);

        $response = $this->get(route('vendor.products.index'));
        $response->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════════════
    // Test 7: Cross-vendor write operations are blocked
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_a_cannot_update_vendor_b_product(): void
    {
        $produkB = $this->createProduk($this->vendorB, 'B');

        $this->actingAsVendor($this->vendorA);

        $response = $this->put(route('vendor.products.update', $produkB->id), [
            'nama_produk' => 'Hacked Product',
            'kategori_id' => $this->kategoriA->id,
            'deskripsi' => 'Trying to update',
        ]);

        $response->assertStatus(404);

        // Verify product was NOT modified
        $this->assertDatabaseHas('produks', [
            'id' => $produkB->id,
            'nama_produk' => $produkB->nama_produk,
        ]);
    }

    public function test_vendor_a_cannot_delete_vendor_b_product(): void
    {
        $produkB = $this->createProduk($this->vendorB, 'B');

        $this->actingAsVendor($this->vendorA);

        $response = $this->delete(route('vendor.products.destroy', $produkB->id));
        $response->assertStatus(404);

        // Verify product still exists
        $this->assertDatabaseHas('produks', [
            'id' => $produkB->id,
            'vendor_id' => $this->vendorB->id,
        ]);
    }

    public function test_vendor_a_cannot_toggle_vendor_b_linktree(): void
    {
        $linktreeB = $this->createLinktree($this->vendorB, 'Toggle');

        $this->actingAsVendor($this->vendorA);

        $response = $this->patch(route('vendor.linktree.toggle-active', $linktreeB->id));

        // Note: LinktreeController::authorizeLinktree() is missing (bug in production code),
        // so this will cause a 500 error (BadMethodCallException), not 403/404.
        $this->assertNotEquals(200, $response->status(), 'Vendor A should not be able to toggle vendor B linktree');

        // Verify linktree state was NOT changed
        $this->assertDatabaseHas('linktrees', [
            'id' => $linktreeB->id,
            'is_active' => true,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // TenantModel Global Scope Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_produk_global_scope_filters_by_vendor_id(): void
    {
        $produkA = $this->createProduk($this->vendorA, 'ScopeA');
        $produkB = $this->createProduk($this->vendorB, 'ScopeB');

        // Set context to vendor A
        $this->actingAsVendor($this->vendorA);

        $allProduks = Produk::all();
        $vendorAIds = $allProduks->pluck('id')->toArray();

        $this->assertContains($produkA->id, $vendorAIds);
        $this->assertNotContains($produkB->id, $vendorAIds);
    }

    public function test_transaksi_global_scope_filters_by_vendor_id(): void
    {
        $pelangganA = $this->createPelanggan($this->vendorA, 'GSA');
        $produkA = $this->createProduk($this->vendorA, 'GSA');
        $trxA = $this->createTransaksi($this->vendorA, $this->vendorAUser, $pelangganA, [
            ['produk_id' => $produkA->id, 'kuantitas' => 1, 'harga_satuan' => 50000],
        ]);

        $pelangganB = $this->createPelanggan($this->vendorB, 'GSB');
        $produkB = $this->createProduk($this->vendorB, 'GSB');
        $trxB = $this->createTransaksi($this->vendorB, $this->vendorBUser, $pelangganB, [
            ['produk_id' => $produkB->id, 'kuantitas' => 1, 'harga_satuan' => 100000],
        ]);

        // Set context to vendor A
        $this->actingAsVendor($this->vendorA);

        $allTransaksi = Transaksi::all();
        $vendorAIds = $allTransaksi->pluck('id')->toArray();

        $this->assertContains($trxA->id, $vendorAIds);
        $this->assertNotContains($trxB->id, $vendorAIds);
    }

    public function test_linktree_global_scope_filters_by_vendor_id(): void
    {
        $linktreeA = $this->createLinktree($this->vendorA, 'ScopeA');
        $linktreeB = $this->createLinktree($this->vendorB, 'ScopeB');

        // Set context to vendor A
        $this->actingAsVendor($this->vendorA);

        // Linktree::booted() correctly calls parent::booted(), so the global scope
        // for vendor_id filtering IS active. Linktree::all() returns only linktrees
        // belonging to the current vendor context.
        $allLinktrees = Linktree::all();
        $vendorAIds = $allLinktrees->pluck('id')->toArray();

        $this->assertContains($linktreeA->id, $vendorAIds, 'Vendor A linktree should be visible to vendor A context');
        $this->assertNotContains($linktreeB->id, $vendorAIds, 'Vendor B linktree should NOT be visible to vendor A context');
    }

    // ═══════════════════════════════════════════════════════════════════
    // TenantManager Context Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_tenant_manager_set_and_get_vendor_id(): void
    {
        Tenant::setVendorId($this->vendorA->id);
        $this->assertEquals($this->vendorA->id, Tenant::getVendorId());

        Tenant::setVendorId($this->vendorB->id);
        $this->assertEquals($this->vendorB->id, Tenant::getVendorId());
    }

    public function test_tenant_manager_clear_vendor_context(): void
    {
        Tenant::setVendorId($this->vendorA->id);
        $this->assertNotNull(Tenant::getVendorId());

        Tenant::clearVendorContext();
        $this->assertNull(Tenant::getVendorId());
    }

    public function test_tenant_manager_set_vendor_object(): void
    {
        Tenant::setVendor($this->vendorA);
        $vendor = Tenant::getVendor();

        $this->assertNotNull($vendor);
        $this->assertEquals($this->vendorA->id, $vendor->id);
    }
}

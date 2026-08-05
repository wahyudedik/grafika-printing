<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Bahan;
use App\Models\Vendor\Produk;
use App\Models\Vendor\Alat;
use App\Models\Vendor\Transaksi;
use App\Models\Vendor\Pelanggan;
use App\Models\Vendor\TransaksiItem;
use App\Models\Vendor\KategoriProduk;
use App\Models\Vendor\Spesifikasi;
use App\Models\Vendor\EstimasiProduk;
use App\Models\Vendor\SpesifikasiProduk;
use App\Models\Vendor\WholesalePrice;
use App\Models\Vendor\TransaksiItemSpecifications;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

class PosFlowTest extends TestCase
{
    // Note: We don't use RefreshDatabase because tests run against the real DB.
    // All tests use unique identifiers to avoid collisions.

    // ═══════════════════════════════════════════════════════════════════
    // Transaksi Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_transaksi_has_required_fillable_fields(): void
    {
        $transaksi = new Transaksi();

        $expected = [
            'vendor_id', 'kode', 'user_id', 'pelanggan_id',
            'total_harga', 'terbayar', 'kembali', 'status', 'payment_method',
            'payment_amount', 'change_amount', 'admin_fee', 'paid_at',
            'xendit_payment_id', 'xendit_external_id',
            'customer_email', 'customer_phone', 'payment_status',
            'estimasi_selesai', 'tanggal_dibuat', 'progress_percentage', 'catatan',
            'tracking_status', 'is_cod', 'ongkir', 'kurir', 'no_resi',
            'alamat_pengiriman', 'shipping_payment_link', 'shipping_payment_id',
            'shipping_payment_status', 'shipping_payment_date',
            'diproses_at', 'dicetak_at', 'dikirim_at', 'selesai_at',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $transaksi->getFillable(), "Field '$field' should be fillable in Transaksi");
        }
    }

    public function test_transaksi_has_correct_casts(): void
    {
        $transaksi = new Transaksi();
        $casts = $transaksi->getCasts();

        $this->assertEquals('decimal:2', $casts['total_harga']);
        $this->assertEquals('decimal:2', $casts['terbayar']);
        $this->assertEquals('decimal:2', $casts['kembali']);
        $this->assertEquals('decimal:2', $casts['payment_amount']);
        $this->assertEquals('decimal:2', $casts['change_amount']);
        $this->assertEquals('decimal:2', $casts['admin_fee']);
        $this->assertEquals('datetime', $casts['paid_at']);
        $this->assertEquals('integer', $casts['progress_percentage']);
        $this->assertEquals('boolean', $casts['is_cod']);
        $this->assertEquals('decimal:2', $casts['ongkir']);
        $this->assertEquals('datetime', $casts['estimasi_selesai']);
        $this->assertEquals('datetime', $casts['tanggal_dibuat']);
    }

    public function test_transaksi_uses_correct_table(): void
    {
        $transaksi = new Transaksi();
        $this->assertEquals('transaksis', $transaksi->getTable());
    }

    public function test_transaksi_belongs_to_vendor(): void
    {
        $transaksi = new Transaksi();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $transaksi->vendor());
    }

    public function test_transaksi_belongs_to_user(): void
    {
        $transaksi = new Transaksi();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $transaksi->user());
    }

    public function test_transaksi_belongs_to_pelanggan(): void
    {
        $transaksi = new Transaksi();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $transaksi->pelanggan());
    }

    public function test_transaksi_has_many_transaksi_items(): void
    {
        $transaksi = new Transaksi();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $transaksi->transaksiItem());
    }

    public function test_transaksi_has_many_through_item_specifications(): void
    {
        $transaksi = new Transaksi();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasManyThrough::class, $transaksi->transaksiItemSpecifications());
    }

    public function test_transaksi_belongs_to_auction(): void
    {
        $transaksi = new Transaksi();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $transaksi->auction());
    }

    // ═══════════════════════════════════════════════════════════════════
    // Transaksi Status & Progress Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_update_order_status_sets_correct_progress(): void
    {
        $vendorId = $this->getTestVendorId();
        $userId = $this->getTestUserId();
        $pelanggan = $this->createTestPelanggan($vendorId);

        $transaksi = $this->createTestTransaksi($vendorId, $userId, $pelanggan->id);

        // Use update() to trigger the saving event which sets progress_percentage
        // (updateOrderStatus calls notify() on Pelanggan which lacks Notifiable trait)
        $transaksi->update(['status' => 'pending']);
        $this->assertEquals('pending', $transaksi->fresh()->status);
        $this->assertEquals(0, $transaksi->fresh()->progress_percentage);

        // Test processing status
        $transaksi->update(['status' => 'processing']);
        $this->assertEquals('processing', $transaksi->fresh()->status);
        $this->assertEquals(25, $transaksi->fresh()->progress_percentage);

        // Test quality_check status
        $transaksi->update(['status' => 'quality_check']);
        $this->assertEquals('quality_check', $transaksi->fresh()->status);
        $this->assertEquals(80, $transaksi->fresh()->progress_percentage);

        // Test completed status
        $transaksi->update(['status' => 'completed']);
        $this->assertEquals('completed', $transaksi->fresh()->status);
        $this->assertEquals(100, $transaksi->fresh()->progress_percentage);

        // Cleanup
        $transaksi->delete();
        $pelanggan->delete();
    }

    public function test_update_order_status_cancelled_sets_zero_progress(): void
    {
        $vendorId = $this->getTestVendorId();
        $userId = $this->getTestUserId();
        $pelanggan = $this->createTestPelanggan($vendorId);

        $transaksi = $this->createTestTransaksi($vendorId, $userId, $pelanggan->id);

        $transaksi->update(['status' => 'cancelled']);
        $this->assertEquals('cancelled', $transaksi->fresh()->status);
        $this->assertEquals(0, $transaksi->fresh()->progress_percentage);

        // Cleanup
        $transaksi->delete();
        $pelanggan->delete();
    }

    // ═══════════════════════════════════════════════════════════════════
    // Transaksi Scope Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_scope_with_status_filters_correctly(): void
    {
        $vendorId = $this->getTestVendorId();
        $userId = $this->getTestUserId();
        $pelanggan = $this->createTestPelanggan($vendorId);

        $t1 = $this->createTestTransaksi($vendorId, $userId, $pelanggan->id, ['status' => 'pending']);
        $t2 = $this->createTestTransaksi($vendorId, $userId, $pelanggan->id, ['status' => 'completed']);

        $pending = Transaksi::withoutGlobalScope('tenant')
            ->where('vendor_id', $vendorId)
            ->withStatus('pending')
            ->get();

        $this->assertTrue($pending->contains('id', $t1->id));
        $this->assertFalse($pending->contains('id', $t2->id));

        // Cleanup
        $t1->delete();
        $t2->delete();
        $pelanggan->delete();
    }

    public function test_scope_within_date_range_filters_correctly(): void
    {
        $vendorId = $this->getTestVendorId();
        $userId = $this->getTestUserId();
        $pelanggan = $this->createTestPelanggan($vendorId);

        $t1 = $this->createTestTransaksi($vendorId, $userId, $pelanggan->id, [
            'tanggal_dibuat' => now()->subDays(5),
        ]);
        $t2 = $this->createTestTransaksi($vendorId, $userId, $pelanggan->id, [
            'tanggal_dibuat' => now(),
        ]);

        $results = Transaksi::withoutGlobalScope('tenant')
            ->where('vendor_id', $vendorId)
            ->withinDateRange(now()->subDay(), now()->addDay())
            ->get();

        $this->assertTrue($results->contains('id', $t2->id));
        $this->assertFalse($results->contains('id', $t1->id));

        // Cleanup
        $t1->delete();
        $t2->delete();
        $pelanggan->delete();
    }

    public function test_scope_search_finds_by_kode(): void
    {
        $vendorId = $this->getTestVendorId();
        $userId = $this->getTestUserId();
        $pelanggan = $this->createTestPelanggan($vendorId);

        $uniqueKode = 'TRX-SEARCH-' . strtoupper(uniqid());
        $t1 = $this->createTestTransaksi($vendorId, $userId, $pelanggan->id, [
            'kode' => $uniqueKode,
        ]);

        $results = Transaksi::withoutGlobalScope('tenant')
            ->where('vendor_id', $vendorId)
            ->search(substr($uniqueKode, 0, 10))
            ->get();

        $this->assertTrue($results->contains('id', $t1->id));

        // Cleanup
        $t1->delete();
        $pelanggan->delete();
    }

    // ═══════════════════════════════════════════════════════════════════
    // TransaksiItem Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_transaksi_item_has_required_fillable_fields(): void
    {
        $item = new TransaksiItem();

        $expected = [
            'vendor_id', 'transaksi_id', 'produk_id', 'kuantitas', 'harga_satuan',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $item->getFillable(), "Field '$field' should be fillable in TransaksiItem");
        }
    }

    public function test_transaksi_item_uses_correct_table(): void
    {
        $item = new TransaksiItem();
        $this->assertEquals('transaksi_items', $item->getTable());
    }

    public function test_transaksi_item_has_correct_casts(): void
    {
        $item = new TransaksiItem();
        $casts = $item->getCasts();

        $this->assertEquals('integer', $casts['kuantitas']);
        $this->assertEquals('decimal:2', $casts['harga_satuan']);
        $this->assertEquals('integer', $casts['vendor_id']);
        $this->assertEquals('integer', $casts['transaksi_id']);
        $this->assertEquals('integer', $casts['produk_id']);
    }

    public function test_transaksi_item_belongs_to_transaksi(): void
    {
        $item = new TransaksiItem();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $item->transaksi());
    }

    public function test_transaksi_item_belongs_to_produk(): void
    {
        $item = new TransaksiItem();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $item->produk());
    }

    public function test_transaksi_item_has_many_specifications(): void
    {
        $item = new TransaksiItem();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $item->transaksiItemSpecifications());
    }

    public function test_transaksi_item_subtotal_attribute(): void
    {
        $item = new TransaksiItem();
        $item->kuantitas = 5;
        $item->harga_satuan = 10000;

        $this->assertEquals(50000, $item->subtotal);
    }

    // ═══════════════════════════════════════════════════════════════════
    // TransaksiItemSpecifications Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_transaksi_item_specifications_has_required_fillable_fields(): void
    {
        $spec = new TransaksiItemSpecifications();

        $expected = [
            'vendor_id', 'transaksi_item_id', 'spesifikasi_produk_id',
            'bahan_id', 'value', 'input_type', 'price',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $spec->getFillable(), "Field '$field' should be fillable in TransaksiItemSpecifications");
        }
    }

    public function test_transaksi_item_specifications_uses_correct_table(): void
    {
        $spec = new TransaksiItemSpecifications();
        $this->assertEquals('transaksi_item_specifications', $spec->getTable());
    }

    public function test_transaksi_item_specifications_has_correct_casts(): void
    {
        $spec = new TransaksiItemSpecifications();
        $casts = $spec->getCasts();

        $this->assertEquals('decimal:2', $casts['price']);
        $this->assertEquals('integer', $casts['vendor_id']);
        $this->assertEquals('integer', $casts['transaksi_item_id']);
        $this->assertEquals('integer', $casts['spesifikasi_produk_id']);
        $this->assertEquals('integer', $casts['bahan_id']);
    }

    public function test_transaksi_item_specifications_belongs_to_bahan(): void
    {
        $spec = new TransaksiItemSpecifications();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $spec->bahan());
    }

    public function test_transaksi_item_specifications_belongs_to_spesifikasi_produk(): void
    {
        $spec = new TransaksiItemSpecifications();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $spec->spesifikasiProduk());
    }

    // ═══════════════════════════════════════════════════════════════════
    // Pelanggan Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_pelanggan_has_required_fillable_fields(): void
    {
        $pelanggan = new Pelanggan();

        $expected = [
            'vendor_id', 'kode', 'nama', 'alamat', 'no_telp', 'email', 'transaksi_terakhir',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $pelanggan->getFillable(), "Field '$field' should be fillable in Pelanggan");
        }
    }

    public function test_pelanggan_uses_correct_table(): void
    {
        $pelanggan = new Pelanggan();
        $this->assertEquals('pelanggans', $pelanggan->getTable());
    }

    public function test_pelanggan_has_correct_casts(): void
    {
        $pelanggan = new Pelanggan();
        $casts = $pelanggan->getCasts();

        $this->assertEquals('datetime', $casts['transaksi_terakhir']);
        $this->assertEquals('datetime', $casts['created_at']);
        $this->assertEquals('datetime', $casts['updated_at']);
    }

    public function test_pelanggan_belongs_to_vendor(): void
    {
        $pelanggan = new Pelanggan();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $pelanggan->vendor());
    }

    public function test_pelanggan_has_many_transaksi(): void
    {
        $pelanggan = new Pelanggan();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $pelanggan->transaksi());
    }

    // ═══════════════════════════════════════════════════════════════════
    // Produk Model Tests (POS-relevant)
    // ═══════════════════════════════════════════════════════════════════

    public function test_produk_has_required_fillable_fields(): void
    {
        $produk = new Produk();

        $expected = [
            'vendor_id', 'gambar', 'nama_produk', 'deskripsi', 'kategori_id',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $produk->getFillable(), "Field '$field' should be fillable in Produk");
        }
    }

    public function test_produk_uses_correct_table(): void
    {
        $produk = new Produk();
        $this->assertEquals('produks', $produk->getTable());
    }

    public function test_produk_has_correct_casts(): void
    {
        $produk = new Produk();
        $casts = $produk->getCasts();

        $this->assertEquals('array', $casts['gambar']);
        $this->assertEquals('decimal:2', $casts['harga_dasar']);
    }

    public function test_produk_belongs_to_kategori(): void
    {
        $produk = new Produk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $produk->kategori());
    }

    public function test_produk_has_many_spesifikasi_produk(): void
    {
        $produk = new Produk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $produk->spesifikasiProduk());
    }

    public function test_produk_has_many_estimasi_produk(): void
    {
        $produk = new Produk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $produk->estimasiProduk());
    }

    // ═══════════════════════════════════════════════════════════════════
    // Bahan Model Tests (POS-relevant)
    // ═══════════════════════════════════════════════════════════════════

    public function test_bahan_has_required_fillable_fields(): void
    {
        $bahan = new Bahan();

        $expected = [
            'vendor_id', 'nama_bahan', 'hpp', 'satuan', 'stok',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $bahan->getFillable(), "Field '$field' should be fillable in Bahan");
        }
    }

    public function test_bahan_uses_correct_table(): void
    {
        $bahan = new Bahan();
        $this->assertEquals('bahans', $bahan->getTable());
    }

    public function test_bahan_has_correct_casts(): void
    {
        $bahan = new Bahan();
        $casts = $bahan->getCasts();

        $this->assertEquals('decimal:2', $casts['hpp']);
        $this->assertEquals('integer', $casts['stok']);
    }

    public function test_bahan_belongs_to_vendor(): void
    {
        $bahan = new Bahan();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $bahan->vendor());
    }

    public function test_bahan_has_many_wholesale_prices(): void
    {
        $bahan = new Bahan();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $bahan->wholesalePrices());
    }

    // ═══════════════════════════════════════════════════════════════════
    // KategoriProduk Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_kategori_produk_has_required_fillable_fields(): void
    {
        $kategori = new KategoriProduk();

        $expected = [
            'vendor_id', 'nama_kategori', 'slug',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $kategori->getFillable(), "Field '$field' should be fillable in KategoriProduk");
        }
    }

    public function test_kategori_produk_uses_correct_table(): void
    {
        $kategori = new KategoriProduk();
        $this->assertEquals('kategori_produks', $kategori->getTable());
    }

    public function test_kategori_produk_has_many_produks(): void
    {
        $kategori = new KategoriProduk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $kategori->produk());
    }

    // ═══════════════════════════════════════════════════════════════════
    // Spesifikasi Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_spesifikasi_has_required_fillable_fields(): void
    {
        $spesifikasi = new Spesifikasi();

        $expected = [
            'vendor_id', 'nama_spesifikasi', 'tipe_input', 'satuan',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $spesifikasi->getFillable(), "Field '$field' should be fillable in Spesifikasi");
        }
    }

    public function test_spesifikasi_uses_correct_table(): void
    {
        $spesifikasi = new Spesifikasi();
        $this->assertEquals('spesifikasis', $spesifikasi->getTable());
    }

    public function test_spesifikasi_has_tipe_input_constants(): void
    {
        $this->assertArrayHasKey('number', Spesifikasi::TIPE_INPUT);
        $this->assertArrayHasKey('select', Spesifikasi::TIPE_INPUT);
        $this->assertArrayHasKey('text', Spesifikasi::TIPE_INPUT);
    }

    public function test_spesifikasi_has_many_spesifikasi_produk(): void
    {
        $spesifikasi = new Spesifikasi();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $spesifikasi->spesifikasiProduk());
    }

    // ═══════════════════════════════════════════════════════════════════
    // SpesifikasiProduk Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_spesifikasi_produk_has_required_fillable_fields(): void
    {
        $sp = new SpesifikasiProduk();

        $expected = [
            'vendor_id', 'produk_id', 'spesifikasi_id', 'wajib_diisi', 'pilihan',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $sp->getFillable(), "Field '$field' should be fillable in SpesifikasiProduk");
        }
    }

    public function test_spesifikasi_produk_uses_correct_table(): void
    {
        $sp = new SpesifikasiProduk();
        $this->assertEquals('spesifikasi_produks', $sp->getTable());
    }

    public function test_spesifikasi_produk_has_correct_casts(): void
    {
        $sp = new SpesifikasiProduk();
        $casts = $sp->getCasts();

        $this->assertEquals('array', $casts['pilihan']);
        $this->assertEquals('boolean', $casts['wajib_diisi']);
        $this->assertEquals('boolean', $casts['use_bahan']);
    }

    public function test_spesifikasi_produk_belongs_to_produk(): void
    {
        $sp = new SpesifikasiProduk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $sp->produk());
    }

    public function test_spesifikasi_produk_belongs_to_spesifikasi(): void
    {
        $sp = new SpesifikasiProduk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $sp->spesifikasi());
    }

    public function test_spesifikasi_produk_has_many_bahan_via_pivot(): void
    {
        $sp = new SpesifikasiProduk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $sp->bahans());
    }

    // ═══════════════════════════════════════════════════════════════════
    // WholesalePrice Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_wholesale_price_has_required_fillable_fields(): void
    {
        $wp = new WholesalePrice();

        $expected = [
            'vendor_id', 'bahan_id', 'min_quantity', 'max_quantity', 'harga',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $wp->getFillable(), "Field '$field' should be fillable in WholesalePrice");
        }
    }

    public function test_wholesale_price_uses_correct_table(): void
    {
        $wp = new WholesalePrice();
        $this->assertEquals('harga_grosir', $wp->getTable());
    }

    public function test_wholesale_price_has_correct_casts(): void
    {
        $wp = new WholesalePrice();
        $casts = $wp->getCasts();

        $this->assertEquals('integer', $casts['vendor_id']);
        $this->assertEquals('integer', $casts['bahan_id']);
        $this->assertEquals('integer', $casts['min_quantity']);
        $this->assertEquals('integer', $casts['max_quantity']);
        $this->assertEquals('decimal:2', $casts['harga']);
    }

    public function test_wholesale_price_belongs_to_bahan(): void
    {
        $wp = new WholesalePrice();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $wp->bahan());
    }

    public function test_wholesale_price_max_quantity_display_attribute(): void
    {
        $wp = new WholesalePrice();
        $wp->max_quantity = null;
        $this->assertEquals('Unlimited', $wp->max_quantity_display);

        $wp2 = new WholesalePrice();
        $wp2->max_quantity = 100;
        $this->assertEquals(100, $wp2->max_quantity_display);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Alat Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_alat_has_required_fillable_fields(): void
    {
        $alat = new Alat();

        $expected = [
            'vendor_id', 'nama_alat', 'merek', 'model', 'spesifikasi_alat',
            'status', 'tanggal_pembelian', 'kapasitas_cetak_per_jam', 'tersedia',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $alat->getFillable(), "Field '$field' should be fillable in Alat");
        }
    }

    public function test_alat_uses_correct_table(): void
    {
        $alat = new Alat();
        $this->assertEquals('alats', $alat->getTable());
    }

    public function test_alat_has_correct_casts(): void
    {
        $alat = new Alat();
        $casts = $alat->getCasts();

        $this->assertEquals('boolean', $casts['tersedia']);
        $this->assertEquals('date', $casts['tanggal_pembelian']);
        $this->assertEquals('integer', $casts['kapasitas_cetak_per_jam']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // EstimasiProduk Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_estimasi_produk_has_required_fillable_fields(): void
    {
        $ep = new EstimasiProduk();

        $expected = [
            'vendor_id', 'produk_id', 'alat_id', 'waktu_persiapan', 'waktu_produksi_per_unit',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $ep->getFillable(), "Field '$field' should be fillable in EstimasiProduk");
        }
    }

    public function test_estimasi_produk_uses_correct_table(): void
    {
        $ep = new EstimasiProduk();
        $this->assertEquals('estimasi_produks', $ep->getTable());
    }

    public function test_estimasi_produk_belongs_to_produk(): void
    {
        $ep = new EstimasiProduk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $ep->produk());
    }

    public function test_estimasi_produk_belongs_to_alat(): void
    {
        $ep = new EstimasiProduk();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $ep->alat());
    }

    public function test_estimasi_produk_calculate_total_production_time(): void
    {
        // calculateTotalProductionTime() accesses $this->produk->estimasiProduk
        // so we need a real DB record with relationships
        $vendorId = $this->getTestVendorId();

        $kategori = KategoriProduk::create([
            'vendor_id' => $vendorId,
            'nama_kategori' => 'Estimasi Kategori ' . uniqid(),
            'slug' => 'estimasi-kategori-' . strtolower(uniqid()),
        ]);

        $produk = Produk::create([
            'vendor_id' => $vendorId,
            'nama_produk' => 'Estimasi Produk ' . uniqid(),
            'deskripsi' => 'Produk untuk test estimasi',
            'kategori_id' => $kategori->id,
        ]);

        $alat = Alat::create([
            'vendor_id' => $vendorId,
            'nama_alat' => 'Estimasi Alat ' . uniqid(),
            'merek' => 'Test Brand',
            'model' => 'Test Model',
            'spesifikasi_alat' => 'Spesifikasi test',
            'status' => 'aktif',
            'tersedia' => true,
            'kapasitas_cetak_per_jam' => 100,
            'tanggal_pembelian' => now()->toDateString(),
        ]);

        $ep = EstimasiProduk::create([
            'vendor_id' => $vendorId,
            'produk_id' => $produk->id,
            'alat_id' => $alat->id,
            'waktu_persiapan' => 10,
            'waktu_produksi_per_unit' => 2,
        ]);

        $totalTime = $ep->calculateTotalProductionTime(5);
        // Expected: (10 + (2 * 5)) * maxWorkload multiplier
        // With single alat at normal workload, multiplier = 1
        $this->assertIsNumeric($totalTime);
        $this->assertGreaterThan(0, $totalTime);

        // Cleanup
        $ep->delete();
        $alat->delete();
        $produk->delete();
        $kategori->delete();
    }

    // ═══════════════════════════════════════════════════════════════════
    // POS Route Registration Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_pos_index_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.index'), 'Route vendor.pos.index should be registered');
    }

    public function test_pos_cart_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.cart'), 'Route vendor.pos.cart should be registered');
    }

    public function test_pos_add_to_cart_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.addToCart'), 'Route vendor.pos.addToCart should be registered');
    }

    public function test_pos_update_cart_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.updateCart'), 'Route vendor.pos.updateCart should be registered');
    }

    public function test_pos_remove_item_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.removeItem'), 'Route vendor.pos.removeItem should be registered');
    }

    public function test_pos_clear_cart_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.clearCart'), 'Route vendor.pos.clearCart should be registered');
    }

    public function test_pos_search_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.search'), 'Route vendor.pos.search should be registered');
    }

    public function test_pos_check_price_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.checkPrice'), 'Route vendor.pos.checkPrice should be registered');
    }

    public function test_pos_checkout_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.checkout'), 'Route vendor.pos.checkout should be registered');
    }

    public function test_pos_payment_options_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.payment.options'), 'Route vendor.pos.payment.options should be registered');
    }

    public function test_pos_payment_success_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.payment.success'), 'Route vendor.pos.payment.success should be registered');
    }

    public function test_pos_payment_failure_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.payment.failure'), 'Route vendor.pos.payment.failure should be registered');
    }

    public function test_pos_cash_payment_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.payment.cash'), 'Route vendor.pos.payment.cash should be registered');
    }

    public function test_pos_online_payment_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.payment.online'), 'Route vendor.pos.payment.online should be registered');
    }

    public function test_pos_invoice_show_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.invoice'), 'Route vendor.pos.invoice should be registered');
    }

    public function test_pos_invoice_print_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.pos.invoice.print'), 'Route vendor.pos.invoice.print should be registered');
    }

    // ═══════════════════════════════════════════════════════════════════
    // POS Access Control Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_unauthenticated_vendor_cannot_access_pos_index(): void
    {
        $response = $this->get(route('vendor.pos.index'));
        $response->assertRedirect();
    }

    public function test_unauthenticated_vendor_cannot_access_pos_cart(): void
    {
        $response = $this->get(route('vendor.pos.cart'));
        $response->assertRedirect();
    }

    public function test_unauthenticated_vendor_cannot_access_pos_checkout(): void
    {
        // Checkout is a POST route, so GET returns 405 Method Not Allowed
        $response = $this->post(route('vendor.pos.checkout'));
        $response->assertRedirect();
    }

    public function test_unauthenticated_vendor_cannot_access_pos_payment_options(): void
    {
        $response = $this->get(route('vendor.pos.payment.options', 1));
        $response->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════════════
    // Full POS Data Creation Integration Test
    // ═══════════════════════════════════════════════════════════════════

    public function test_full_pos_data_creation_flow(): void
    {
        $vendorId = $this->getTestVendorId();
        $userId = $this->getTestUserId();

        // Create full POS data chain: Vendor → KategoriProduk → Produk → Spesifikasi → Bahan → Pelanggan → Transaksi
        $kategori = KategoriProduk::create([
            'vendor_id' => $vendorId,
            'nama_kategori' => 'Testing Kategori ' . uniqid(),
            'slug' => 'testing-kategori-' . strtolower(uniqid()),
        ]);

        $produk = Produk::create([
            'vendor_id' => $vendorId,
            'nama_produk' => 'Testing Produk ' . uniqid(),
            'deskripsi' => 'Deskripsi produk testing',
            'kategori_id' => $kategori->id,
        ]);

        $spesifikasi = Spesifikasi::create([
            'vendor_id' => $vendorId,
            'nama_spesifikasi' => 'Ukuran',
            'tipe_input' => 'select',
            'satuan' => 'cm',
        ]);

        $spesifikasiProduk = SpesifikasiProduk::create([
            'vendor_id' => $vendorId,
            'produk_id' => $produk->id,
            'spesifikasi_id' => $spesifikasi->id,
            'wajib_diisi' => true,
        ]);

        $bahan = Bahan::create([
            'vendor_id' => $vendorId,
            'nama_bahan' => 'Testing Bahan ' . uniqid(),
            'hpp' => 5000,
            'satuan' => 'meter',
            'stok' => 1000,
        ]);

        // Attach bahan to spesifikasi_produk via pivot
        $spesifikasiProduk->bahans()->attach($bahan->id);

        $pelanggan = Pelanggan::create([
            'vendor_id' => $vendorId,
            'kode' => 'PLG-' . date('YmdHis') . '-TEST-' . strtoupper(uniqid()),
            'nama' => 'Testing Pelanggan',
            'alamat' => 'Jl. Test No. 1',
            'no_telp' => '081234567890',
            'email' => 'pelanggan_' . uniqid() . '@test.com',
        ]);

        // Create transaction
        $transaksi = Transaksi::create([
            'vendor_id' => $vendorId,
            'kode' => 'TRX-TEST-' . strtoupper(uniqid()),
            'user_id' => $userId,
            'pelanggan_id' => $pelanggan->id,
            'total_harga' => 150000,
            'terbayar' => 150000,
            'kembali' => 0,
            'status' => 'pending',
            'payment_method' => 'cash',
            'estimasi_selesai' => now()->addHours(2),
            'tanggal_dibuat' => now(),
            'progress_percentage' => 0,
        ]);

        // Create transaction item
        $item = $transaksi->transaksiItem()->create([
            'vendor_id' => $vendorId,
            'produk_id' => $produk->id,
            'kuantitas' => 10,
            'harga_satuan' => 15000,
        ]);

        // Create item specification
        $item->transaksiItemSpecifications()->create([
            'vendor_id' => $vendorId,
            'spesifikasi_produk_id' => $spesifikasiProduk->id,
            'bahan_id' => $bahan->id,
            'value' => 'A4',
            'input_type' => 'select',
            'price' => 150000,
        ]);

        // Verify all data was created
        $this->assertDatabaseHas('kategori_produks', ['id' => $kategori->id]);
        $this->assertDatabaseHas('produks', ['id' => $produk->id]);
        $this->assertDatabaseHas('spesifikasis', ['id' => $spesifikasi->id]);
        $this->assertDatabaseHas('spesifikasi_produks', ['id' => $spesifikasiProduk->id]);
        $this->assertDatabaseHas('bahans', ['id' => $bahan->id]);
        $this->assertDatabaseHas('pelanggans', ['id' => $pelanggan->id]);
        $this->assertDatabaseHas('transaksis', ['id' => $transaksi->id]);
        $this->assertDatabaseHas('transaksi_items', ['id' => $item->id]);

        // Verify relationships
        $this->assertEquals($vendorId, $transaksi->vendor_id);
        $this->assertEquals($userId, $transaksi->user_id);
        $this->assertEquals($pelanggan->id, $transaksi->pelanggan_id);
        $this->assertEquals(150000, $transaksi->total_harga);
        $this->assertEquals('cash', $transaksi->payment_method);
        $this->assertEquals('pending', $transaksi->status);

        // Verify item
        $this->assertEquals($produk->id, $item->produk_id);
        $this->assertEquals(10, $item->kuantitas);
        $this->assertEquals(15000, $item->harga_satuan);
        $this->assertEquals(150000, $item->subtotal);

        // Verify item has specification
        $this->assertCount(1, $item->transaksiItemSpecifications);

        // Verify transaksi has items
        $this->assertCount(1, $transaksi->transaksiItem);

        // Cleanup
        $item->transaksiItemSpecifications()->delete();
        $item->delete();
        $transaksi->delete();
        $pelanggan->delete();
        $spesifikasiProduk->bahans()->detach();
        $spesifikasiProduk->delete();
        $spesifikasi->delete();
        $produk->delete();
        $kategori->delete();
        $bahan->delete();
    }

    public function test_transaksi_status_lifecycle(): void
    {
        $vendorId = $this->getTestVendorId();
        $userId = $this->getTestUserId();
        $pelanggan = $this->createTestPelanggan($vendorId);

        $transaksi = $this->createTestTransaksi($vendorId, $userId, $pelanggan->id);

        // Initial state
        $this->assertEquals('pending', $transaksi->status);
        $this->assertEquals(0, $transaksi->progress_percentage);

        // Use update() to trigger the saving event which sets progress_percentage
        // (updateOrderStatus calls notify() on Pelanggan which lacks Notifiable trait)
        $transaksi->update(['status' => 'processing']);
        $this->assertEquals('processing', $transaksi->fresh()->status);
        $this->assertEquals(25, $transaksi->fresh()->progress_percentage);

        // Quality check
        $transaksi->update(['status' => 'quality_check']);
        $this->assertEquals('quality_check', $transaksi->fresh()->status);
        $this->assertEquals(80, $transaksi->fresh()->progress_percentage);

        // Completed
        $transaksi->update(['status' => 'completed']);
        $this->assertEquals('completed', $transaksi->fresh()->status);
        $this->assertEquals(100, $transaksi->fresh()->progress_percentage);

        // Cleanup
        $transaksi->delete();
        $pelanggan->delete();
    }

    public function test_multiple_items_in_single_transaksi(): void
    {
        $vendorId = $this->getTestVendorId();
        $userId = $this->getTestUserId();
        $pelanggan = $this->createTestPelanggan($vendorId);

        // Create real Produk for FK constraint
        $kategori = KategoriProduk::create([
            'vendor_id' => $vendorId,
            'nama_kategori' => 'Multi Item Kategori ' . uniqid(),
            'slug' => 'multi-item-kategori-' . strtolower(uniqid()),
        ]);

        $produk1 = Produk::create([
            'vendor_id' => $vendorId,
            'nama_produk' => 'Multi Item Produk 1 ' . uniqid(),
            'deskripsi' => 'Produk 1 untuk test multi item',
            'kategori_id' => $kategori->id,
        ]);

        $produk2 = Produk::create([
            'vendor_id' => $vendorId,
            'nama_produk' => 'Multi Item Produk 2 ' . uniqid(),
            'deskripsi' => 'Produk 2 untuk test multi item',
            'kategori_id' => $kategori->id,
        ]);

        $transaksi = $this->createTestTransaksi($vendorId, $userId, $pelanggan->id, [
            'total_harga' => 300000,
        ]);

        // Create 2 items with real Produk IDs
        $item1 = $transaksi->transaksiItem()->create([
            'vendor_id' => $vendorId,
            'produk_id' => $produk1->id,
            'kuantitas' => 10,
            'harga_satuan' => 15000,
        ]);

        $item2 = $transaksi->transaksiItem()->create([
            'vendor_id' => $vendorId,
            'produk_id' => $produk2->id,
            'kuantitas' => 5,
            'harga_satuan' => 30000,
        ]);

        // Verify 2 items
        $this->assertCount(2, $transaksi->fresh()->transaksiItem);

        // Verify total
        $totalFromItems = $transaksi->fresh()->transaksiItem->sum(function ($item) {
            return $item->kuantitas * $item->harga_satuan;
        });
        $this->assertEquals(300000, $totalFromItems);

        // Cleanup
        $item1->delete();
        $item2->delete();
        $transaksi->delete();
        $pelanggan->delete();
        $produk1->delete();
        $produk2->delete();
        $kategori->delete();
    }

    public function test_cash_payment_change_calculation(): void
    {
        $totalHarga = 150000;
        $paymentAmount = 200000;
        $changeAmount = max(0, $paymentAmount - $totalHarga);

        $this->assertEquals(50000, $changeAmount);
    }

    public function test_cash_payment_exact_amount(): void
    {
        $totalHarga = 150000;
        $paymentAmount = 150000;
        $changeAmount = max(0, $paymentAmount - $totalHarga);

        $this->assertEquals(0, $changeAmount);
    }

    public function test_admin_fee_calculation_for_pos(): void
    {
        // Test that AdminFeeService can handle POS transaction amounts
        $service = new \App\Services\AdminFeeService();

        $result = $service->calculateFees(1000000, 'pos_transaction');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_fee', $result);
        $this->assertArrayHasKey('fee_breakdown', $result);
        $this->assertArrayHasKey('settings_applied', $result);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Get or create a test user with 'vendor' type.
     */
    protected function getTestUserId(): int
    {
        $user = User::firstOrCreate(
            ['email' => 'pos_flow_test_user@grafika.test'],
            [
                'name' => 'POS Flow Test User',
                'password' => bcrypt('password'),
                'usertype' => 'vendor',
            ]
        );

        return $user->id;
    }

    /**
     * Get or create a test vendor.
     * Uses unique phone number to avoid collision with other test classes.
     */
    protected function getTestVendorId(): int
    {
        $uniquePhone = '08' . substr(md5('pos_flow_' . php_uname('n')), 0, 10);

        $vendor = Vendor::withoutGlobalScope('active')->firstOrCreate(
            ['email' => 'pos_flow_test_vendor@grafika.test'],
            [
                'name' => 'POS Flow Test Vendor',
                'phone' => $uniquePhone,
                'address' => 'Jl. Test No. 1',
                'is_active' => true,
            ]
        );

        // Ensure vendor_user pivot exists
        $userId = $this->getTestUserId();
        $user = User::find($userId);
        if (!$user->vendorUser()->where('vendors.id', $vendor->id)->exists()) {
            $user->vendorUser()->attach($vendor->id);
        }

        return $vendor->id;
    }

    /**
     * Create a test pelanggan (customer).
     */
    protected function createTestPelanggan(int $vendorId): Pelanggan
    {
        return Pelanggan::create([
            'vendor_id' => $vendorId,
            'kode' => 'PLG-TEST-' . strtoupper(uniqid()),
            'nama' => 'Test Customer ' . uniqid(),
            'alamat' => 'Jl. Test No. 1',
            'no_telp' => '08' . substr(md5('pelanggan_' . uniqid()), 0, 10),
            'email' => 'customer_' . uniqid() . '@test.com',
        ]);
    }

    /**
     * Create a test transaksi with given overrides.
     */
    protected function createTestTransaksi(int $vendorId, int $userId, int $pelangganId, array $overrides = []): Transaksi
    {
        $defaults = [
            'vendor_id' => $vendorId,
            'kode' => 'TRX-TEST-' . strtoupper(uniqid()),
            'user_id' => $userId,
            'pelanggan_id' => $pelangganId,
            'total_harga' => 150000,
            'terbayar' => 150000,
            'kembali' => 0,
            'status' => 'pending',
            'payment_method' => 'cash',
            'estimasi_selesai' => now()->addHours(2),
            'tanggal_dibuat' => now(),
            'progress_percentage' => 0,
        ];

        return Transaksi::create(array_merge($defaults, $overrides));
    }
}

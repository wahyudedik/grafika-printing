<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vendor;
use App\Models\Vendor\Linktree;
use App\Models\Vendor\LinktreeLink;
use App\Models\Vendor\LinktreeSocial;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LinktreeFlowTest extends TestCase
{
    // Note: We don't use RefreshDatabase because tests run against the real DB.
    // All tests use unique identifiers to avoid collisions.

    // ═══════════════════════════════════════════════════════════════════
    // Linktree Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_linktree_has_required_fillable_fields(): void
    {
        $linktree = new Linktree();

        $expected = [
            'title', 'custom_url', 'bio', 'avatar', 'banner',
            'template', 'primary_color', 'secondary_color', 'bg_color',
            'text_color', 'button_style', 'is_active', 'show_qris',
            'qris_image', 'meta_title', 'meta_description',
            'views_count', 'clicks_count',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $linktree->getFillable(), "Field '$field' should be fillable in Linktree");
        }
    }

    public function test_linktree_has_correct_casts(): void
    {
        $linktree = new Linktree();
        $casts = $linktree->getCasts();

        $this->assertArrayHasKey('is_active', $casts);
        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertArrayHasKey('show_qris', $casts);
        $this->assertEquals('boolean', $casts['show_qris']);
        $this->assertArrayHasKey('views_count', $casts);
        $this->assertEquals('integer', $casts['views_count']);
        $this->assertArrayHasKey('clicks_count', $casts);
        $this->assertEquals('integer', $casts['clicks_count']);
    }

    public function test_linktree_uses_correct_table(): void
    {
        $linktree = new Linktree();
        $this->assertEquals('linktrees', $linktree->getTable());
    }

    public function test_linktree_belongs_to_vendor(): void
    {
        $linktree = new Linktree();
        $this->assertTrue(method_exists($linktree, 'vendor'));
        $relation = $linktree->vendor();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_linktree_has_many_links(): void
    {
        $linktree = new Linktree();
        $this->assertTrue(method_exists($linktree, 'links'));
        $relation = $linktree->links();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_linktree_has_many_socials(): void
    {
        $linktree = new Linktree();
        $this->assertTrue(method_exists($linktree, 'socials'));
        $relation = $linktree->socials();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_linktree_has_many_linktree_products(): void
    {
        $linktree = new Linktree();
        $this->assertTrue(method_exists($linktree, 'linktreeProducts'));
        $relation = $linktree->linktreeProducts();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    public function test_linktree_increment_views(): void
    {
        $linktree = Linktree::where('views_count', '>', 0)->first();
        if (!$linktree) {
            $this->markTestSkipped('No linktree with views found');
        }

        $before = $linktree->views_count;
        $linktree->incrementViews();
        $linktree->refresh();

        $this->assertEquals($before + 1, $linktree->views_count);
    }

    public function test_linktree_increment_clicks(): void
    {
        $linktree = Linktree::where('clicks_count', '>', 0)->first();
        if (!$linktree) {
            $this->markTestSkipped('No linktree with clicks found');
        }

        $before = $linktree->clicks_count;
        $linktree->incrementClicks();
        $linktree->refresh();

        $this->assertEquals($before + 1, $linktree->clicks_count);
    }

    public function test_linktree_find_by_custom_url(): void
    {
        $linktree = Linktree::where('is_active', true)->where('custom_url', '!=', null)->first();
        if (!$linktree) {
            $this->markTestSkipped('No active linktree with custom_url found');
        }

        $found = Linktree::findByCustomUrl($linktree->custom_url);
        $this->assertNotNull($found);
        $this->assertEquals($linktree->id, $found->id);
    }

    public function test_linktree_find_by_custom_url_returns_null_for_inactive(): void
    {
        $linktree = Linktree::where('is_active', false)->where('custom_url', '!=', null)->first();
        if (!$linktree) {
            $this->markTestSkipped('No inactive linktree with custom_url found');
        }

        $found = Linktree::findByCustomUrl($linktree->custom_url);
        $this->assertNull($found);
    }

    public function test_linktree_get_template_classes_minimal(): void
    {
        $linktree = new Linktree(['template' => 'minimal']);
        $classes = $linktree->getTemplateClasses();

        $this->assertArrayHasKey('bg', $classes);
        $this->assertArrayHasKey('card', $classes);
        $this->assertArrayHasKey('button', $classes);
    }

    public function test_linktree_get_template_classes_colorful(): void
    {
        $linktree = new Linktree(['template' => 'colorful']);
        $classes = $linktree->getTemplateClasses();

        $this->assertStringContainsString('purple', $classes['bg']);
    }

    public function test_linktree_get_template_classes_dark(): void
    {
        $linktree = new Linktree(['template' => 'dark']);
        $classes = $linktree->getTemplateClasses();

        $this->assertStringContainsString('gray-900', $classes['bg']);
    }

    public function test_linktree_get_template_classes_professional(): void
    {
        $linktree = new Linktree(['template' => 'professional']);
        $classes = $linktree->getTemplateClasses();

        $this->assertStringContainsString('slate', $classes['bg']);
    }

    public function test_linktree_has_product_catalog_returns_false_when_empty(): void
    {
        $linktree = new Linktree();
        $this->assertFalse($linktree->hasProductCatalog());
    }

    // ═══════════════════════════════════════════════════════════════════
    // LinktreeLink Model Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_linktree_link_has_required_fillable_fields(): void
    {
        $link = new LinktreeLink();

        $expected = [
            'linktree_id', 'title', 'url', 'icon', 'type',
            'is_active', 'sort_order', 'clicks_count',
        ];

        foreach ($expected as $field) {
            $this->assertContains($field, $link->getFillable(), "Field '$field' should be fillable in LinktreeLink");
        }
    }

    public function test_linktree_link_has_correct_casts(): void
    {
        $link = new LinktreeLink();
        $casts = $link->getCasts();

        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertEquals('integer', $casts['sort_order']);
        $this->assertEquals('integer', $casts['clicks_count']);
    }

    public function test_linktree_link_uses_correct_table(): void
    {
        $link = new LinktreeLink();
        $this->assertEquals('linktree_links', $link->getTable());
    }

    public function test_linktree_link_belongs_to_linktree(): void
    {
        $link = new LinktreeLink();
        $relation = $link->linktree();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_linktree_link_icon_html_returns_svg_for_known_types(): void
    {
        $types = ['link', 'qris', 'whatsapp', 'phone', 'email'];

        foreach ($types as $type) {
            $link = new LinktreeLink(['type' => $type]);
            $iconHtml = $link->icon_html;
            $this->assertStringContainsString('<svg', $iconHtml, "Icon for type '$type' should contain SVG");
        }
    }

    public function test_linktree_link_icon_html_defaults_to_link_for_unknown_type(): void
    {
        $link = new LinktreeLink(['type' => 'unknown_type']);
        $iconHtml = $link->icon_html;
        $this->assertStringContainsString('<svg', $iconHtml);
    }

    public function test_linktree_link_increment_clicks(): void
    {
        $vendor = Vendor::where('is_active', true)->first();
        if (!$vendor) {
            $this->markTestSkipped('No active vendor found');
        }

        // Set tenant context so TenantModel can resolve vendor_id
        app(\App\Services\TenantManager::class)->setVendorId($vendor->id);

        $linktree = Linktree::create([
            'vendor_id' => $vendor->id,
            'title' => 'Test Linktree ' . uniqid(),
            'custom_url' => 'test-click-' . strtolower(uniqid()),
            'template' => 'minimal',
            'is_active' => true,
        ]);

        $link = LinktreeLink::create([
            'vendor_id' => $vendor->id,
            'linktree_id' => $linktree->id,
            'title' => 'Test Link',
            'url' => 'https://example.com',
            'type' => 'link',
            'is_active' => true,
            'sort_order' => 1,
            'clicks_count' => 0,
        ]);

        $link->incrementClicks();
        $link->refresh();

        $this->assertEquals(1, $link->clicks_count);

        // Cleanup
        $link->delete();
        $linktree->delete();
    }

    // ═══════════════════════════════════════════════════════════════════
    // Linktree Route Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_vendor_linktree_index_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.linktree.index'), 'vendor.linktree.index route should exist');
    }

    public function test_vendor_linktree_create_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.linktree.create'), 'vendor.linktree.create route should exist');
    }

    public function test_vendor_linktree_store_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.linktree.store'), 'vendor.linktree.store route should exist');
    }

    public function test_vendor_linktree_show_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.linktree.show'), 'vendor.linktree.show route should exist');
    }

    public function test_vendor_linktree_edit_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.linktree.edit'), 'vendor.linktree.edit route should exist');
    }

    public function test_vendor_linktree_update_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.linktree.update'), 'vendor.linktree.update route should exist');
    }

    public function test_vendor_linktree_destroy_route_is_registered(): void
    {
        $this->assertTrue(Route::has('vendor.linktree.destroy'), 'vendor.linktree.destroy route should exist');
    }

    // ═══════════════════════════════════════════════════════════════════
    // Authentication Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_unauthenticated_vendor_cannot_access_linktree_index(): void
    {
        $response = $this->get(route('vendor.linktree.index'));
        $response->assertRedirect();
    }

    public function test_unauthenticated_vendor_cannot_access_linktree_create(): void
    {
        $response = $this->get(route('vendor.linktree.create'));
        $response->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════════════
    // Data Creation Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_full_linktree_data_creation_flow(): void
    {
        $vendor = Vendor::where('is_active', true)->first();
        if (!$vendor) {
            $this->markTestSkipped('No active vendor found');
        }

        // Set tenant context so TenantModel can resolve vendor_id
        app(\App\Services\TenantManager::class)->setVendorId($vendor->id);

        // Create Linktree (vendor_id is auto-filled by TenantModel)
        $linktree = Linktree::create([
            'title' => 'Test Linktree ' . uniqid(),
            'custom_url' => 'test-' . strtolower(uniqid()),
            'bio' => 'Test bio description',
            'template' => 'minimal',
            'primary_color' => '#6366f1',
            'secondary_color' => '#8b5cf6',
            'bg_color' => '#ffffff',
            'text_color' => '#1f2937',
            'button_style' => 'rounded',
            'is_active' => true,
            'show_qris' => false,
        ]);

        $this->assertNotNull($linktree->id);
        $this->assertEquals($vendor->id, $linktree->vendor_id);

        // Create LinktreeLink
        $link = LinktreeLink::create([
            'linktree_id' => $linktree->id,
            'title' => 'WhatsApp',
            'url' => 'https://wa.me/6281234567890',
            'type' => 'whatsapp',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertNotNull($link->id);
        $this->assertEquals($linktree->id, $link->linktree_id);

        // Create LinktreeSocial
        $social = LinktreeSocial::create([
            'linktree_id' => $linktree->id,
            'platform' => 'instagram',
            'url' => 'https://instagram.com/testaccount',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertNotNull($social->id);

        // Verify relationships
        $this->assertEquals(1, $linktree->links()->count());
        $this->assertEquals(1, $linktree->socials()->count());

        // Cleanup
        $social->delete();
        $link->delete();
        $linktree->delete();
    }
}

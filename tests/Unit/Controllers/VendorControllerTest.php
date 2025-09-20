<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendorControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Create and login as dev user
        $user = User::factory()->create(['usertype' => 'dev']);
        $this->actingAs($user);
    }

    public function test_index_displays_vendors()
    {
        $vendors = Vendor::factory(3)->create();

        $response = $this->get(route('admin.admin.vendors.index'));

        $response->assertStatus(200);
        $response->assertViewIs('dev.admin.vendors.index');
        $response->assertViewHas('vendors');
    }

    public function test_create_displays_form()
    {
        $response = $this->get(route('admin.vendors.create'));

        $response->assertStatus(200);
        $response->assertViewIs('dev.vendors.create');
    }

    public function test_store_creates_vendor_with_logo()
    {
        $user = User::factory()->create();
        $logo = UploadedFile::fake()->image('logo.jpg');

        $vendorData = [
            'name' => $this->faker->company,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'website' => $this->faker->url,
            'is_active' => true,
            'logo' => $logo,
            'users' => [$user->id]
        ];

        $response = $this->post(route('admin.vendors.store'), $vendorData);

        $response->assertRedirect(route('admin.vendors.index'));
        $this->assertDatabaseHas('vendors', [
            'name' => $vendorData['name'],
            'email' => $vendorData['email']
        ]);
        Storage::disk('public')->assertExists('vendors/' . $logo->hashName());
    }

    public function test_show_displays_vendor()
    {
        $vendor = Vendor::factory()->create();

        $response = $this->get(route('admin.vendors.show', $vendor->id));

        $response->assertStatus(200);
        $response->assertViewIs('dev.vendors.show');
        $response->assertViewHas('vendor');
    }

    public function test_edit_displays_form()
    {
        $vendor = Vendor::factory()->create();

        $response = $this->get(route('admin.vendors.edit', $vendor->id));

        $response->assertStatus(200);
        $response->assertViewIs('dev.vendors.edit');
        $response->assertViewHas('vendor');
    }

    public function test_update_vendor_with_new_logo()
    {
        $vendor = Vendor::factory()->create(['logo' => 'vendors/old-logo.jpg']);
        $newLogo = UploadedFile::fake()->image('new-logo.jpg');

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'address' => 'Updated Address',
            'website' => 'https://updated.com',
            'is_active' => true,
            'logo' => $newLogo
        ];

        $response = $this->put(route('admin.vendors.update', $vendor->id), $updatedData);

        $response->assertRedirect(route('admin.vendors.index'));
        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
        Storage::disk('public')->assertExists('vendors/' . $newLogo->hashName());
        Storage::disk('public')->assertMissing('vendors/old-logo.jpg');
    }

    public function test_destroy_vendor_with_logo()
    {
        $vendor = Vendor::factory()->create(['logo' => 'vendors/logo.jpg']);
        Storage::disk('public')->put('vendors/logo.jpg', 'fake-image-content');

        $response = $this->delete(route('admin.vendors.destroy', $vendor->id));

        $response->assertRedirect(route('admin.vendors.index'));
        $this->assertDatabaseMissing('vendors', ['id' => $vendor->id]);
        Storage::disk('public')->assertMissing('vendors/logo.jpg');
    }

    public function test_validation_error_on_store()
    {
        $response = $this->post(route('admin.vendors.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'phone', 'address', 'is_active']);
    }

    public function test_handles_invalid_vendor_id()
    {
        $response = $this->get(route('admin.vendors.show', 999999));

        $response->assertRedirect();
        $response->assertSessionHas('toast_error', 'Error loading vendor');
    }
}

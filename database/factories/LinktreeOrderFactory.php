<?php

namespace Database\Factories;

use App\Models\Vendor\LinktreeOrder;
use App\Models\Vendor;
use App\Models\Vendor\Linktree;
use App\Models\Vendor\LinktreeProduct;
use App\Models\Vendor\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor\LinktreeOrder>
 */
class LinktreeOrderFactory extends Factory
{
    protected $model = LinktreeOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'vendor_id' => Vendor::factory(),
            'linktree_id' => null, // Will be set in setUp or via relationship
            'linktree_product_id' => null, // Will be set in setUp or via relationship
            'produk_id' => null, // Will be set in setUp or via relationship
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->optional()->safeEmail(),
            'customer_phone' => '08' . $this->faker->numerify('##########'),
            'selected_specs' => [
                ['nama' => 'Ukuran', 'value' => 'A4'],
                ['nama' => 'Bahan', 'value' => 'Art Carton'],
            ],
            'notes' => $this->faker->optional()->sentence(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'total_price' => $this->faker->randomFloat(2, 10000, 500000),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_proof' => null,
            'vendor_notes' => null,
            'whatsapp_message' => null,
            'whatsapp_sent' => false,
        ];
    }

    /**
     * Indicate that the order is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that payment proof has been sent.
     */
    public function paymentProofSent(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'proof_sent',
        ]);
    }

    /**
     * Indicate that payment is confirmed.
     */
    public function paymentConfirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'confirmed',
        ]);
    }
}

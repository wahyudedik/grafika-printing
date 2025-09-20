<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'website' => $this->faker->optional()->url(),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'logo' => $this->faker->optional()->imageUrl(200, 200, 'business'),
            'auto_withdrawal_enabled' => $this->faker->boolean(30),
            'auto_withdrawal_date' => $this->faker->numberBetween(1, 31),
            'auto_withdrawal_amount' => $this->faker->numberBetween(100000, 5000000),
            'auto_withdrawal_method' => $this->faker->randomElement(['bank_transfer', 'e_wallet', 'cash']),
            'auto_withdrawal_account_number' => $this->faker->numerify('##########'),
            'auto_withdrawal_account_name' => $this->faker->name(),
            'auto_withdrawal_bank_name' => $this->faker->randomElement(['BCA', 'BRI', 'BNI', 'DANA', 'OVO']),
            'primary_bank_name' => $this->faker->randomElement(['BCA', 'BRI', 'BNI', 'MANDIRI']),
            'primary_account_number' => $this->faker->numerify('##########'),
            'primary_account_name' => $this->faker->name(),
            'primary_bank_code' => $this->faker->randomElement(['BCA', 'BRI', 'BNI', 'MANDIRI']),
            'secondary_bank_name' => $this->faker->optional()->randomElement(['BCA', 'BRI', 'BNI', 'MANDIRI']),
            'secondary_account_number' => $this->faker->optional()->numerify('##########'),
            'secondary_account_name' => $this->faker->optional()->name(),
            'secondary_bank_code' => $this->faker->optional()->randomElement(['BCA', 'BRI', 'BNI', 'MANDIRI']),
            'ewallet_provider' => $this->faker->optional()->randomElement(['DANA', 'OVO', 'LINKAJA', 'SHOPEEPAY']),
            'ewallet_number' => $this->faker->optional()->numerify('##########'),
            'ewallet_name' => $this->faker->optional()->name(),
            'bank_verified' => $this->faker->boolean(60),
            'bank_verified_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'bank_verified_by' => $this->faker->optional()->name(),
        ];
    }

    /**
     * Indicate that the vendor is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the vendor is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the vendor has verified bank account.
     */
    public function verified(): static
    {
        return $this->state(fn(array $attributes) => [
            'bank_verified' => true,
            'bank_verified_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'bank_verified_by' => 'Admin',
        ]);
    }
}

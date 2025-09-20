<?php

namespace Database\Factories;

use App\Models\AdminFeeSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminFeeSetting>
 */
class AdminFeeSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdminFeeSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['fixed', 'percentage']);
        $value = $type === 'fixed'
            ? $this->faker->numberBetween(1000, 10000)
            : $this->faker->numberBetween(1, 20);

        return [
            'name' => $this->faker->words(3, true) . ' Fee',
            'description' => $this->faker->sentence(),
            'type' => $type,
            'value' => $value,
            'minimum_amount' => $this->faker->numberBetween(10000, 100000),
            'maximum_amount' => $this->faker->numberBetween(1000000, 10000000),
            'category' => $this->faker->randomElement(['auction', 'payment', 'transaction']),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'effective_from' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'effective_until' => $this->faker->dateTimeBetween('now', '+1 year'),
            'conditions' => json_encode([
                'min_bid_count' => $this->faker->numberBetween(1, 5),
                'max_bid_count' => $this->faker->numberBetween(10, 50),
            ]),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the admin fee setting is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the admin fee setting is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the admin fee setting is a fixed fee.
     */
    public function fixed(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'fixed',
            'value' => $this->faker->numberBetween(1000, 10000),
        ]);
    }

    /**
     * Indicate that the admin fee setting is a percentage fee.
     */
    public function percentage(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'percentage',
            'value' => $this->faker->numberBetween(1, 20),
        ]);
    }

    /**
     * Indicate that the admin fee setting is for auction category.
     */
    public function auction(): static
    {
        return $this->state(fn(array $attributes) => [
            'category' => 'auction',
        ]);
    }

    /**
     * Indicate that the admin fee setting is for payment category.
     */
    public function payment(): static
    {
        return $this->state(fn(array $attributes) => [
            'category' => 'payment',
        ]);
    }

    /**
     * Indicate that the admin fee setting is for transaction category.
     */
    public function transaction(): static
    {
        return $this->state(fn(array $attributes) => [
            'category' => 'transaction',
        ]);
    }

    /**
     * Indicate that the admin fee setting is effective from a specific date.
     */
    public function effectiveFrom(string $date): static
    {
        return $this->state(fn(array $attributes) => [
            'effective_from' => $date,
        ]);
    }

    /**
     * Indicate that the admin fee setting is effective until a specific date.
     */
    public function effectiveUntil(string $date): static
    {
        return $this->state(fn(array $attributes) => [
            'effective_until' => $date,
        ]);
    }

    /**
     * Indicate that the admin fee setting has specific minimum and maximum amounts.
     */
    public function withAmountRange(int $min, int $max): static
    {
        return $this->state(fn(array $attributes) => [
            'minimum_amount' => $min,
            'maximum_amount' => $max,
        ]);
    }

    /**
     * Indicate that the admin fee setting has specific conditions.
     */
    public function withConditions(array $conditions): static
    {
        return $this->state(fn(array $attributes) => [
            'conditions' => json_encode($conditions),
        ]);
    }

    /**
     * Indicate that the admin fee setting is created by a specific user.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Indicate that the admin fee setting is updated by a specific user.
     */
    public function updatedBy(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'updated_by' => $user->id,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\AdminFeeTransaction;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminFeeTransaction>
 */
class AdminFeeTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdminFeeTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $auctionAmount = $this->faker->numberBetween(10000, 1000000);
        $adminFeeAmount = $this->faker->numberBetween(1000, 100000);
        $paymentGatewayFee = $this->faker->numberBetween(100, 10000);
        $totalAmount = $auctionAmount + $adminFeeAmount + $paymentGatewayFee;

        return [
            'auction_id' => Auction::factory(),
            'vendor_id' => User::factory()->state(['usertype' => 'vendor']),
            'user_id' => User::factory()->state(['usertype' => 'user']),
            'auction_amount' => $auctionAmount,
            'admin_fee_amount' => $adminFeeAmount,
            'payment_gateway_fee' => $paymentGatewayFee,
            'total_amount' => $totalAmount,
            'vendor_receives' => $auctionAmount,
            'admin_receives' => $adminFeeAmount + $paymentGatewayFee,
            'status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'fee_breakdown' => json_encode([
                'admin_fees' => [
                    [
                        'name' => 'Admin Fee 10%',
                        'type' => 'percentage',
                        'value' => 10.00,
                        'amount' => $adminFeeAmount,
                    ]
                ],
                'payment_gateway' => [
                    'method' => 'bank_transfer',
                    'rate' => 1.5,
                    'amount' => $paymentGatewayFee,
                ]
            ]),
        ];
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the transaction is paid.
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'paid',
        ]);
    }

    /**
     * Indicate that the transaction failed.
     */
    public function failed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Indicate that the transaction is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'refunded',
        ]);
    }

    /**
     * Indicate that the transaction has a specific auction amount.
     */
    public function withAuctionAmount(int $amount): static
    {
        return $this->state(fn(array $attributes) => [
            'auction_amount' => $amount,
            'vendor_receives' => $amount,
        ]);
    }

    /**
     * Indicate that the transaction has a specific admin fee amount.
     */
    public function withAdminFeeAmount(int $amount): static
    {
        return $this->state(fn(array $attributes) => [
            'admin_fee_amount' => $amount,
            'admin_receives' => $amount + $attributes['payment_gateway_fee'],
        ]);
    }

    /**
     * Indicate that the transaction has a specific payment gateway fee.
     */
    public function withPaymentGatewayFee(int $amount): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_gateway_fee' => $amount,
            'admin_receives' => $attributes['admin_fee_amount'] + $amount,
        ]);
    }

    /**
     * Indicate that the transaction has a specific total amount.
     */
    public function withTotalAmount(int $amount): static
    {
        return $this->state(fn(array $attributes) => [
            'total_amount' => $amount,
        ]);
    }

    /**
     * Indicate that the transaction has a specific auction.
     */
    public function forAuction(Auction $auction): static
    {
        return $this->state(fn(array $attributes) => [
            'auction_id' => $auction->id,
        ]);
    }

    /**
     * Indicate that the transaction has a specific vendor.
     */
    public function forVendor(User $vendor): static
    {
        return $this->state(fn(array $attributes) => [
            'vendor_id' => $vendor->id,
        ]);
    }

    /**
     * Indicate that the transaction has a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the transaction has a specific fee breakdown.
     */
    public function withFeeBreakdown(array $breakdown): static
    {
        return $this->state(fn(array $attributes) => [
            'fee_breakdown' => json_encode($breakdown),
        ]);
    }

    /**
     * Indicate that the transaction has a specific date range.
     */
    public function between(string $startDate, string $endDate): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween($startDate, $endDate),
            'updated_at' => $this->faker->dateTimeBetween($startDate, $endDate),
        ]);
    }

    /**
     * Indicate that the transaction is from today.
     */
    public function today(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Indicate that the transaction is from yesterday.
     */
    public function yesterday(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);
    }

    /**
     * Indicate that the transaction is from last week.
     */
    public function lastWeek(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => now()->subWeek(),
            'updated_at' => now()->subWeek(),
        ]);
    }

    /**
     * Indicate that the transaction is from last month.
     */
    public function lastMonth(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => now()->subMonth(),
            'updated_at' => now()->subMonth(),
        ]);
    }
}

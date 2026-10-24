<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 50);
        $unitPrice = fake()->numberBetween(5_000, 250_000);

        return [
            'user_id' => User::factory(),
            'customer_name' => fake()->name(),
            'product_name' => fake()->randomElement(['Plakat Akrilik', 'Buket Wisuda', 'Boneka Wisuda']),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,
            'status' => 'pending',
            'due_date' => fake()->dateTimeBetween('+1 day', '+2 months')->format('Y-m-d'),
            'notes' => null,
        ];
    }
}

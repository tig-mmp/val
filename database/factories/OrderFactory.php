<?php

namespace Database\Factories;

use App\Models\Order;
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
        $bases = ['Chourição', 'Fiambre', 'Queijo'];

        return [
            'size' => fake()->randomElement(Order::SIZES),
            'base' => fake()->randomElement($bases),
            'state' => Order::STATE_PENDING,
        ];
    }
}

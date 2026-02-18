<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
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
        $sizes = ['Individual', 'Média', 'Grande', 'Familiar'];
        $bases = ['Chourição', 'Fiambre', 'Queijo'];
        return [
            'size' => fake()->randomElement($sizes),
            'base' => fake()->randomElement($bases),
        ];
    }
}

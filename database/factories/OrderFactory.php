<?php

namespace Database\Factories;

use App\Enums\OrderEnum;
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
        $enum = array_column(OrderEnum::cases(), 'value');
        
        return [
            'users_id' => rand(1, 3),
            'total_amount' => rand(10000, 100000) / 10,
            'status' => $enum[array_rand($enum)],
            'address' => fake()->address(),
        ];
    }
}

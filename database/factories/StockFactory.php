<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::factory(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'min_quantity' => fake()->numberBetween(-10000, 10000),
        ];
    }
}

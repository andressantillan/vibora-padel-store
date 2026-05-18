<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'url' => fake()->url(),
            'is_main' => fake()->boolean(),
            'sort' => fake()->numberBetween(-10000, 10000),
        ];
    }
}

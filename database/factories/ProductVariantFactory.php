<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => fake()->regexify('[A-Za-z0-9]{100}'),
            'price' => fake()->randomFloat(2, 0, 99999999.99),
            'color' => fake()->regexify('[A-Za-z0-9]{50}'),
            'size' => fake()->regexify('[A-Za-z0-9]{50}'),
            'weight' => fake()->randomFloat(2, 0, 999.99),
        ];
    }
}

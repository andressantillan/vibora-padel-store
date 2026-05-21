<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'address_id' => Address::factory(),
            'status' => fake()->regexify('[A-Za-z0-9]{50}'),
            'subtotal' => fake()->randomFloat(2, 0, 99999999.99),
            'discount' => fake()->randomFloat(2, 0, 99999999.99),
            'total' => fake()->randomFloat(2, 0, 99999999.99),
            'coupon_code' => fake()->regexify('[A-Za-z0-9]{50}'),
        ];
    }
}

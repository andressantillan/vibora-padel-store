<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'street' => fake()->streetName(),
            'city' => fake()->city(),
            'province' => fake()->regexify('[A-Za-z0-9]{100}'),
            'postal_code' => fake()->postcode(),
            'is_default' => fake()->boolean(),
        ];
    }
}

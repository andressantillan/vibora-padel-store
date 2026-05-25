<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'method' => fake()->regexify('[A-Za-z0-9]{50}'),
            'amount' => fake()->randomFloat(2, 0, 99999999.99),
            'status' => fake()->regexify('[A-Za-z0-9]{50}'),
            'reference' => fake()->regexify('[A-Za-z0-9]{100}'),
            'paid_at' => fake()->dateTime(),
        ];
    }
}

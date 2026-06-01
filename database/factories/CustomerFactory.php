<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'dni' => fake()->numberBetween(10000000, 45000000),
            'phone' => fake()->phoneNumber(),
            'birth_date' => fake()->date(),
        ];
    }
}

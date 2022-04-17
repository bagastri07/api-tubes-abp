<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(random_int(1,2), true),
            'price' => $this->faker->randomElement([50000, 100000, 500000, 600000]),
            'type' => $this->faker->randomElement(['product', 'ticket']),
            'stock' => $this->faker->numberBetween(2, 100)
        ];
    }
}

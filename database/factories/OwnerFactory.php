<?php

namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Owner>
 */
class OwnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password'),
            'birthday' => $this->faker->date('Y-m-d', '2002-03-09'),
            'phone_number' => $this->faker->e164PhoneNumber(),
            'shop' => ucwords($this->faker->words(2, true)),
            'shop_img_url' => Owner::getRandomIcon()
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    private static $districtList = [
        'Jakarta Utara', 'Jakarta Pusat', 'Kotawaringin Barat',
        'Bandung', 'Bogor'
    ];

    private static $provinceList = [
        'DKI Jakarta', 'DKI Jakarta', 'Kalimantan Tengah',
        'Jawa Barat', 'Jawa Barat'
    ];

    public function definition()
    {
        $size = sizeof(AddressFactory::$districtList);
        $randomIndex = random_int(0, $size - 1);

        return [
            'post_code' => $this->faker->postcode(),
            'street' => $this->faker->streetAddress(),
            'district' => AddressFactory::$districtList[$randomIndex],
            'province' => AddressFactory::$provinceList[$randomIndex]
        ];
    }
}

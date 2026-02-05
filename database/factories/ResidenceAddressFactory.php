<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResidenceAddress>
 */
class ResidenceAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		$hasPolishResidency = rand(1, 10) > 3;
        return [
            "country" => $hasPolishResidency ? "PL" : $this->faker->countryCode(),
			"commune" => $hasPolishResidency ? $this->faker->city() : null,
			"town" => $this->faker->city(),
			"street" => rand(1, 10) > 8 ? $this->faker->streetName : null,
			"house_number" => rand(1, 100),
			"postal_code" => rand(10, 99) . "-" . rand(100, 999)
        ];
    }
}

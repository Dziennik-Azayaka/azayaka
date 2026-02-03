<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guardian>
 */
class GuardianFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			"first_name" => $this->faker->firstName,
			"last_name" => $this->faker->lastName,
			"second_name" => rand(1, 10) > 7 ? $this->faker->firstName : null,
			"phone_number" => rand(1, 10) > 3 ? $this->faker->phoneNumber : null,
			"email" => rand(1, 10) > 3 ? $this->faker->email : null,
			"residence_address_id" => rand(1, 10)
		];
	}
}

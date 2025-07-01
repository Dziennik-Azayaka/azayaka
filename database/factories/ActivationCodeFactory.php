<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivationCode>
 */
class ActivationCodeFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		$words = $this->faker->words(10);
		$words_string = "";
		foreach ($words as $word) {
			$words_string .= $word . ",";
		}
		$words_string = substr($words_string, 0, -1);

		return [
			"words" => $words_string,
			"first_name" => $this->faker->firstName,
			"last_name" => $this->faker->lastName
		];
	}
}

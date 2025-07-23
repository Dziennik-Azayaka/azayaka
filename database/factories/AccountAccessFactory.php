<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountAccess>
 */
class AccountAccessFactory extends Factory
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
			"student_id" => Student::inRandomOrder()->first()->id
		];
	}
}

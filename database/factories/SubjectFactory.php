<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		$subjects = [
			"Język Polski", "Język Angielski", "Język Niemiecki", "Muzyka", "Plastyka", "Historia", "Wiedza o Społeczeństwie",
			"Przyroda", "Geografia", "Biologia", "Chemia", "Fizyka", "Matematyka", "Informatyka", "Technika",
			"Wychowanie Fizyczne", "Edukacja dla bezpieczeństwa", "Zajęcia z Wychowawcą", "Religia", "Wychowanie do życia w rodzinie"
		];

		$subject = $this->faker->unique()->randomElement($subjects);

		return [
			"name" => $subject . " (Debug)",
			"shortcut" => substr($subject, 0, 3) . $this->faker->unique()->randomLetter(),
		];
	}
}

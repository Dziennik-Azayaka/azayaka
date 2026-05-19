<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\ChildrenRegistry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompulsoryEducationFulfillment>
 */
class CompulsoryEducationFulfillmentFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		$kindergartenNumber = rand(1, 10);
		$schoolNumber = rand(1, 10);
		return [
			"child_id" => Child::factory(),
			"school_year" => rand(2020, 2024),
			"control_date" => $this->faker->date(),
			"kindergarten_info" => rand(1, 6) > 2 ? "Przedszkole nr $kindergartenNumber" : null,
			"postponement_info" => rand(1, 6) > 5 ? "Edukację odroczono." : null,
			"school_info" => "W szkole nr $schoolNumber",
			"out_of_school_info" => rand(1, 6) > 5 ? "Edukacja domowa" : null,
			"level" => rand(1, 8)
		];
	}
}

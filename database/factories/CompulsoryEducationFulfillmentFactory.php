<?php

namespace Database\Factories;

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
		return [
			"student_id" => rand(1, 10),
			"children_registry_id" => 1,
			"school_year" => rand(2020, 2024),
			"control_date" => $this->faker->date(),
			"fulfillment_form" => "w szkole w której obwodzie mieszka uczeń",
			"level" => rand(1, 8),
			"relationship" => "podlega obowiązku szkolnemu w szkole podstawowej"
		];
	}
}

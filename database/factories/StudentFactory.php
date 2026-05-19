<?php

namespace Database\Factories;

use App\Models\Person;
use App\Models\ResidenceAddress;
use App\Models\StudentRegistry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		$leftSchool = rand(1, 10) > 8;

		return [
			"person_id" => Person::factory()->create(),
			"admission_date" => "2025-09-01",
			"leave_date" => $leftSchool ? null : "2025-12-31",
			"leave_reason" => $leftSchool ? "Przeniesienie do innej placówki edukacyjnej." : null,
			"student_registry_id" => StudentRegistry::factory(),
		];
	}
}

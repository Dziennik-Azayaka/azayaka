<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Gradebook;
use App\Models\Lesson;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
	public function configure(): static
	{
		return $this->afterCreating(function (Lesson $lesson) {
			$lesson->gradebooks()->attach(Gradebook::factory()->create());
		});
	}

	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		$startHour = rand(7, 16);
		$endHour = $startHour + 1;
		return [
			"number" => $this->faker->numberBetween(1, 100),
			"primary_teacher_id" => Employee::factory(),
			"subject_id" => Subject::factory(),
			"topic" => $this->faker->sentence(),
			"date" => $this->faker->date(),
			"start_time" => "$startHour:30",
			"end_time" => "$endHour:15",
		];
	}
}

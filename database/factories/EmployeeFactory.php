<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		$firstName = $this->faker->firstName;
		$lastName = $this->faker->lastName;
		$shortcut = substr($firstName, 0, 1) . substr($lastName, 0, 2);
		$shortcut_count = Employee::whereLike("shortcut", "$shortcut%")->count();
		if ($shortcut_count > 0) {
			$shortcut = substr($firstName, 0, 1) . substr($lastName, 0, 2) . $shortcut_count;
		}
		return [
			"first_name" => $firstName,
			"last_name" => $lastName,
			"shortcut" => $shortcut,
			"is_admin" => rand(0, 1) == 0,
			"is_headmaster" => rand(0, 1) == 0,
			"is_secretary" => rand(0, 1) == 0,
			"is_teacher" => rand(0, 1) == 0,
        ];
    }
}

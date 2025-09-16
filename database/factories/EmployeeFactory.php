<?php

namespace Database\Factories;

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
        return [
			"first_name" => $firstName,
			"last_name" => $lastName,
			"shortcut" => substr($firstName, 0, 1) . substr($lastName, 0, 1)
        ];
    }
}

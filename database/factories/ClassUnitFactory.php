<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassUnit>
 */
class ClassUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "school_unit_id" => rand(1, 5),
			"alias" => $this->faker->unique()->word(),
			"mark" => $this->faker->unique()->randomLetter(),
			"starting_school_year" => $this->faker->year(),
			"teaching_cycle_length" => $this->faker->numberBetween(2, 8),
        ];
    }
}

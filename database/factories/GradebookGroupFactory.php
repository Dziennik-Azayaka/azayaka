<?php

namespace Database\Factories;

use App\Models\Gradebook;
use App\Models\GradebookGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradebookGroup>
 */
class GradebookGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => $this->faker->unique()->word(),
			"shortcut" => $this->faker->randomLetter() . $this->faker->randomLetter(),
			"gradebook_id" => Gradebook::factory()
        ];
    }
}

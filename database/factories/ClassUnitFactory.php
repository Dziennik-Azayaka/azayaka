<?php

namespace Database\Factories;

use App\Models\ClassificationPeriod;
use App\Models\SchoolUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;

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
            "school_unit_id" => SchoolUnit::factory(),
            "alias" => $this->faker->unique()->word(),
            "mark" => $this->faker->randomLetter(),
            "starting_classification_period_id" => ClassificationPeriod::factory(),
            "teaching_cycle_length" => $this->faker->numberBetween(2, 8),
        ];
    }
}

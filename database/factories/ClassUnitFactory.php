<?php

namespace Database\Factories;

use App\Models\ClassificationPeriod;
use App\Models\SchoolUnit;
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
        $unit = SchoolUnit::factory()->create();
        $start = now()->startOfYear();
        $end = (clone $start)->endOfYear();
        $period = ClassificationPeriod::create([
            "school_unit_id" => $unit->id,
            "school_year" => $start->year,
            "period_number" => 1,
            "period_start" => $start,
            "period_end" => $end,
        ]);

        return [
            "school_unit_id" => $unit->id,
            "alias" => $this->faker->unique()->word(),
            "mark" => $this->faker->unique()->randomLetter(),
            "starting_classification_period_id" => $period->id,
            "teaching_cycle_length" => $this->faker->numberBetween(2, 8),
        ];
    }
}

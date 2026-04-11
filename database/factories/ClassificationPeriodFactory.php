<?php

namespace Database\Factories;

use App\Models\ClassificationPeriod;
use App\Models\SchoolUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassificationPeriod>
 */
class ClassificationPeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		$start = now()->startOfYear();
		$end = (clone $start)->endOfYear();
        return [
			"school_unit_id" => SchoolUnit::factory(),
			"school_year" => $start->year,
			"period_number" => 1,
			"period_start" => $start,
			"period_end" => $end,
        ];
    }
}

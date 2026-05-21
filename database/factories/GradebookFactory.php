<?php

namespace Database\Factories;

use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\Gradebook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gradebook>
 */
class GradebookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
			"classification_period_id" => ClassificationPeriod::factory(),
			"class_unit_id" => ClassUnit::factory()
        ];
    }
}

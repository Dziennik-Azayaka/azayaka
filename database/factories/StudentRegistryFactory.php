<?php

namespace Database\Factories;

use App\Models\SchoolUnit;
use App\Models\StudentRegistry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentRegistryFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
    protected $model = StudentRegistry::class;

    public function definition(): array
    {
        return [
            "school_unit_id" => SchoolUnit::factory()->create()->id,
        ];
    }
}

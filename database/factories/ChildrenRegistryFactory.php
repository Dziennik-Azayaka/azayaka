<?php

namespace Database\Factories;

use App\Models\ChildrenRegistry;
use App\Models\SchoolUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChildrenRegistry>
 */
class ChildrenRegistryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
			"school_unit_id" => SchoolUnit::factory()->create()
		];
    }
}

<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\ChildrenRegistry;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Child>
 */
class ChildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "person_id" => Person::factory()->create(),
			"children_registry_id" => ChildrenRegistry::factory()
        ];
    }
}

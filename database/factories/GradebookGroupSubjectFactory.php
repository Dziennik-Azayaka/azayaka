<?php

namespace Database\Factories;

use App\Enums\GradebookSubjectType;
use App\Models\GradebookGroup;
use App\Models\GradebookGroupSubject;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradebookGroupSubject>
 */
class GradebookGroupSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "gradebook_group_id" => GradebookGroup::factory(),
			"subject_id" => Subject::factory(),
			"description" => $this->faker->randomElement(GradebookSubjectType::cases())
        ];
    }
}

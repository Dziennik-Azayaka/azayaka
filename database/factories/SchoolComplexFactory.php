<?php

namespace Database\Factories;

use App\Enums\SchoolType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolComplex>
 */
class SchoolComplexFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => "Zespół szkół ponadpodstawowych nr. " . rand(1, 100),
			"type" => SchoolType::ZESPOL_SZKOL_I_PLACOWEK_OSWIATOWYCH
        ];
    }
}

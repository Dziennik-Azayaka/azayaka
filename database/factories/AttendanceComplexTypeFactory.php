<?php

namespace Database\Factories;

use App\Enums\AttendancePrimitiveType;
use App\Models\AttendanceComplexType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceComplexType>
 */
class AttendanceComplexTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		$sampleNames = ["nieobecność z powodów szkolnych", "dojazd PKS", "nieobecność ucznia z Ukrainy"];
        return [
            "name" => $sampleNames[array_rand($sampleNames)],
			"shortcut" => $this->faker->randomLetter(),
			"maps_to_primitive_type" => $this->faker->randomElement(AttendancePrimitiveType::cases()),
			"active" => true
        ];
    }
}

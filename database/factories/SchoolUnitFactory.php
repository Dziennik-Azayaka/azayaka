<?php

namespace Database\Factories;

use App\Enums\SchoolType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolUnit>
 */
class SchoolUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		$schoolTypeNum = rand(1, 3);
		if ($schoolTypeNum == 1) {
			$schoolType = SchoolType::LICEUM_OGOLNOKSZTALCACE;
			$schoolTypeName = "Liceum Ogólnokształcące";
		} else if ($schoolTypeNum == 2) {
			$schoolType = SchoolType::TECHNIKUM;
			$schoolTypeName = "Technikum";
		} else {
			$schoolType = SchoolType::BRANZOWA_SZKOLA_I_STOPNIA;
			$schoolTypeName = "Szkoła Branżowa I stopnia";
		}

        return [
            "name" => $schoolTypeName . " nr. " . rand(1, 100),
			"type" => $schoolType,
			"studentCategory" => "childrenAndAdults",
			"municipality" => "Łódź",
			"voivodeship" => "Łódzkie",
			"district" => rand(0, 1) == 0 ? "Bałuty" : null,
			"school_complex_id" => 1
        ];
    }
}

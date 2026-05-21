<?php

namespace Database\Factories;

use App\Models\Person;
use App\Models\ResidenceAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		$hasPolishCitizenship = rand(1, 10) > 3;
        return [
                        "first_name" => $this->faker->firstName,
                        "last_name" => $this->faker->lastName,
                        "second_name" => rand(1, 10) > 7 ? $this->faker->firstName : null,
                        "pesel" => $hasPolishCitizenship ? $this->faker->unique()->numerify("###########") : null,
                        "alternate_identity_document" => $hasPolishCitizenship ? null : "ALT-" . $this->faker->unique()->numerify("#######"),
                        "birthdate" => $this->faker->date(),
                        "birthplace" => $this->faker->city(),
                        "gender" => rand(1, 2) == 1 ? "male" : "female",
                        "last_modified_by" => "System (Bezpośredni wpis do bazy danych)",
                        "school_unit_id" => \App\Models\SchoolUnit::factory(),
                        "residence_address_id" => ResidenceAddress::factory()->create()
        ];
    }
}

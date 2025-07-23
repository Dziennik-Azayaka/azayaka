<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountLog>
 */
class AccountLogFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		$eventTypes = [
			"failed_login_attempt",
			"successful_login_attempt",
			"logout",
			"logged_out_by_another_device",
			"credentials_changed"
		];

		return [
			"user_agent" => $this->faker->userAgent(),
			"ip" => rand(0, 10) > 3 ? $this->faker->ipv4() : $this->faker->ipv6(),
			"user_id" => 1,
			"event_type" =>	$eventTypes[array_rand($eventTypes)]
        ];
	}
}

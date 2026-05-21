<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Guardian;
use App\Models\Person;
use App\Models\SchoolUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GuardianControllerTest extends TestCase
{
	use RefreshDatabase;

	private function createPerson(): Person
	{
		$schoolUnit = SchoolUnit::factory()->create();
		return Person::factory()->create([
			"school_unit_id" => $schoolUnit->id
		]);
	}

	public function test_can_create_guardian(): void
	{
		$this->actingUser();
		$person = $this->createPerson();
		$response = $this->post("/api/people/$person->id/guardians", [
			"firstName" => "John",
			"lastName" => "Doe",
			"email" => null,
			"phoneNumber" => "1234567890",
			"residenceAddressCountry" => "AQ"
		]);
		$response->assertCreated();
		$this->assertDatabaseHas("guardians", [
			"person_id" => $person->id,
			"first_name" => "John",
			"last_name" => "Doe",
			"email" => null,
			"phone_number" => "1234567890"
		]);
		$this->assertDatabaseHas("residence_addresses", [
			"country" => "AQ"
		]);
	}

	public function test_can_update_guardian(): void
	{
		$this->actingUser();
		$guardian = Guardian::factory()->create();
		$response = $this->put("/api/guardians/$guardian->id", [
			"firstName" => "Jane",
			"lastName" => "Doe",
			"email" => "test@example.com",
			"phoneNumber" => "987654321",
			"residenceAddressCountry" => "AQ"
		]);
		$response->assertOk();
		$this->assertDatabaseHas("guardians", [
			"id" => $guardian->id,
			"first_name" => "Jane",
			"last_name" => "Doe",
			"email" => "test@example.com",
			"phone_number" => "987654321"
		]);
		$this->assertDatabaseHas("residence_addresses", [
			"country" => "AQ"
		]);
	}

	public function test_can_delete_guardian(): void
	{
		$this->actingUser();
		$guardian = Guardian::factory()->create();
		$response = $this->delete("/api/guardians/$guardian->id");
		$response->assertOk();
		$this->assertDatabaseMissing("guardians", [
			"id" => $guardian->id
		]);
	}
}

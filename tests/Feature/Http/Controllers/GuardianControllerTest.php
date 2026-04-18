<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class GuardianControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_list_guardians(): void
	{
		$this->actingUser();
		$student = Student::factory()->create();
		Guardian::factory()->count(5)->create([
			"student_id" => $student->id
		]);
		$response = $this->get("/api/students/$student->id/guardians");
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonCount(5);
		$response->assertJsonStructure([
			"*" => [
				"id",
				"firstName",
				"lastName",
				"email",
				"phoneNumber",
			]
		]);
	}

	public function test_can_create_guardian(): void
	{
		$this->actingUser();
		$student = Student::factory()->create();
		$response = $this->post("/api/students/$student->id/guardians", [
			"firstName" => "John",
			"lastName" => "Doe",
			"email" => null,
			"phoneNumber" => "1234567890",
			"residenceAddressCountry" => "AQ"
		]);
		$response->assertCreated();
		$this->assertDatabaseHas("guardians", [
			"student_id" => $student->id,
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

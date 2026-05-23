<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\ClassUnit;
use App\Models\Gradebook;
use App\Models\GradebookStudents;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\SchoolUnit;
use App\Models\Student;
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
		$this->actingAdminUser();
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
		$this->actingAdminUser();
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
		$this->actingAdminUser();
		$guardian = Guardian::factory()->create();
		$response = $this->delete("/api/guardians/$guardian->id");
		$response->assertOk();
		$this->assertDatabaseMissing("guardians", [
			"id" => $guardian->id
		]);
	}

	public function test_can_generate_guardian_access_for_student()
	{
		$this->actingAdminUser();
		$guardian = Guardian::factory()->create();
		$student = Student::factory()->create();

		$response = $this->get("/api/guardians/$guardian->id/students/$student->id/generateAccess");

		$response->assertStatus(200)
			->assertJsonStructure([
				"success",
				"words"
			]);

		$this->assertDatabaseHas("account_accesses", [
			"guardian_id" => $guardian->id,
			"student_id" => $student->id,
		]);
	}

	public function test_can_list_active_guardians_with_accesses()
	{
		$this->actingAdminUser();
		$person = Person::factory()->create([
			"first_name" => "Jan",
			"last_name" => "Nowak",
		]);

		$guardian = Guardian::factory()->create([
			"person_id" => $person->id
		]);

		$student = Student::factory()->create(["person_id" => $person->id]);

		$access = AccountAccess::factory()->create([
			"guardian_id" => $guardian->id,
			"student_id" => $student->id,
			"words" => "a,b,c"
		]);

		$response = $this->get("/api/guardians/accesses");

		$response->assertStatus(200)
			->assertJsonFragment([
				"guardianId" => $guardian->id,
				"guardianFirstName" => $guardian->first_name,
				"guardianLastName" => $guardian->last_name,
			]);

		$data = $response->json()[0];
		$this->assertEquals("Jan", $data["students"][0]["firstName"]);
		$this->assertEquals("a,b,c", $data["accessWords"]["words"]);
	}

	public function test_can_filter_guardian_accesses_by_class_unit_id()
	{
		$this->actingAdminUser();
		$student = Student::factory()->create();
		$guardian = Guardian::factory()->create(["person_id" => $student->person_id]);

		$classUnit = ClassUnit::factory()->create();
		$gradebook = Gradebook::factory()->create(["class_unit_id" => $classUnit->id]);

		$pivotEntry = new GradebookStudents();
		$pivotEntry->student_id = $student->id;
		$pivotEntry->gradebook_id = $gradebook->id;
		$pivotEntry->position = 1;
		$pivotEntry->date_from = "2025-09-01";
		$pivotEntry->date_to = null;
		$pivotEntry->save();

		$response = $this->get("/api/guardians/accesses?classUnitId=$classUnit->id");
		$response->assertStatus(200)->assertJsonCount(1);

		$emptyResponse = $this->get("/api/guardians/accesses?classUnitId=9999");
		$emptyResponse->assertStatus(200)->assertJsonCount(0);
	}
}

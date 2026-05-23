<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Person;
use App\Models\SchoolUnit;
use App\Models\Student;
use App\Models\StudentRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StudentControllerTest extends TestCase
{
	use RefreshDatabase;

	private function createStudentRegistry(): StudentRegistry
	{
		$schoolUnit = SchoolUnit::factory()->create();
		return $schoolUnit->studentRegistry()->create();
	}

	public function test_can_list_students(): void
	{
		$this->actingAdminUser();
		$registry = $this->createStudentRegistry();
		Student::factory(5)->recycle($registry)->create();
		$response = $this->get("/api/studentRegistry/$registry->id");
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonCount(5);
	}

	public function test_can_create_student(): void
	{
		$this->actingAdminUser();
		$registry = $this->createStudentRegistry();
		$person = Person::factory()->create([
			"school_unit_id" => $registry->schoolUnit->id
		]);
		$response = $this->post("/api/studentRegistry/$registry->id", [
			"personId" => $person->id,
			"admissionDate" => "2025-09-01"
		]);
		$response->assertCreated();
		$this->assertDatabaseHas("students", [
			"person_id" => $person->id,
			"student_registry_id" => $registry->id,
			"admission_date" => "2025-09-01"
		]);
	}

	public function test_cannot_create_student_without_valid_person_id(): void
	{
		$this->actingAdminUser();
		$registry = $this->createStudentRegistry();
		$response = $this->post("/api/studentRegistry/$registry->id", [
			"personId" => 99999,
			"admissionDate" => "2025-09-01"
		]);
		$response->assertUnprocessable();
	}

	public function test_cannot_create_student_without_admission_date(): void
	{
		$this->actingAdminUser();
		$registry = $this->createStudentRegistry();
		$person = Person::factory()->create([
			"school_unit_id" => $registry->schoolUnit->id
		]);
		$response = $this->post("/api/studentRegistry/$registry->id", [
			"personId" => $person->id
		]);
		$response->assertUnprocessable();
	}

	public function test_can_update_student(): void
	{
		$this->actingAdminUser();
		$student = Student::factory()->create();
		$response = $this->put("/api/students/$student->id", [
			"admissionDate" => "2024-09-01",
			"leaveDate" => "2025-06-30",
			"leaveReason" => "Przeniesienie do innej placówki edukacyjnej."
		]);
		$response->assertOk();
		$this->assertDatabaseHas("students", [
			"id" => $student->id,
			"admission_date" => "2024-09-01",
			"leave_date" => "2025-06-30",
			"leave_reason" => "Przeniesienie do innej placówki edukacyjnej."
		]);
	}

	public function test_can_delete_student(): void
	{
		$this->actingAdminUser();
		$student = Student::factory()->create();
		$response = $this->delete("/api/students/$student->id");
		$response->assertOk();
		$student = $student->refresh();
		$this->assertTrue($student->trashed());
	}

	public function test_can_get_own_info_as_student(): void
	{
		$this->actingStudent();
		$response = $this->get("/api/students/me");
		$response->assertOk();
		$response->assertJsonStructure([
			"id",
			"firstName",
			"secondName",
			"lastName",
			"residenceAddress" => [
				"id",
				"country",
				"commune",
				"town",
				"postalCode",
				"street",
				"houseNumber",
				"flatNumber"
			],
			"gender"
		]);
	}
}

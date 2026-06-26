<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\Gradebook;
use App\Models\GradebookStudents;
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
			"admissionDate" => "2025-09-01",
			"studentRegistryNumber" => rand(1, 9999)
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
			"admissionDate" => "2025-09-01",
			"studentRegistryNumber" => rand(1, 9999)
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

	public function test_can_generate_student_accesses_document(): void
	{
		$this->actingAdminUser();
		$student = Student::factory()->create();
		AccountAccess::factory()->create([
			"student_id" => $student->id,
			"words" => "a,b,c",
		]);

		$response = $this->post("/api/students/accesses/document", [
			"ids" => [$student->id],
		]);

		$response->assertOk();
		$response->assertHeader("Content-Type", "application/pdf");
		$this->assertMatchesRegularExpression(
			"/\.pdf/",
			$response->headers->get("Content-Disposition")
		);
	}

	public function test_cannot_generate_student_accesses_document_without_words(): void
	{
		$this->actingAdminUser();
		$student = Student::factory()->create();

		$response = $this->post("/api/students/accesses/document", [
			"ids" => [$student->id],
		]);

		$response->assertStatus(422);
		$response->assertJsonFragment([
			"ENTITY_HAS_NO_ACCESS_WORDS",
		]);
	}

	public function test_generate_student_accesses_document_validates_ids_presence(): void
	{
		$this->actingAdminUser();

		$response = $this->post("/api/students/accesses/document", []);

		$response->assertUnprocessable();
	}

	public function test_generating_access_fails_when_student_not_in_active_class_unit(): void
	{
		$this->actingAdminUser();
		$student = Student::factory()->create(["leave_date" => null]);

		$response = $this->get("/api/students/$student->id/generateAccess");

		$response->assertStatus(422);
		$response->assertJsonFragment(["STUDENT_NOT_IN_ACTIVE_CLASS_UNIT"]);
	}

	public function test_generating_access_succeeds_when_student_in_active_class_unit(): void
	{
		$this->actingAdminUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classificationPeriod = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2025,
			"period_number" => 1,
			"period_start" => now()->subMonth()->toDateString(),
			"period_end" => now()->addMonth()->toDateString()
		]);
		$classUnit = ClassUnit::factory()->create([
			"school_unit_id" => $schoolUnit->id,
			"starting_classification_period_id" => $classificationPeriod->id
		]);
		$classUnit->periods()->attach($classificationPeriod->id, ["level" => 1]);
		$gradebook = Gradebook::factory()->create([
			"classification_period_id" => $classificationPeriod->id,
			"class_unit_id" => $classUnit->id
		]);
		$studentRegistry = StudentRegistry::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$student = Student::factory()->create([
			"student_registry_id" => $studentRegistry->id,
			"leave_date" => null
		]);
		GradebookStudents::insert([
			"gradebook_id" => $gradebook->id,
			"student_id" => $student->id,
			"position" => 1,
			"date_from" => now()->toDateString(),
			"created_at" => now(),
			"updated_at" => now()
		]);

		$response = $this->get("/api/students/$student->id/generateAccess");

		$response->assertOk();
		$response->assertJson(["success" => true]);
		$response->assertJsonStructure(["words"]);
	}

	public function test_list_accesses_returns_only_active_students(): void
	{
		$this->actingAdminUser();
		$activeStudent = Student::factory()->create(["leave_date" => null]);
		$inactiveStudent = Student::factory()->create(["leave_date" => "2024-12-31"]);

		AccountAccess::factory()->create([
			"student_id" => $activeStudent->id,
			"words" => "a,b,c",
		]);
		AccountAccess::factory()->create([
			"student_id" => $inactiveStudent->id,
			"words" => "d,e,f",
		]);

		$response = $this->get("/api/students/accesses");

		$response->assertOk();
		$data = $response->json();
		$studentIds = array_column($data, "studentId");

		$this->assertContains($activeStudent->id, $studentIds);
		$this->assertNotContains($inactiveStudent->id, $studentIds);
	}
}

<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\Gradebook;
use App\Models\GradebookStudents;
use App\Models\SchoolUnit;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class GradebookControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_list_gradebooks(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$gradebook = Gradebook::factory()->create(["class_unit_id" => $classUnit->id]);
		$response = $this->get("/api/schoolUnits/$schoolUnit->id/gradebooks");
		$response->assertOk();
		$response->assertJsonCount(1);
		$response->assertJsonFragment([
			"id" => $gradebook->id,
			"schoolYear" => $gradebook->startingClassificationPeriod->school_year,
			"level" => $gradebook->level
		]);
	}

	public function test_can_list_gradebooks_by_class_unit_and_school_year(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classUnit1 = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$classUnit2 = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$classificationPeriod1 = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2025,
			"period_number" => 1,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31"
		]);
		$classificationPeriod2 = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2024,
			"period_number" => 1,
			"period_start" => "2024-09-01",
			"period_end" => "2024-12-31"
		]);

		$gradebookCorrect = Gradebook::factory()->create([
			"class_unit_id" => $classUnit1->id,
			"classification_period_id" => $classificationPeriod1->id
		]);
		$gradebookIncorrectClass = Gradebook::factory()->create([
			"class_unit_id" => $classUnit2->id,
			"classification_period_id" => $classificationPeriod1->id
		]);
		$gradebookIncorrectPeriod = Gradebook::factory()->create([
			"class_unit_id" => $classUnit1->id,
			"classification_period_id" => $classificationPeriod2->id
		]);

		$response = $this->get("/api/schoolUnits/$schoolUnit->id/gradebooks?classUnitId=$classUnit1->id&schoolYear=2025");
		$response->assertOk();
		$response->assertJsonCount(1);
		$response->assertJsonFragment(["id" => $gradebookCorrect->id]);
		$response->assertJsonMissing(["id" => $gradebookIncorrectClass->id]);
		$response->assertJsonMissing(["id" => $gradebookIncorrectPeriod->id]);
	}

	public function test_can_create_gradebook(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$classificationPeriod = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2025,
			"period_number" => 1,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31"
		]);
		$response = $this->post("/api/gradebooks", [
			"classUnitId" => $classUnit->id,
			"classificationPeriodId" => $classificationPeriod->id,
		]);
		$response->assertCreated();
		$this->assertDatabaseHas("gradebooks", [
			"class_unit_id" => $classUnit->id,
			"classification_period_id" => $classificationPeriod->id,
		]);
	}

	public function test_creating_gradebook_when_one_already_exists_for_classification_period_fails(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$classificationPeriod = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2025,
			"period_number" => 1,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31"
		]);
		Gradebook::factory()->create([
			"class_unit_id" => $classUnit->id,
			"classification_period_id" => $classificationPeriod->id
		]);
		$response = $this->post("/api/gradebooks", [
			"classUnitId" => $classUnit->id,
			"classificationPeriodId" => $classificationPeriod->id,
		]);
		$response->assertConflict();
	}

	public function test_can_list_students_in_gradebook(): void
	{
		$this->actingUser();
		$gradebook = Gradebook::factory()->create();
		$student = Student::factory()->create();

		GradebookStudents::insert([
			"gradebook_id" => $gradebook->id,
			"student_id" => $student->id,
			"position" => 5,
			"date_from" => now(),
			"created_at" => now(),
			"updated_at" => now(),
		]);

		$response = $this->get("/api/gradebooks/$gradebook->id/students");

		$response->assertOk();
		$response->assertJsonCount(1);
		$response->assertJsonFragment([
			"studentId" => $student->id,
			"studentName" => $student->person->first_name,
			"studentSecondName" => $student->person->second_name,
			"studentLastName" => $student->person->last_name,
			"position" => 5
		]);
	}

	public function test_can_attach_students_to_gradebook(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classificationPeriod = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2025,
			"period_number" => 1,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31"
		]);
		$gradebook = Gradebook::factory()->create([
			"classification_period_id" => $classificationPeriod->id
		]);

		$student1 = Student::factory()->create();
		$student2 = Student::factory()->create();

		$response = $this->post("/api/gradebooks/$gradebook->id/students", [
			"studentIds" => [$student1->id, $student2->id],
			"positions" => [1, 2]
		]);

		$response->assertOk();
		$response->assertJson(["success" => true]);

		$this->assertDatabaseHas("gradebooks_students", [
			"gradebook_id" => $gradebook->id,
			"student_id" => $student1->id,
			"position" => 1
		]);
		$this->assertDatabaseHas("gradebooks_students", [
			"gradebook_id" => $gradebook->id,
			"student_id" => $student2->id,
			"position" => 2
		]);
	}

	public function test_attaching_students_fails_if_arrays_length_mismatch(): void
	{
		$this->actingUser();
		$gradebook = Gradebook::factory()->create();
		$student = Student::factory()->create();

		$response = $this->post("/api/gradebooks/$gradebook->id/students", [
			"studentIds" => [$student->id],
			"positions" => [1, 2]
		]);

		$response->assertStatus(422);
		$response->assertJsonFragment([
			"errors" => ["NUMBER_OF_STUDENTS_AND_POSITIONS_DO_NOT_MATCH"]
		]);
	}

	public function test_attaching_students_fails_on_validation_errors(): void
	{
		$this->actingUser();
		$gradebook = Gradebook::factory()->create();
		$student = Student::factory()->create();

		$response = $this->post("/api/gradebooks/$gradebook->id/students", [
			"studentIds" => [$student->id, $student->id],
			"positions" => [1, 2]
		]);

		$response->assertStatus(422);
		$response->assertSee("HAS_A_DUPLICATE_VALUE");
	}
}

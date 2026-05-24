<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\GradebookSubjectType;
use App\Models\Employee;
use App\Models\Gradebook;
use App\Models\GradebookGroup;
use App\Models\GradebookGroupSubject;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class GradebookGroupControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_list_groups_for_gradebook(): void
	{
		$this->actingAdminUser();
		$gradebook = Gradebook::factory()->create();
		GradebookGroup::factory()->create(["gradebook_id" => $gradebook->id]);

		$response = $this->get("/api/gradebooks/$gradebook->id/groups");

		$response->assertStatus(200);
		$response->assertJsonCount(1);
	}

	public function test_can_create_group(): void
	{
		$this->actingAdminUser();
		$gradebook = Gradebook::factory()->create();
		$student = Student::factory()->create();

		$response = $this->post("/api/gradebooks/$gradebook->id/groups", [
			"name" => "Angielski Rozszerzony",
			"shortcut" => "AngRoz",
			"studentIds" => [$student->id],
		]);

		$response->assertStatus(200)->assertJson(["success" => true]);

		$this->assertDatabaseHas("gradebook_groups", [
			"gradebook_id" => $gradebook->id,
			"name" => "Angielski Rozszerzony",
			"shortcut" => "AngRoz",
		]);

		$group = GradebookGroup::where("shortcut", "AngRoz")->first();

		$this->assertDatabaseHas("gradebook_group_student", [
			"gradebook_group_id" => $group->id,
			"student_id" => $student->id,
		]);
	}

	public function test_creating_group_validation_fails_without_required_fields(): void
	{
		$this->actingAdminUser();
		$gradebook = Gradebook::factory()->create();

		$response = $this->post("/api/gradebooks/$gradebook->id/groups", [
			"name" => "Matematyka Eksperymentalna"
		]);

		$response->assertStatus(422);
	}

	public function test_can_update_group(): void
	{
		$this->actingAdminUser();
		$gradebook = Gradebook::factory()->create();
		$group = GradebookGroup::factory()->create([
			"gradebook_id" => $gradebook->id,
			"name" => "Angielski Podstawowy",
			"shortcut" => "AngPod",
		]);

		$student = Student::factory()->create();

		$response = $this->put("/api/gradebooks/groups/$group->id", [
			"name" => "Angielski Rozszerzony",
			"shortcut" => "AngRoz",
			"studentIds" => [$student->id],
		]);

		$response->assertStatus(200)->assertJson(["success" => true]);

		$this->assertDatabaseHas("gradebook_groups", [
			"id" => $group->id,
			"name" => "Angielski Rozszerzony",
			"shortcut" => "AngRoz",
		]);

		$this->assertDatabaseMissing("gradebook_groups", [
			"id" => $group->id,
			"name" => "Angielski Podstawowy",
			"shortcut" => "AngPod"
		]);

		$this->assertDatabaseHas("gradebook_group_student", [
			"gradebook_group_id" => $group->id,
			"student_id" => $student->id,
		]);
	}

	public function test_can_add_subject_to_group(): void
	{
		$this->actingAdminUser();
		$group = GradebookGroup::factory()->create();
		$subject = Subject::factory()->create();

		$response = $this->post("/api/gradebooks/groups/$group->id/subjects", [
			"subject_id" => $subject->id,
			"description" => GradebookSubjectType::BILINGUAL_REGULAR_SUBJECT->value,
		]);

		$response->assertStatus(200)->assertJson(["success" => true]);

		$this->assertDatabaseHas("gradebook_group_subjects", [
			"gradebook_group_id" => $group->id,
			"subject_id" => $subject->id,
			"description" => GradebookSubjectType::BILINGUAL_REGULAR_SUBJECT,
		]);
	}

	public function test_cannot_add_subject_if_already_assigned(): void
	{
		$this->actingAdminUser();
		$group = GradebookGroup::factory()->create();
		$subject = Subject::factory()->create();

		GradebookGroupSubject::factory()->create([
			"gradebook_group_id" => $group->id,
			"subject_id" => $subject->id,
			"description" => GradebookSubjectType::BILINGUAL_REGULAR_SUBJECT,
		]);

		$response = $this->post("/api/gradebooks/groups/$group->id/subjects", [
			"subject_id" => $subject->id,
			"description" => GradebookSubjectType::BILINGUAL_REGULAR_SUBJECT->value,
		]);

		$response->assertStatus(409)
			->assertJson([
				"success" => false,
				"errors" => ["SUBJECT_ALREADY_ASSIGNED_TO_GROUP"]
			]);
	}

	public function test_can_update_subject(): void
	{
		$this->actingAdminUser();
		$group = GradebookGroup::factory()->create();
		$subject = Subject::factory()->create();

		$groupSubject = GradebookGroupSubject::factory()->create([
			"gradebook_group_id" => $group->id,
			"subject_id" => $subject->id,
			"description" => GradebookSubjectType::BILINGUAL_REGULAR_SUBJECT,
		]);

		$response = $this->put("/api/gradebooks/groups/$group->id/subjects/$groupSubject->id", [
			"description" => GradebookSubjectType::BILINGUAL_LINGUISTIC_SUBJECT->value,
		]);

		$response->assertStatus(200)->assertJson(["success" => true]);

		$this->assertDatabaseHas("gradebook_group_subjects", [
			"id" => $groupSubject->id,
			"description" => GradebookSubjectType::BILINGUAL_LINGUISTIC_SUBJECT,
		]);
	}

	public function test_can_remove_subject(): void
	{
		$this->actingAdminUser();
		$group = GradebookGroup::factory()->create();
		$subject = Subject::factory()->create();

		$groupSubject = GradebookGroupSubject::factory()->create([
			"gradebook_group_id" => $group->id,
			"subject_id" => $subject->id,
		]);

		$response = $this->delete("/api/gradebooks/groups/$group->id/subjects/$groupSubject->id");

		$response->assertStatus(200)->assertJson(["success" => true]);

		$this->assertDatabaseMissing("gradebook_group_subjects", [
			"id" => $groupSubject->id,
		]);
	}

	public function test_can_update_teachers(): void
	{
		$this->actingAdminUser();
		$group = GradebookGroup::factory()->create();
		$subject = Subject::factory()->create();

		$groupSubject = GradebookGroupSubject::factory()->create([
			"gradebook_group_id" => $group->id,
			"subject_id" => $subject->id,
		]);

		$employee1 = Employee::factory()->create();
		$employee2 = Employee::factory()->create();

		$response = $this->put("/api/gradebooks/groups/$group->id/subjects/$groupSubject->id/teachers", [
			"teachers" => [$employee1->id, $employee2->id],
		]);

		$response->assertStatus(200)->assertJson(["success" => true]);

		$this->assertDatabaseHas("employee_gradebook_group_subject", [
			"gradebook_group_subject_id" => $groupSubject->id,
			"employee_id" => $employee1->id,
		]);

		$this->assertDatabaseHas("employee_gradebook_group_subject", [
			"gradebook_group_subject_id" => $groupSubject->id,
			"employee_id" => $employee2->id,
		]);
	}
}

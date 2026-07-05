<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Employee;
use App\Models\Gradebook;
use App\Models\GradebookGroup;
use App\Models\Lesson;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_list_lessons_and_filter_them(): void
	{
		$this->actingAdminUser();
		$gradebook = Gradebook::factory()->create();
		$subject = Subject::factory()->create();
		$teacher = Employee::factory()->create();

		$matchingLesson = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"topic" => "Wielowątkowe programowanie obiektowe w języku Scratch",
			"date" => "2026-05-24",
			"completed" => true
		]);
		$matchingLesson->gradebooks()->sync([$gradebook->id]);

		Lesson::factory()->create([
			"date" => "2026-05-25",
		]);

		$response = $this->getJson(
			"/api/gradebooks/$gradebook->id/lessons?dateFrom=2026-05-24&dateTo=2026-05-24&completed=1&topic=Wielowątkowe programowanie obiektowe w języku Scratch"
		);

		$response->assertStatus(200);
		$response->assertJsonCount(1);
		$response->assertJsonPath("0.id", $matchingLesson->id);
		$response->assertJsonPath("0.topic", "Wielowątkowe programowanie obiektowe w języku Scratch");
	}

	public function test_can_filter_lessons_by_assisting_teacher(): void
	{
		$this->actingAdminUser();
		$gradebook = Gradebook::factory()->create();
		$assistingTeacher = Employee::factory()->create();

		$matchingLesson = Lesson::factory()->create();
		$matchingLesson->gradebooks()->sync([$gradebook->id]);
		$matchingLesson->assistingTeachers()->attach($assistingTeacher);

		$nonMatchingLesson = Lesson::factory()->create();
		$nonMatchingLesson->gradebooks()->sync([$gradebook->id]);

		$response = $this->getJson(
			"/api/gradebooks/$gradebook->id/lessons?assistingTeacherId=$assistingTeacher->id"
		);

		$response->assertStatus(200);
		$response->assertJsonCount(1);
		$response->assertJsonPath("0.id", $matchingLesson->id);
	}

	public function test_can_create_a_lesson(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$subject = Subject::factory()->create();
		$assistingTeacher = Employee::factory()->create();
		$group = GradebookGroup::factory()->create();

		$payload = [
			"number" => 1,
			"gradebookIds" => [$gradebook->id],
			"subjectId" => $subject->id,
			"topic" => "Programowanie pojazdów autonomicznych w języku Scratch",
			"date" => "2026-05-24",
			"startTime" => "10:00",
			"endTime" => "11:30",
			"completed" => false,
			"assistingTeachers" => [$assistingTeacher->id],
			"groups" => [$group->id],
		];

		$response = $this->postJson("/api/lessons", $payload);

		$response->assertStatus(201)
			->assertJson(["success" => true]);

		$lessonId = $response->json("lessonId");

		$this->assertDatabaseHas("lessons", [
			"topic" => "Programowanie pojazdów autonomicznych w języku Scratch",
			"primary_teacher_id" => $employee->id,
		]);

		$this->assertDatabaseHas("lessons_gradebooks", [
			"lesson_id" => $lessonId,
			"gradebook_id" => $gradebook->id,
		]);

		$this->assertDatabaseHas("lessons_assisting_teachers", [
			"lesson_id" => $lessonId,
			"employee_id" => $assistingTeacher->id,
		]);

		$this->assertDatabaseHas("lessons_gradebook_group", [
			"lesson_id" => $lessonId,
			"gradebook_group_id" => $group->id,
		]);
	}

	public function test_can_update_a_lesson(): void
	{
		$this->actingAdminUser();
		$lesson = Lesson::factory()->create([
			"topic" => "Sieci neuronowe w Scratchu",
			"primary_teacher_id" => $this->actingAdminUser->employees->first()->id
		]);

		$newSubject = Subject::factory()->create();
		$newGroup = GradebookGroup::factory()->create();

		$payload = [
			"number" => 2,
			"gradebookIds" => $lesson->gradebooks->pluck("id")->toArray(),
			"subjectId" => $newSubject->id,
			"topic" => "Tworzenie dużych modeli językowych w języku Scratch",
			"date" => "2026-05-25",
			"startTime" => "12:00",
			"endTime" => "13:00",
			"completed" => false,
			"assistingTeachers" => [],
			"groups" => [$newGroup->id],
		];

		$response = $this->putJson("/api/lessons/$lesson->id", $payload);

		$response->assertStatus(200)
			->assertJson(["success" => true]);

		$this->assertDatabaseHas("lessons", [
			"id" => $lesson->id,
			"topic" => "Tworzenie dużych modeli językowych w języku Scratch",
			"subject_id" => $newSubject->id,
		]);

		$this->assertDatabaseHas("lessons_gradebook_group", [
			"lesson_id" => $lesson->id,
			"gradebook_group_id" => $newGroup->id,
		]);
	}

	public function test_can_mark_a_lesson_as_completed(): void
	{
		$this->actingAdminUser();
		$lesson = Lesson::factory()->create([
			"completed" => false,
			"primary_teacher_id" => $this->actingAdminUser->employees->first()->id
		]);

		$response = $this->patchJson("/api/lessons/$lesson->id/completed");

		$response->assertStatus(200)
			->assertJson(["success" => true]);

		$this->assertDatabaseHas("lessons", [
			"id" => $lesson->id,
			"completed" => true
		]);
	}

	public function test_student_can_see_their_lessons(): void
	{
		$this->actingStudent();
		$gradebook = Gradebook::factory()->create();
		$subject = Subject::factory()->create();
		$primaryTeacher = Employee::factory()->create();
		$gradebookGroup = GradebookGroup::factory()->create([
			"gradebook_id" => $gradebook->id,
		]);
		$gradebookGroup->students()->attach($this->actingStudentUser->students->first());
		$lesson = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"primary_teacher_id" => $primaryTeacher->id
		]);
		$lesson->gradebooks()->sync([$gradebook->id]);
		$lesson->gradebookGroups()->attach($gradebookGroup);
		$response = $this->getJson("/api/students/me/gradebooks/$gradebook->id/lessons");
		$response->assertStatus(200);
		$response->assertJsonCount(1);
		$response->assertJsonPath("0.id", $lesson->id);
	}
}

<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\AttendancePrimitiveType;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Gradebook;
use App\Models\GradebookGroup;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class AttendanceControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_day_view_returns_correct_structure(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();

		$student = Student::factory()->create();
		$gradebook->students()->attach($student->id, [
			"position" => 1,
			"date_from" => now()->toDateString()
		]);

		$group = GradebookGroup::factory()->create(["gradebook_id" => $gradebook->id]);
		$group->students()->attach($student->id);

		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
		]);
		$lesson->gradebooks()->sync([$gradebook->id]);

		Attendance::factory()->create([
			"lesson_id" => $lesson->id,
			"student_id" => $student->id,
			"primitive_type" => AttendancePrimitiveType::PRESENCE->value,
			"employee_id" => $employee->id,
		]);

		$response = $this->get("/api/gradebooks/$gradebook->id/attendance/dayView?date=$lesson->date");

		$response->assertOk();
		$response->assertJsonPath("0.id", $lesson->id);
		$response->assertJsonPath("0.subject", $lesson->subject->name);
		$response->assertJsonPath("0.students.0.id", $student->id);
		$response->assertJsonPath("0.attendances.0.primitiveType", AttendancePrimitiveType::PRESENCE->value);
	}

	public function test_can_create_or_update_attendance_as_primary_teacher(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
		]);
		$lesson->gradebooks()->sync([$gradebook->id]);
		$student = Student::factory()->create();
		$gradebook->students()->attach($student->id, [
			"position" => 1,
			"date_from" => now()->toDateString(),
		]);

		$response = $this->post("/api/gradebooks/$gradebook->id/attendance/$lesson->id", [
			"attendances" => [
				[
					"student_id" => $student->id,
					"primitive_type" => AttendancePrimitiveType::ABSENCE->value,
				]
			]
		]);

		$response->assertCreated();
		$this->assertDatabaseHas("attendances", [
			"lesson_id" => $lesson->id,
			"student_id" => $student->id,
			"primitive_type" => AttendancePrimitiveType::ABSENCE->value,
			"employee_id" => $employee->id,
		]);
	}
	public function test_can_delete_attendance_as_primary_teacher(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
		]);
		$lesson->gradebooks()->sync([$gradebook->id]);
		$student = Student::factory()->create();

		$gradebook->students()->attach($student->id, [
			"position" => 1,
			"date_from" => now()->toDateString(),
		]);

		Attendance::factory()->create([
			"lesson_id" => $lesson->id,
			"student_id" => $student->id,
			"employee_id" => $employee->id,
		]);

		$response = $this->post("/api/gradebooks/$gradebook->id/attendance/$lesson->id", [
			"attendances" => [
				[
					"student_id" => $student->id,
					"primitive_type" => null,
					"complex_type_id" => null,
				]
			]
		]);

		$response->assertCreated();
		$this->assertDatabaseMissing("attendances", [
			"lesson_id" => $lesson->id,
			"student_id" => $student->id,
		]);
	}

	public function test_autofill_copies_attendance_from_previous_lesson_and_modifies_lateness(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$subject = Subject::factory()->create();

		$olderLesson = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"date" => now()->toDateString(),
			"start_time" => "07:00:00",
		]);
		$olderLesson->gradebooks()->sync([$gradebook->id]);

		$newerLesson = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"date" => now()->toDateString(),
			"start_time" => "07:45:00",
		]);
		$newerLesson->gradebooks()->sync([$gradebook->id]);

		$lesson = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
			"start_time" => "08:00:00",
		]);
		$lesson->gradebooks()->sync([$gradebook->id]);

		$studentA = Student::factory()->create();
		$studentB = Student::factory()->create();

		Attendance::factory()->create([
			"lesson_id" => $olderLesson->id,
			"student_id" => $studentA->id,
			"primitive_type" => AttendancePrimitiveType::LATENESS->value,
		]);
		Attendance::factory()->create([
			"lesson_id" => $newerLesson->id,
			"student_id" => $studentA->id,
			"primitive_type" => AttendancePrimitiveType::EXCUSED_ABSENCE->value,
		]);

		Attendance::factory()->create([
			"lesson_id" => $olderLesson->id,
			"student_id" => $studentB->id,
			"primitive_type" => AttendancePrimitiveType::ABSENCE->value,
		]);

		$response = $this->post("/api/gradebooks/$gradebook->id/attendance/$lesson->id/autofill");

		$response->assertOk();

		$response->assertJsonCount(2);

		$response->assertJsonFragment([
			"student_id" => $studentA->id,
			"primitive_type" => AttendancePrimitiveType::EXCUSED_ABSENCE->value,
		]);

		$response->assertJsonFragment([
			"student_id" => $studentB->id,
			"primitive_type" => AttendancePrimitiveType::ABSENCE->value,
		]);

		$this->assertDatabaseMissing("attendances", ["lesson_id" => $lesson->id]);
	}

	public function test_autofill_returns_error_if_no_previous_lesson_exists(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
		]);
		$lesson->gradebooks()->sync([$gradebook->id]);

		$response = $this->postJson("/api/gradebooks/$gradebook->id/attendance/$lesson->id/autofill");

		$response->assertStatus(404);
		$response->assertJsonFragment(["PREVIOUS_LESSONS_NOT_FOUND"]);
	}

	public function test_create_or_update_fails_if_unauthorized(): void
	{
		$this->actingAdminUser();
		$gradebook = Gradebook::factory()->create();
		$otherEmployee = Employee::factory()->create();

		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $otherEmployee->id,
		]);
		$lesson->gradebooks()->sync([$gradebook->id]);
		$student = Student::factory()->create();
		$gradebook->students()->attach($student->id, [
			"position" => 1,
			"date_from" => now()->toDateString(),
		]);

		$response = $this->post("/api/gradebooks/$gradebook->id/attendance/$lesson->id", [
			"attendances" => [
				[
					"student_id" => $student->id,
					"primitive_type" => AttendancePrimitiveType::ABSENCE->value,
				]
			]
		]);

		$response->assertStatus(403);
		$response->assertJsonFragment(["UNAUTHORIZED_TO_PERFORM_ACTION"]);
	}

	public function test_create_or_update_fails_if_student_not_in_gradebook(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
		]);
		$lesson->gradebooks()->sync([$gradebook->id]);
		// NOT attached to the gradebook
		$student = Student::factory()->create();

		$response = $this->post("/api/gradebooks/$gradebook->id/attendance/$lesson->id", [
			"attendances" => [
				[
					"student_id" => $student->id,
					"primitive_type" => AttendancePrimitiveType::PRESENCE->value,
				]
			]
		]);

		$response->assertUnprocessable();
	}
}

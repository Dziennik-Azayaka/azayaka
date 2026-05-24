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
			"gradebook_id" => $gradebook->id,
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
		]);

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
			"gradebook_id" => $gradebook->id,
			"primary_teacher_id" => $employee->id,
		]);
		$student = Student::factory()->create();

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

	public function test_create_or_update_fails_validation_if_both_types_are_null(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"gradebook_id" => $gradebook->id,
			"primary_teacher_id" => $employee->id,
		]);
		$student = Student::factory()->create();

		$response = $this->post("/api/gradebooks/$gradebook->id/attendance/$lesson->id", [
			"attendances" => [
				[
					"student_id" => $student->id,
					// no attendance type
				]
			]
		]);

		$response->assertUnprocessable();
	}

	public function test_can_delete_attendance_as_primary_teacher(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"gradebook_id" => $gradebook->id,
			"primary_teacher_id" => $employee->id,
		]);
		$student = Student::factory()->create();

		$attendance = Attendance::factory()->create([
			"lesson_id" => $lesson->id,
			"student_id" => $student->id,
			"employee_id" => $employee->id,
		]);

		$response = $this->delete("/api/gradebooks/$gradebook->id/attendance/$attendance->id");

		$response->assertOk();
		$this->assertDatabaseMissing("attendances", ["id" => $attendance->id]);
	}

	public function test_destroy_fails_if_unauthorized(): void
	{
		$this->actingAdminUser();
		$gradebook = Gradebook::factory()->create();
		$otherEmployee = Employee::factory()->create();

		$lesson = Lesson::factory()->create([
			"gradebook_id" => $gradebook->id,
			"primary_teacher_id" => $otherEmployee->id,
		]);
		$student = Student::factory()->create();

		$attendance = Attendance::factory()->create([
			"lesson_id" => $lesson->id,
			"student_id" => $student->id,
		]);

		$response = $this->delete("/api/gradebooks/$gradebook->id/attendance/$attendance->id");

		$response->assertStatus(422);
		$response->assertJsonFragment(["0_UNAUTHORIZED_TO_EDIT_ATTENDANCE"]);
	}

	public function test_autofill_copies_attendance_from_previous_lesson_and_modifies_lateness(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$subject = Subject::factory()->create();

		$previousLesson = Lesson::factory()->create([
			"gradebook_id" => $gradebook->id,
			"subject_id" => $subject->id,
			"date" => now()->toDateString(),
			"start_time" => "07:00:00",
		]);

		$lesson = Lesson::factory()->create([
			"gradebook_id" => $gradebook->id,
			"subject_id" => $subject->id,
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
			"start_time" => "08:00:00",
		]);

		$student = Student::factory()->create();

		Attendance::factory()->create([
			"lesson_id" => $previousLesson->id,
			"student_id" => $student->id,
			"primitive_type" => AttendancePrimitiveType::LATENESS->value,
		]);

		$response = $this->get("/api/gradebooks/$gradebook->id/attendance/$lesson->id/autofill");

		$response->assertOk();

		// lateness -> presence
		$this->assertDatabaseHas("attendances", [
			"lesson_id" => $lesson->id,
			"student_id" => $student->id,
			"primitive_type" => AttendancePrimitiveType::PRESENCE->value,
			"employee_id" => $employee->id,
		]);
	}

	public function test_autofill_returns_error_if_no_previous_lesson_exists(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"gradebook_id" => $gradebook->id,
			"primary_teacher_id" => $employee->id,
		]);

		$response = $this->get("/api/gradebooks/$gradebook->id/attendance/$lesson->id/autofill");

		$response->assertStatus(422);
		$response->assertJsonFragment(["NO_PREVIOUS_LESSON_FOUND"]);
	}
}

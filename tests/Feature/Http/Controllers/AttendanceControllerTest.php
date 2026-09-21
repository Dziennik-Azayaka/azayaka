<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\AttendancePrimitiveType;
use App\Models\Attendance;
use App\Models\AttendanceComplexType;
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

	private function attachGradebook(Lesson $lesson, int ...$gradebookIds): void
	{
		foreach ($gradebookIds as $gradebookId) {
			$lesson->gradebooks()->create(["gradebook_id" => $gradebookId]);
		}
	}

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
		$lessonGradebook = $lesson->gradebooks()->create(["gradebook_id" => $gradebook->id]);
		$lessonGradebook->groups()->create(["gradebook_group_id" => $group->id]);

		$complexType = AttendanceComplexType::factory()->create();
		Attendance::factory()->create([
			"lesson_id" => $lesson->id,
			"student_id" => $student->id,
			"attendance_complex_type_id" => $complexType->id,
			"employee_id" => $employee->id,
		]);

		$response = $this->get("/api/gradebookGroups/$group->id/attendance/dayView?date=$lesson->date");

		$response->assertOk();
		$response->assertJsonPath("0.id", $lesson->id);
		$response->assertJsonPath("0.subject", $lesson->subject->name);
		$response->assertJsonPath("0.students.0.id", $student->id);
		$response->assertJsonPath("0.attendances.0.complexType", $complexType->id);
	}

	public function test_can_create_or_update_attendance_as_primary_teacher(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
		]);
		$this->attachGradebook($lesson, $gradebook->id);
		$student = Student::factory()->create();
		$gradebook->students()->attach($student->id, [
			"position" => 1,
			"date_from" => now()->toDateString(),
		]);

		$complexType = AttendanceComplexType::factory()->create();

		$response = $this->post("/api/lessons/$lesson->id/attendance", [
			"attendances" => [
				[
					"student_id" => $student->id,
					"complex_type_id" => $complexType->id,
				]
			]
		]);

		$response->assertCreated();
		$this->assertDatabaseHas("attendances", [
			"lesson_id" => $lesson->id,
			"student_id" => $student->id,
			"attendance_complex_type_id" => $complexType->id,
			"employee_id" => $employee->id,
		]);
	}

	public function test_can_delete_attendance_as_primary_teacher(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
		]);
		$this->attachGradebook($lesson, $gradebook->id);
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

		$response = $this->post("/api/lessons/$lesson->id/attendance", [
			"attendances" => [
				[
					"student_id" => $student->id,
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
		$this->attachGradebook($olderLesson, $gradebook->id);

		$newerLesson = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"date" => now()->toDateString(),
			"start_time" => "07:45:00",
		]);
		$this->attachGradebook($newerLesson, $gradebook->id);

		$lesson = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
			"start_time" => "08:00:00",
		]);
		$this->attachGradebook($lesson, $gradebook->id);

		$studentA = Student::factory()->create();
		$studentB = Student::factory()->create();

		$excusedAbsenceTypeId = AttendanceComplexType::where("maps_to_primitive_type", AttendancePrimitiveType::EXCUSED_ABSENCE)
			->where("built_in", true)->first()->id;
		$absenceTypeId = AttendanceComplexType::where("maps_to_primitive_type", AttendancePrimitiveType::ABSENCE)
			->where("built_in", true)->first()->id;

		Attendance::factory()->create([
			"lesson_id" => $olderLesson->id,
			"student_id" => $studentA->id,
			"attendance_complex_type_id" => AttendanceComplexType::where("maps_to_primitive_type", AttendancePrimitiveType::LATENESS)
				->where("built_in", true)->first()->id
		]);
		Attendance::factory()->create([
			"lesson_id" => $newerLesson->id,
			"student_id" => $studentA->id,
			"attendance_complex_type_id" => $excusedAbsenceTypeId
		]);

		Attendance::factory()->create([
			"lesson_id" => $olderLesson->id,
			"student_id" => $studentB->id,
			"attendance_complex_type_id" => $absenceTypeId
		]);

		$response = $this->post("/api/lessons/$lesson->id/attendance/autofill");

		$response->assertOk();

		$response->assertJsonCount(2);

		$response->assertJsonFragment([
			"studentId" => $studentA->id,
			"complexTypeId" => $excusedAbsenceTypeId,
		]);

		$response->assertJsonFragment([
			"studentId" => $studentB->id,
			"complexTypeId" => $absenceTypeId,
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
		$this->attachGradebook($lesson, $gradebook->id);

		$response = $this->postJson("/api/lessons/$lesson->id/attendance/autofill");

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
		$this->attachGradebook($lesson, $gradebook->id);
		$student = Student::factory()->create();
		$gradebook->students()->attach($student->id, [
			"position" => 1,
			"date_from" => now()->toDateString(),
		]);

		$response = $this->post("/api/lessons/$lesson->id/attendance", [
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
		$this->attachGradebook($lesson, $gradebook->id);
		// NOT attached to the gradebook
		$student = Student::factory()->create();

		$response = $this->post("/api/lessons/$lesson->id/attendance", [
			"attendances" => [
				[
					"student_id" => $student->id,
					"primitive_type" => AttendancePrimitiveType::PRESENCE->value,
				]
			]
		]);

		$response->assertUnprocessable();
	}

	public function test_sync_fails_if_student_gradebook_date_range_does_not_cover_lesson_date(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
			"date" => "2025-06-15",
		]);
		$this->attachGradebook($lesson, $gradebook->id);
		$studentA = Student::factory()->create();
		$studentB = Student::factory()->create();
		$gradebook->students()->attach($studentA->id, [
			"position" => 1,
			"date_from" => "2025-01-01",
			"date_to" => "2025-05-31",
		]);
		$gradebook->students()->attach($studentB->id, [
			"position" => 2,
			"date_from" => "2025-07-01",
		]);

		$complexType = AttendanceComplexType::factory()->create();

		$response = $this->post("/api/lessons/$lesson->id/attendance", [
			"attendances" => [
				[
					"student_id" => $studentA->id,
					"complex_type_id" => $complexType->id,
				]
			]
		]);

		$response->assertUnprocessable();
		$response->assertJsonPath("errors.0", "STUDENT_NOT_IN_GRADEBOOK_FOR_LESSON_DATE");
	}

	public function test_sync_supports_multiple_gradebooks_on_the_same_lesson(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebookA = Gradebook::factory()->create();
		$gradebookB = Gradebook::factory()->create();

		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
		]);
		$this->attachGradebook($lesson, $gradebookA->id, $gradebookB->id);

		$studentA = Student::factory()->create();
		$studentB = Student::factory()->create();
		$studentOutside = Student::factory()->create();

		$gradebookA->students()->attach($studentA->id, [
			"position" => 1,
			"date_from" => now()->toDateString(),
		]);
		$gradebookB->students()->attach($studentB->id, [
			"position" => 1,
			"date_from" => now()->toDateString(),
		]);

		$complexType = AttendanceComplexType::factory()->create();

		$response = $this->post("/api/lessons/$lesson->id/attendance", [
			"attendances" => [
				["student_id" => $studentA->id, "complex_type_id" => $complexType->id],
				["student_id" => $studentB->id, "complex_type_id" => $complexType->id],
			]
		]);

		$response->assertCreated();
		$this->assertDatabaseHas("attendances", [
			"lesson_id" => $lesson->id,
			"student_id" => $studentA->id,
			"attendance_complex_type_id" => $complexType->id,
			"employee_id" => $employee->id,
		]);
		$this->assertDatabaseHas("attendances", [
			"lesson_id" => $lesson->id,
			"student_id" => $studentB->id,
			"attendance_complex_type_id" => $complexType->id,
			"employee_id" => $employee->id,
		]);
		$this->assertDatabaseMissing("attendances", [
			"lesson_id" => $lesson->id,
			"student_id" => $studentOutside->id,
		]);
	}

	public function test_sync_rejects_request_when_any_student_is_outside_any_gradebook(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebookA = Gradebook::factory()->create();
		$gradebookB = Gradebook::factory()->create();

		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
		]);
		$this->attachGradebook($lesson, $gradebookA->id, $gradebookB->id);

		$studentA = Student::factory()->create();
		$studentOutside = Student::factory()->create();

		$gradebookA->students()->attach($studentA->id, [
			"position" => 1,
			"date_from" => now()->toDateString(),
		]);

		$complexType = AttendanceComplexType::factory()->create();

		$response = $this->post("/api/lessons/$lesson->id/attendance", [
			"attendances" => [
				["student_id" => $studentA->id, "complex_type_id" => $complexType->id],
				["student_id" => $studentOutside->id, "complex_type_id" => $complexType->id],
			]
		]);

		$response->assertUnprocessable();
		$response->assertJsonPath("errors.0", "ATTENDANCES_1_STUDENT_ID_THE_SELECTED_ATTENDANCES_1_STUDENT_ID_IS_INVALID");
		$this->assertDatabaseMissing("attendances", ["lesson_id" => $lesson->id]);
	}

	public function test_sync_rejects_request_when_student_gradebook_date_range_does_not_cover_lesson_date_across_any_gradebook(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebookA = Gradebook::factory()->create();
		$gradebookB = Gradebook::factory()->create();

		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
			"date" => "2025-06-15",
		]);
		$this->attachGradebook($lesson, $gradebookA->id, $gradebookB->id);

		// studentA is in gradebookA but outside the date range
		$studentA = Student::factory()->create();
		$gradebookA->students()->attach($studentA->id, [
			"position" => 1,
			"date_from" => "2025-01-01",
			"date_to" => "2025-05-31",
		]);

		// studentB is in gradebookB with a valid range
		$studentB = Student::factory()->create();
		$gradebookB->students()->attach($studentB->id, [
			"position" => 1,
			"date_from" => "2025-01-01",
		]);

		$complexType = AttendanceComplexType::factory()->create();

		$response = $this->post("/api/lessons/$lesson->id/attendance", [
			"attendances" => [
				["student_id" => $studentA->id, "complex_type_id" => $complexType->id],
				["student_id" => $studentB->id, "complex_type_id" => $complexType->id],
			]
		]);

		$response->assertUnprocessable();
		$response->assertJsonPath("errors.0", "STUDENT_NOT_IN_GRADEBOOK_FOR_LESSON_DATE");
		$this->assertDatabaseMissing("attendances", ["lesson_id" => $lesson->id]);
	}

	public function test_sync_rejects_request_when_lesson_has_no_gradebooks_attached(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebook = Gradebook::factory()->create();
		$lesson = Lesson::factory()->create([
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
		]);
		// No gradebooks attached to the lesson

		$student = Student::factory()->create();
		$gradebook->students()->attach($student->id, [
			"position" => 1,
			"date_from" => now()->toDateString(),
		]);

		$complexType = AttendanceComplexType::factory()->create();

		$response = $this->post("/api/lessons/$lesson->id/attendance", [
			"attendances" => [
				["student_id" => $student->id, "complex_type_id" => $complexType->id],
			]
		]);

		$response->assertUnprocessable();
		$response->assertJsonPath("errors.0", "ATTENDANCES_0_STUDENT_ID_THE_SELECTED_ATTENDANCES_0_STUDENT_ID_IS_INVALID");
		$this->assertDatabaseMissing("attendances", ["lesson_id" => $lesson->id]);
	}

	public function test_autofill_aggregates_attendance_across_multiple_gradebooks(): void
	{
		$employee = $this->actingAdminUser()->employees->first();
		$gradebookA = Gradebook::factory()->create();
		$gradebookB = Gradebook::factory()->create();
		$subject = Subject::factory()->create();

		$olderLessonA = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"date" => now()->toDateString(),
			"start_time" => "07:00:00",
		]);
		$this->attachGradebook($olderLessonA, $gradebookA->id, $gradebookB->id);

		$olderLessonB = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"date" => now()->toDateString(),
			"start_time" => "07:30:00",
		]);
		$this->attachGradebook($olderLessonB, $gradebookB->id);

		$lesson = Lesson::factory()->create([
			"subject_id" => $subject->id,
			"primary_teacher_id" => $employee->id,
			"date" => now()->toDateString(),
			"start_time" => "08:00:00",
		]);
		$this->attachGradebook($lesson, $gradebookA->id, $gradebookB->id);

		$studentA = Student::factory()->create(); // in gradebookA
		$studentB = Student::factory()->create(); // in gradebookB

		$excusedAbsenceTypeId = AttendanceComplexType::where("maps_to_primitive_type", AttendancePrimitiveType::EXCUSED_ABSENCE)
			->where("built_in", true)->first()->id;
		$absenceTypeId = AttendanceComplexType::where("maps_to_primitive_type", AttendancePrimitiveType::ABSENCE)
			->where("built_in", true)->first()->id;

		Attendance::factory()->create([
			"lesson_id" => $olderLessonA->id,
			"student_id" => $studentA->id,
			"attendance_complex_type_id" => $excusedAbsenceTypeId,
		]);
		Attendance::factory()->create([
			"lesson_id" => $olderLessonB->id,
			"student_id" => $studentB->id,
			"attendance_complex_type_id" => $absenceTypeId,
		]);

		$response = $this->post("/api/lessons/$lesson->id/attendance/autofill");

		$response->assertOk();
		$response->assertJsonCount(2);
		$response->assertJsonFragment([
			"studentId" => $studentA->id,
			"complexTypeId" => $excusedAbsenceTypeId,
		]);
		$response->assertJsonFragment([
			"studentId" => $studentB->id,
			"complexTypeId" => $absenceTypeId,
		]);
	}
}

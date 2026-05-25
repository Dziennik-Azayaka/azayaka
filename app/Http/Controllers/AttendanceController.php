<?php

namespace App\Http\Controllers;

use App\Enums\AttendancePrimitiveType;
use App\Exceptions\CustomValidationException;
use App\Models\AccountAccess;
use App\Models\Attendance;
use App\Models\AttendanceComplexType;
use App\Models\Employee;
use App\Models\Gradebook;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
	public function dayView(Request $request, Gradebook $gradebook)
	{
		$gradebook = $gradebook->load(["students.person"]);
		$date = $request->input("date", now()->toDateString());

		$lessons = Lesson::with([
			"subject",
			"primaryTeacher",
			"attendances",
			"attendances.complexType",
			"attendances.employee",
		])->where("gradebook_id", $gradebook->id)
			->where("date", $date)
			->orderBy("start_time")
			->get();

		return $lessons->map(fn(Lesson $lesson) => [
			"id" => $lesson->id,
			"subject" => $lesson->subject->name,
			"number" => $lesson->number,
			"startTime" => $lesson->start_time,
			"endTime" => $lesson->end_time,
			"students" => $gradebook->students->map(fn(Student $student) => [
				"id" => $student->id,
				"firstName" => $student->person->first_name,
				"secondName" => $student->person->second_name,
				"lastName" => $student->person->last_name,
				"position" => $student->pivot->position,
			]),
			"attendances" => $lesson->attendances->map(fn(Attendance $attendance) => [
				"id" => $attendance->id,
				"primitiveType" => $attendance->primitive_type,
				"complexType" => $attendance->attendance_complex_type_id,
				"studentId" => $attendance->student_id,
				"employee" => $attendance->employee->full_name . " " . $attendance->employee->last_name
			])
		]);
	}

	public function subjectView(Gradebook $gradebook, Subject $subject)
	{
		$gradebook = $gradebook->load("students");
		$lessons = Lesson::with([
			"attendances.complexType",
			"attendances.student",
			"attendances.employee"
		])
			->where("subject_id", $subject->id)
			->where("gradebook_id", $gradebook->id)
			->orderByDesc("date")
			->orderByDesc("start_time")
			->paginate(15);

		$students = Student::whereHas("gradebookGroups", function ($query) use ($gradebook) {
			$query->where("gradebook_id", $gradebook->id);
		})->orderBy("student_registry_number")->get();

		return $lessons->map(fn(Lesson $lesson) => [
			"id" => $lesson->id,
			"subject" => $lesson->subject->name,
			"number" => $lesson->number,
			"date" => $lesson->date,
			"startTime" => $lesson->start_time,
			"endTime" => $lesson->end_time,
			"students" => $students->map(fn(Student $student) => [
				"id" => $student->id,
				"firstName" => $student->person->first_name,
				"secondName" => $student->person->second_name,
				"lastName" => $student->person->last_name,
				"position" => $gradebook->students()->find($student->id)->pivot->position
			]),
			"attendances" => $lesson->attendances->map(fn(Attendance $attendance) => [
				"id" => $attendance->id,
				"primitiveType" => $attendance->primitive_type,
				"complexType" => $attendance->attendance_complex_type_id,
				"studentId" => $attendance->student_id,
				"employee" => $attendance->employee->full_name . " " . $attendance->employee->last_name
			])
		]);
	}

	public function createOrUpdate(Request $request, Gradebook $gradebook, Lesson $lesson): JsonResponse
	{
		$employee = $this->getUserEmployee($request);
		$this->authorizeLessonAttendanceEdit($employee, $lesson);

		$validated = $request->validate([
			"attendances" => ["required", "array"],
			"attendances.*.student_id" => ["required", "exists:students,id"],
			"attendances.*.primitive_type" => ["required_without:attendances.*.complex_type_id", Rule::enum(AttendancePrimitiveType::class)],
			"attendances.*.complex_type_id" => ["required_without:attendances.*.primitive_type", "exists:attendance_complex_types,id"]
		]);

		$attendances = $validated["attendances"];

		$complexTypeIds = collect($attendances)->pluck("complex_type_id")->filter()->unique();
		$complexTypes = AttendanceComplexType::whereIn("id", $complexTypeIds)->get()->keyBy("id");

		$upsertData = [];
		$studentIdsToDelete = [];
		$now = now();

		foreach ($attendances as $item) {
			$studentId = $item["student_id"];
			$primitiveType = $item["primitive_type"] ?? null;
			$complexTypeId = $item["complex_type_id"] ?? null;

			// delete attendance if the user has removed an entry
			if ($primitiveType === null && $complexTypeId === null) {
				$studentIdsToDelete[] = $studentId;
				continue;
			}

			$upsertData[] = [
				"lesson_id" => $lesson->id,
				"student_id" => $studentId,
				"primitive_type" => $primitiveType,
				"attendance_complex_type_id" => $complexTypeId,
				"employee_id" => $employee->id,
				"created_at" => $now,
				"updated_at" => $now,
			];
		}

		DB::transaction(function () use ($lesson, $upsertData, $studentIdsToDelete) {
			if (!empty($studentIdsToDelete)) {
				Attendance::where("lesson_id", $lesson->id)
					->whereIn("student_id", $studentIdsToDelete)
					->delete();
			}

			if (!empty($upsertData)) {
				Attendance::upsert(
					$upsertData,
					["lesson_id", "student_id"], // which fields are unique?
					["primitive_type", "attendance_complex_type_id", "employee_id", "updated_at"] // if the entry already exists, update these.
				);
			}
		});

		return response()->json([
			"success" => true
		], 201);
	}

	public function destroy(Request $request, Gradebook $gradebook, Attendance $attendance)
	{
		$this->authorizeAttendanceEdit($this->getUserEmployee($request), $attendance->lesson, $attendance->student);
		$attendance->delete();

		return [
			"success" => true
		];
	}

	public function autofill(Request $request, Gradebook $gradebook, Lesson $lesson)
	{
		$employee = $this->getUserEmployee($request);
		$this->authorizeLessonAttendanceEdit($employee, $lesson);

		$previousLesson = Lesson::where("subject_id", $lesson->subject_id)
			->where("gradebook_id", $lesson->gradebook_id)
			->where("date", $lesson->date)
			->where("start_time", "<", $lesson->start_time)
			->orderByDesc("start_time")
			->first();

		if (!$previousLesson) {
			return \Response::json([
				"success" => false,
				"errors" => ["NO_PREVIOUS_LESSON_FOUND"]
			], 422);
		}

		$previousAttendances = Attendance::where("lesson_id", $previousLesson->id)->get();
		$newAttendances = [];

		foreach ($previousAttendances as $previousAttendance) {
			if ($previousAttendance->attendance_complex_type_id != null) {
				continue;
			}

			$newType = match ($previousAttendance->primitive_type) {
				AttendancePrimitiveType::PRESENCE,
				AttendancePrimitiveType::LATENESS,
				AttendancePrimitiveType::EXCUSED_LATENESS => AttendancePrimitiveType::PRESENCE,
				AttendancePrimitiveType::ABSENCE => AttendancePrimitiveType::ABSENCE,
				AttendancePrimitiveType::EXCUSED_ABSENCE => AttendancePrimitiveType::EXCUSED_ABSENCE,
				AttendancePrimitiveType::EXEMPTION => AttendancePrimitiveType::EXEMPTION
			};

			$newAttendances[] = [
				"lesson_id" => $lesson->id,
				"student_id" => $previousAttendance->student_id,
				"primitive_type" => $newType,
				"attendance_complex_type_id" => null,
				"employee_id" => $employee->id,
				"created_at" => now(),
				"updated_at" => now(),
			];
		}

		Attendance::insert($newAttendances);

		return ["success" => true];
	}

	private function authorizeAttendanceEdit(Employee $employee, Lesson $lesson, Student $student): void
	{
		if ($lesson->primary_teacher_id == $employee->id) {
			return;
		}

		// Please excuse this mess for now.
		// employee -> form tutor -> class unit -> gradebook -> group -> student
		$isFormTutor = DB::table("class_units_form_tutors")
			->join("class_units", "class_units_form_tutors.class_unit_id", "=", "class_units.id")
			->join("gradebooks", "class_units.id", "=", "gradebooks.class_unit_id")
			->join("gradebook_groups", "gradebooks.id", "=", "gradebook_groups.gradebook_id")
			->join("gradebook_group_student", "gradebook_groups.id", "=", "gradebook_group_student.gradebook_group_id")
			->where("class_units_form_tutors.employee_id", $employee->id)
			->where("gradebook_group_student.student_id", $student->id)
			->exists();

		if ($isFormTutor) {
			return;
		}

		throw CustomValidationException::withMessages(["UNAUTHORIZED_TO_EDIT_ATTENDANCE"]);
	}

	private function authorizeLessonAttendanceEdit(Employee $employee, Lesson $lesson): void
	{
		if ($lesson->primary_teacher_id === $employee->id) {
			return;
		}

		$isFormTutor = DB::table("class_units_form_tutors")
			->join("gradebooks", "class_units_form_tutors.class_unit_id", "=", "gradebooks.class_unit_id")
			->where("gradebooks.id", $lesson->gradebook_id)
			->where("class_units_form_tutors.employee_id", $employee->id)
			->where("class_units_form_tutors.date_from", "<=", $lesson->date)
			->where("class_units_form_tutors.date_to", ">=", $lesson->date)
			->exists();

		if ($isFormTutor) {
			return;
		}

		throw CustomValidationException::withMessages(["UNAUTHORIZED_TO_EDIT_ATTENDANCE"]);
	}

	private function getUserEmployee(Request $request): Employee
	{
		$accessID = $request->header("Access-ID") ?? $request->route("accessId");
		$employeeAccess = AccountAccess::where("user_id", $request->user()->id)
			->where("id", $accessID)
			->with("employee")->first();
		return $employeeAccess->employee;
	}
}

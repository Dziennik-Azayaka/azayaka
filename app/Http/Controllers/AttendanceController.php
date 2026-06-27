<?php

namespace App\Http\Controllers;

use App\Enums\AttendancePrimitiveType;
use App\Models\Attendance;
use DB;
use App\Models\AttendanceComplexType;
use App\Http\Resources\LessonAttendanceResource;
use App\Models\Employee;
use App\Models\Gradebook;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
	public function dayView(Request $request, Gradebook $gradebook)
	{
		$gradebook = $gradebook->load(["students.person"]);
		$date = $request->input("date", now()->toDateString());;

		$lessons = Lesson::with([
			"subject",
			"primaryTeacher",
			"attendances",
			"attendances.complexType",
			"attendances.employee",
		])->whereHas("gradebooks", fn ($q) => $q->where("gradebook_id", $gradebook->id))
			->where("date", $date)
			->orderBy("start_time")
			->get();

		return $lessons->map(fn(Lesson $lesson) => new LessonAttendanceResource(
			$lesson,
			$gradebook->students,
			false
		));
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
			->whereHas("gradebooks", fn ($q) => $q->where("gradebook_id", $gradebook->id))
			->orderByDesc("date")
			->orderByDesc("start_time")
			->paginate(15);

		$students = Student::whereHas("gradebookGroups", function ($query) use ($gradebook) {
			$query->where("gradebook_id", $gradebook->id);
		})->orderBy("student_registry_number")->get();

		return $lessons->map(fn(Lesson $lesson) => new LessonAttendanceResource(
			$lesson,
			$students,
			true
		));
	}

	public function createOrUpdate(Request $request, Gradebook $gradebook, Lesson $lesson): JsonResponse
	{
		$employee = $this->getUserEmployee($request);
		$this->authorize("editAttendance", [$lesson]);

		$validated = $request->validate([
			"attendances" => ["required", "array"],
			"attendances.*.student_id" => [
				"required",
				Rule::exists("gradebooks_students", "student_id")->where("gradebook_id", $gradebook->id)
			],
			"attendances.*.primitive_type" => ["nullable", Rule::enum(AttendancePrimitiveType::class)],
			"attendances.*.complex_type_id" => ["nullable", "exists:attendance_complex_types,id"]
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

	public function autofill(Request $request, Gradebook $gradebook, Lesson $lesson)
	{
		$this->authorize("editAttendance", [$lesson]);

		$previousLessons = Lesson::where("subject_id", $lesson->subject_id)
			->whereHas("gradebooks", fn ($q) => $q->whereIn("gradebook_id", $lesson->gradebooks->pluck("id")))
			->where("date", $lesson->date)
			->where("start_time", "<", $lesson->start_time)
			->orderByDesc("start_time")
			->get();

		if ($previousLessons->isEmpty()) {
			return \Response::json([
				"success" => false,
				"errors" => ["NO_PREVIOUS_LESSONS_FOUND"]
			], 422);
		}

		$lessonIds = $previousLessons->pluck("id");
		$previousAttendances = Attendance::whereIn("lesson_id", $lessonIds)
			->get()
			->sortBy(fn(Attendance $attendance) => $lessonIds->search($attendance->lesson_id));
		$newAttendances = [];

		foreach ($previousAttendances as $previousAttendance) {
			if (array_key_exists($previousAttendance->student_id, $newAttendances)) {
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

			$newAttendances[$previousAttendance->student_id] = [
				"student_id" => $previousAttendance->student_id,
				"primitive_type" => $newType,
				"attendance_complex_type_id" => $previousAttendance->attendance_complex_type_id,
			];
		}

		return array_values($newAttendances);
	}

}

<?php

namespace App\Http\Controllers;

use App\Enums\AttendancePrimitiveType;
use App\Exceptions\NotFoundException;
use App\Http\Resources\LessonAttendanceResource;
use App\Models\Attendance;
use App\Models\AttendanceComplexType;
use App\Models\GradebookGroup;
use App\Models\GradebookStudents;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Services\AccessContext;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
	public function dayView(Request $request, GradebookGroup $gradebookGroup)
	{
		$gradebookGroup = $gradebookGroup->load(["students.person"]);
		$date = $request->input("date", now()->toDateString());

		$lessons = Lesson::with([
			"subject",
			"primaryTeacher",
			"attendances",
			"attendances.complexType",
			"attendances.employee",
		])->whereHas("gradebookGroups", fn($q) => $q->where("gradebook_group_id", $gradebookGroup->id))
			->where("date", $date)
			->oldest("start_time")
			->get();

		return $lessons->map(fn(Lesson $lesson) => new LessonAttendanceResource(
			$lesson,
			$gradebookGroup->students,
			false
		));
	}

	public function subjectView(GradebookGroup $gradebookGroup, Subject $subject)
	{
		$gradebookGroup = $gradebookGroup->load("students");
		$lessons = Lesson::with([
			"attendances.complexType",
			"attendances.student",
			"attendances.employee"
		])
			->where("subject_id", $subject->id)
			->whereHas("gradebookGroups", fn($q) => $q->where("gradebook_group_id", $gradebookGroup->id))
			->orderByDesc("date")
			->orderByDesc("start_time")
			->paginate(15);

		$students = Student::join("gradebook_group_student", "students.id", "=", "gradebook_group_student.student_id")
			->where("gradebook_group_student.gradebook_group_id", $gradebookGroup->id)
			->orderBy("students.id")
			->select("students.*")
			->get();

		return $lessons->map(fn(Lesson $lesson) => new LessonAttendanceResource(
			$lesson,
			$students,
			true
		));
	}

	public function sync(Request $request, Lesson $lesson): JsonResponse
	{
		$employee = app(AccessContext::class)->currentEmployee();
		$this->authorize("editAttendance", [$lesson]);

		$gradebookIds = $lesson->gradebooks()->pluck("gradebooks.id")->all();

		if (empty($gradebookIds)) {
			return response()->json([
				"success" => false,
				"errors" => ["LESSON_HAS_NO_GRADEBOOKS"]
			], 422);
		}

		$validated = $request->validate([
			"attendances" => ["required", "array"],
			"attendances.*.student_id" => [
				"required",
				"distinct",
				Rule::exists("gradebooks_students", "student_id")->whereIn("gradebook_id", $gradebookIds)
			],
			"attendances.*.complex_type_id" => ["nullable", "exists:attendance_complex_types,id"]
		]);

		$attendances = $validated["attendances"];

		$submittedStudentIds = array_column($attendances, "student_id");
		$validStudentIds = GradebookStudents::query()
			->whereIn("gradebook_id", $gradebookIds)
			->where("date_from", "<=", $lesson->date)
			->where(function ($query) use ($lesson) {
				$query->whereNull("date_to")->orWhere("date_to", ">=", $lesson->date);
			})
			->whereIn("student_id", $submittedStudentIds)
			->pluck("student_id")
			->toArray();

		$invalidStudentIds = array_diff($submittedStudentIds, $validStudentIds);
		if (!empty($invalidStudentIds)) {
			return response()->json([
				"success" => false,
				"errors" => ["STUDENT_NOT_IN_GRADEBOOK_FOR_LESSON_DATE"]
			], 422);
		}

		$pivotData = [];
		$gradebookStudentIds = GradebookStudents::query()
			->whereIn("gradebook_id", $gradebookIds)
			->pluck("student_id")
			->all();

		foreach ($attendances as $item) {
			$studentId = $item["student_id"];
			$complexTypeId = $item["complex_type_id"] ?? null;

			// delete attendance if the user has removed an entry
			if ($complexTypeId === null) {
				continue;
			}

			$pivotData[$studentId] = [
				"attendance_complex_type_id" => $complexTypeId,
				"employee_id" => $employee->id,
			];
		}

		$currentlyAttached = $lesson->students()->pluck("students.id")->all();
		$submittedIds = array_keys($pivotData);
		$keepIds = array_values(array_unique(array_merge(
			$submittedIds,
			array_diff($currentlyAttached, $gradebookStudentIds)
		)));

		DB::transaction(function () use ($lesson, $pivotData, $keepIds, $currentlyAttached) {
			$lesson->students()->sync(array_intersect_key($pivotData, array_flip($keepIds)));

			$detachIds = array_values(array_diff($currentlyAttached, $keepIds));
			if (!empty($detachIds)) {
				$lesson->students()->detach($detachIds);
			}
		});

		return response()->json([
			"success" => true
		], 201);
	}

	public function autofill(Lesson $lesson)
	{
		$this->authorize("editAttendance", [$lesson]);

		$gradebookIds = $lesson->gradebooks()->pluck("gradebooks.id")->all();

		$previousLessons = Lesson::where("subject_id", $lesson->subject_id)
			->whereHas("gradebooks", fn($q) => $q->whereIn("gradebook_id", $gradebookIds))
			->where("date", $lesson->date)
			->where("start_time", "<", $lesson->start_time)
			->orderByDesc("start_time")
			->get();

		if ($previousLessons->isEmpty()) {
			throw new NotFoundException("PREVIOUS_LESSONS");
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

			if ($previousAttendance->primitive_type === AttendancePrimitiveType::LATENESS ||
				$previousAttendance->primitive_type === AttendancePrimitiveType::EXCUSED_LATENESS) {
				$newAttendances[$previousAttendance->student_id] = [
					"studentId" => $previousAttendance->student_id,
					"complexTypeId" =>
						AttendanceComplexType::where("maps_to_primitive_type", "=", AttendancePrimitiveType::PRESENCE)
							->where("built_in", "=", true)->first()->id
				];
			} else {
				$newAttendances[$previousAttendance->student_id] = [
					"studentId" => $previousAttendance->student_id,
					"complexTypeId" => $previousAttendance->attendance_complex_type_id,
				];
			}
		}

		return array_values($newAttendances);
	}
}

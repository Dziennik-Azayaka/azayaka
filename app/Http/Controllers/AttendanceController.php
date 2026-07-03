<?php

namespace App\Http\Controllers;

use App\Enums\AttendancePrimitiveType;
use App\Exceptions\NotFoundException;
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
		])->whereHas("gradebooks", fn($q) => $q->where("gradebook_id", $gradebook->id))
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
			->whereHas("gradebooks", fn($q) => $q->where("gradebook_id", $gradebook->id))
			->orderByDesc("date")
			->orderByDesc("start_time")
			->paginate(15);

		$students = Student::join("gradebooks_students", "students.id", "=", "gradebooks_students.student_id")
			->where("gradebooks_students.gradebook_id", $gradebook->id)
			->orderBy("gradebooks_students.position")
			->select("students.*")
			->get();

		return $lessons->map(fn(Lesson $lesson) => new LessonAttendanceResource(
			$lesson,
			$students,
			true
		));
	}

	public function sync(Request $request, Gradebook $gradebook, Lesson $lesson): JsonResponse
	{
		$employee = $this->getUserEmployee($request);
		$this->authorize("editAttendance", [$lesson]);

		$validated = $request->validate([
			"attendances" => ["required", "array"],
			"attendances.*.student_id" => [
				"required",
				"distinct",
				Rule::exists("gradebooks_students", "student_id")->where("gradebook_id", $gradebook->id)
			],
			"attendances.*.complex_type_id" => ["nullable", "exists:attendance_complex_types,id"]
		]);

		$attendances = $validated["attendances"];

		$upsertData = [];
		$studentIdsToKeep = [];
		$studentIdsToDelete = [];
		$gradebookStudentIds = $gradebook->students()->pluck("students.id")->all();
		$now = now();

		foreach ($attendances as $item) {
			$studentId = $item["student_id"];
			$complexTypeId = $item["complex_type_id"] ?? null;

			// delete attendance if the user has removed an entry
			if ($complexTypeId === null) {
				$studentIdsToDelete[] = $studentId;
				continue;
			}

			$studentIdsToKeep[] = $studentId;

			$upsertData[] = [
				"lesson_id" => $lesson->id,
				"student_id" => $studentId,
				"attendance_complex_type_id" => $complexTypeId,
				"employee_id" => $employee->id,
				"created_at" => $now,
				"updated_at" => $now,
			];
		}

		DB::transaction(function () use ($studentIdsToKeep, $gradebookStudentIds, $lesson, $upsertData, $studentIdsToDelete) {
			Attendance::where("lesson_id", $lesson->id)
				->whereIn("student_id", $gradebookStudentIds)
				->where(function ($query) use ($studentIdsToKeep, $studentIdsToDelete) {
					$query->whereIn("student_id", $studentIdsToDelete);

					if (!empty($studentIdsToKeep)) {
						$query->orWhereNotIn("student_id", $studentIdsToKeep);
					}
				})
				->delete();

			if (!empty($upsertData)) {
				Attendance::upsert(
					$upsertData,
					["lesson_id", "student_id"], // which fields are unique?
					["attendance_complex_type_id", "employee_id", "updated_at"] // if the entry already exists, update these.
				);
			}
		});

		return response()->json([
			"success" => true
		], 201);
	}

	public function autofill(Gradebook $gradebook, Lesson $lesson)
	{
		$this->authorize("editAttendance", [$lesson]);

		$previousLessons = Lesson::where("subject_id", $lesson->subject_id)
			->whereHas("gradebooks", fn($q) => $q->whereIn("gradebook_id", $lesson->gradebooks->pluck("id")))
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

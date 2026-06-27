<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Models\Gradebook;
use App\Models\Lesson;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

class LessonController extends Controller
{
	public function list(Request $request, Gradebook $gradebook)
	{
		$lessons = $gradebook->lessons()
			->with(["gradebookGroups", "primaryTeacher", "subject", "assistingTeachers"]);
		$lessons = $this->applyFilters($request, $lessons);

		return $lessons->get()->toResourceCollection();
	}

	public function create(LessonRequest $request)
	{
		$validated = $request->validated();

		$lesson = \DB::transaction(function () use ($validated) {
			$lesson = new Lesson();

			$lesson = $this->saveLessonDetails($validated, $lesson);
			$lesson->gradebooks()->sync($validated["gradebookIds"]);
			$this->authorize("create", [
				$lesson
			]);

			if (!empty($validated["assistingTeachers"])) {
				$lesson->assistingTeachers()->attach($validated["assistingTeachers"]);
			}

			$lesson->gradebookGroups()->attach($validated["groups"]);

			return $lesson;
		});

		return \Response::json([
			"success" => true,
			"lessonId" => $lesson->id
		], 201);
	}

	public function update(LessonRequest $request, Lesson $lesson)
	{
		$this->authorize("update", [$lesson]);

		$validated = $request->validated();
		if ($validated["primaryTeacherId"] != $lesson->primary_teacher_id) {
			return response()->json([
				"success" => false,
				"errors" => ["CHANGING_THE_PRIMARY_TEACHER_IS_FORBIDDEN"]
			]);
		}

		\DB::transaction(function () use ($validated, $lesson) {
			$lesson = $this->saveLessonDetails($validated, $lesson);
			$lesson->gradebooks()->sync($validated["gradebookIds"]);
			$lesson->assistingTeachers()->sync($validated["assistingTeachers"]);
			$lesson->gradebookGroups()->sync($validated["groups"]);
		});

		return [
			"success" => true
		];
	}

	/**
	 * @throws \Throwable
	 */
	private function saveLessonDetails(array $validated, Lesson $lesson): Lesson
	{
		$lesson->number = $validated["number"];
		$lesson->primary_teacher_id = $validated["primaryTeacherId"];
		$lesson->subject_id = $validated["subjectId"];
		$lesson->topic = $validated["topic"];
		$lesson->date = $validated["date"];
		$lesson->start_time = $validated["startTime"];
		$lesson->end_time = $validated["endTime"];
		if (isset($validated["completed"])) {
			$lesson->completed = $validated["completed"];
		} else {
			$lesson->completed = false;
		}
		$lesson->saveOrFail();
		return $lesson;
	}

	public function markAsCompleted(Lesson $lesson)
	{
		$this->authorize("markAsCompleted", [$lesson]);

		if (!$lesson->completed) {
			$lesson->completed = true;
			$lesson->save();
		}

		return [
			"success" => true
		];
	}

	public function getStudentLessons(Request $request, Gradebook $gradebook)
	{
		$student = Student::getStudentFromAccessId($request);
		$lessons = $gradebook->lessons()->with(["primaryTeacher", "subject", "assistingTeachers"])
			->whereHas("gradebookGroups", function ($groupQuery) use ($student) {
				$groupQuery->whereHas("students", function ($query) use ($student) {
					$query->where("student_id", $student->id);
				});
			});
		$lessons = $this->applyFilters($request, $lessons);

		return $lessons->get()->map(fn(Lesson $lesson) => [
			"id" => $lesson->id,
			"number" => $lesson->number,
			"topic" => $lesson->topic,
			"date" => $lesson->date,
			"startTime" => $lesson->start_time,
			"endTime" => $lesson->end_time,
			"completed" => $lesson->completed,
			"primaryTeacher" => $lesson->primaryTeacher->first_name . " " . $lesson->primaryTeacher->last_name,
			"subject" => $lesson->subject->name,
			"assistingTeachers" => $lesson->assistingTeachers->map(fn($teacher) => $teacher->first_name . " " . $teacher->last_name)
		]);
	}

	private function applyFilters(Request $request, Builder|BelongsToMany $query): Builder|BelongsToMany
	{
		if ($request->has("dateFrom")) {
			$query = $query->whereDate("date", ">=", $request->input("dateFrom"));
		}

		if ($request->has("dateTo")) {
			$query = $query->whereDate("date", "<=", $request->input("dateTo"));
		}

		if ($request->has("completed")) {
			$query = $query->where("completed", "=", $request->input("completed"));
		}

		if ($request->has("subjectId")) {
			$query = $query->where("subject_id", "=", $request->input("subjectId"));
		}

		if ($request->has("primaryTeacherId")) {
			$query = $query->where("primary_teacher_id", "=", $request->input("primaryTeacherId"));
		}

		if ($request->has("topic")) {
			$query = $query->where("topic", "like", "%" . $request->input("topic") . "%");
		}

		return $query;
	}
}

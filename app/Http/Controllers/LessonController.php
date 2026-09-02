<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Http\Resources\StudentLessonResource;
use App\Models\Gradebook;
use App\Models\Lesson;
use App\Services\AccessContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

class LessonController extends Controller
{
	public function list(Request $request, Gradebook $gradebook)
	{
		$lessons = $gradebook->lessons()
			->with(["gradebookGroups.gradebook.classUnit", "gradebooks.classUnit", "primaryTeacher", "subject", "assistingTeachers"]);
		$lessons = $this->applyFilters($request, $lessons);

		return $lessons->get()->toResourceCollection();
	}

	public function create(LessonRequest $request)
	{
		$validated = $request->validated();

		if (!isset($validated["number"])) {
			$validated["number"] = Lesson::where("subject_id", $validated["subjectId"])
					->whereHas("gradebooks", fn($q) => $q->whereIn("gradebooks.id", $validated["gradebookIds"]))
					->whereHas("gradebookGroups", fn($q) => $q->whereIn("gradebook_groups.id", $validated["groups"]))
					->max("number") + 1;
		}

		$lesson = \DB::transaction(function () use ($request, $validated) {
			$lesson = new Lesson();

			$lesson = $this->saveLessonDetails($request, $validated, $lesson);
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

		if (!isset($validated["number"])) {
			$validated["number"] = $lesson->number;
		}

		\DB::transaction(function () use ($request, $validated, $lesson) {
			$lesson = $this->saveLessonDetails($request, $validated, $lesson);
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
	private function saveLessonDetails(Request $request, array $validated, Lesson $lesson): Lesson
	{
		$lesson->number = $validated["number"];
		$lesson->primary_teacher_id = app(AccessContext::class)->currentEmployee()->id;
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
		$student = app(AccessContext::class)->currentStudent();
		$lessons = $gradebook->lessons()->with(["primaryTeacher", "subject", "assistingTeachers", "attendances", "attendances.employee"])
			->whereHas("gradebookGroups", function ($groupQuery) use ($student) {
				$groupQuery->whereHas("students", function ($query) use ($student) {
					$query->where("student_id", $student->id);
				});
			});
		$lessons = $this->applyFilters($request, $lessons);

		return StudentLessonResource::collection($lessons->get());
	}

	private function applyFilters(Request $request, Builder|BelongsToMany $query): Builder|BelongsToMany
	{
		return $query
			->when($request->has("dateFrom"), fn($query) => $query->dateFrom($request->input("dateFrom")))
			->when($request->has("dateTo"), fn($query) => $query->dateTo($request->input("dateTo")))
			->when($request->has("completed"), fn($query) => $query->completed($request->input("completed")))
			->when($request->has("subjectId"), fn($query) => $query->forSubject($request->input("subjectId")))
			->when($request->has("primaryTeacherId"), fn($query) => $query->forPrimaryTeacher($request->input("primaryTeacherId")))
			->when($request->has("assistingTeacherId"), fn($query) => $query->forAssistingTeacher($request->input("assistingTeacherId")))
			->when($request->has("topic"), fn($query) => $query->topicLike($request->input("topic")));
	}
}

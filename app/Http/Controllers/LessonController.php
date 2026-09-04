<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Http\Resources\StudentLessonResource;
use App\Models\Gradebook;
use App\Models\Lesson;
use App\Models\LessonGradebook;
use App\Services\AccessContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

class LessonController extends Controller
{
	public function list(Request $request, Gradebook $gradebook)
	{
		$lessons = Lesson::whereHas("gradebooks.gradebook", fn($q) => $q->where("gradebooks.id", $gradebook->id))
			->with([
				"subject",
				"primaryTeacher",
				"assistingTeachers",
				"gradebooks.gradebook.classUnit",
				"gradebooks.groups.gradebookGroup",
			]);
		$lessons = $this->applyFilters($request, $lessons);

		return $lessons->get()->toResourceCollection();
	}

	public function create(LessonRequest $request)
	{
		$validated = $request->validated();
		$employee = app(AccessContext::class)->currentEmployee();

		$lesson = \DB::transaction(function () use ($validated, $employee) {
			if (!isset($validated["number"])) {
				$validated["number"] = $this->nextNumber($validated);
			}

			$lesson = new Lesson();
			$lesson->number = $validated["number"];
			$lesson->primary_teacher_id = $employee->id;
			$lesson->subject_id = $validated["subjectId"];
			$lesson->topic = $validated["topic"];
			$lesson->date = $validated["date"];
			$lesson->start_time = $validated["startTime"];
			$lesson->end_time = $validated["endTime"];
			$lesson->completed = $validated["completed"] ?? false;
			$lesson->saveOrFail();

			$this->authorize("create", [$lesson]);

			foreach ($validated["gradebooks"] as $entry) {
				$lessonGradebook = LessonGradebook::create([
					"lesson_id" => $lesson->id,
					"gradebook_id" => $entry["id"],
				]);

				foreach ($entry["groupIds"] ?? [] as $groupId) {
					$lessonGradebook->groups()->create([
						"gradebook_group_id" => $groupId,
					]);
				}
			}

			if (!empty($validated["assistingTeachers"])) {
				$lesson->assistingTeachers()->attach($validated["assistingTeachers"]);
			}

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

		\DB::transaction(function () use ($validated, $lesson) {
			$lesson->number = $validated["number"];
			$lesson->subject_id = $validated["subjectId"];
			$lesson->topic = $validated["topic"];
			$lesson->date = $validated["date"];
			$lesson->start_time = $validated["startTime"];
			$lesson->end_time = $validated["endTime"];
			if (array_key_exists("completed", $validated)) {
				$lesson->completed = $validated["completed"];
			}
			$lesson->saveOrFail();

			$lesson->gradebooks()->delete();
			foreach ($validated["gradebooks"] as $entry) {
				$lessonGradebook = LessonGradebook::create([
					"lesson_id" => $lesson->id,
					"gradebook_id" => $entry["id"],
				]);

				foreach ($entry["groupIds"] ?? [] as $groupId) {
					$lessonGradebook->groups()->create([
						"gradebook_group_id" => $groupId,
					]);
				}
			}

			$lesson->assistingTeachers()->sync($validated["assistingTeachers"] ?? []);
		});

		return [
			"success" => true
		];
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
		$lessons = Lesson::whereHas("gradebooks.gradebook", fn($q) => $q->where("gradebooks.id", $gradebook->id))
			->whereHas("gradebooks.groups.gradebookGroup.students", function ($query) use ($student) {
				$query->where("students.id", $student->id);
			})
			->with(["primaryTeacher", "subject", "assistingTeachers", "attendances", "attendances.employee"]);
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

	private function nextNumber(array $validated): int
	{
		$gradebookIds = collect($validated["gradebooks"])->pluck("id")->all();
		$groupIds = collect($validated["gradebooks"])->flatMap(fn($g) => $g["groupIds"] ?? [])->all();

		return (int) Lesson::where("subject_id", $validated["subjectId"])
			->whereHas("gradebooks", fn($q) => $q->whereIn("gradebook_id", $gradebookIds))
			->whereHas("gradebooks.groups.gradebookGroup", fn($q) => $q->whereIn("gradebook_groups.id", $groupIds))
			->max("number") + 1;
	}
}

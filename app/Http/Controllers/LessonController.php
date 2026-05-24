<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Models\Gradebook;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
	public function list(Request $request, Gradebook $gradebook) {
		$lessons = $gradebook->lessons()
			->with(["gradebookGroups", "primaryTeacher", "subject", "assistingTeachers"])
			->where("gradebook_id", $gradebook->id);

		if ($request->has("dateFrom")) {
			$lessons = $lessons->whereDate("date", ">=", $request->input("dateFrom"));
		}

		if ($request->has("dateTo")) {
			$lessons = $lessons->whereDate("date", "<=", $request->input("dateTo"));
		}

		if ($request->has("completed")) {
			$lessons = $lessons->where("completed", "=", $request->input("completed"));
		}

		if ($request->has("subjectId")) {
			$lessons = $lessons->where("subject_id", "=", $request->input("subjectId"));
		}

		if ($request->has("primaryTeacherId")) {
			$lessons = $lessons->where("primary_teacher_id", "=", $request->input("primaryTeacherId"));
		}

		if ($request->has("topic")) {
			$lessons = $lessons->where("topic", "like", "%" . $request->input("topic") . "%");
		}

		return $lessons->get()->toResourceCollection();
	}

	public function create(LessonRequest $request)
	{
		$validated = $request->validated();
		$lesson = \DB::transaction(function () use ($validated) {
			$lesson = new Lesson();
			$lesson = $this->saveLessonDetails($validated, $lesson);

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
		$validated = $request->validated();

		\DB::transaction(function () use ($validated, $lesson) {
			$lesson = $this->saveLessonDetails($validated, $lesson);
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
		$lesson->gradebook_id = $validated["gradebookId"];
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
		if (!$lesson->completed) {
			$lesson->completed = true;
			$lesson->save();
		}

		return [
			"success" => true
		];
	}
}

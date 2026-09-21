<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Models\Lesson;
use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LessonRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$subjectId = $this->input("subjectId");
		$gradebooks = $this->input("gradebooks", []);
		$gradebookIds = collect($gradebooks)->pluck("id")->all();
		$lessonId = $this->route("lesson")?->id;

		return [
			"number" => [
				"nullable",
				"integer",
				function (string $attribute, mixed $value, \Closure $fail) use ($subjectId, $gradebooks, $lessonId) {
					$groupIds = collect($gradebooks)->flatMap(fn($g) => $g["groupIds"] ?? [])->all();

					$query = Lesson::where("number", $value)
						->where("subject_id", $subjectId)
						->whereHas("gradebooks", fn($q) => $q->whereIn("gradebook_id", collect($gradebooks)->pluck("id")->all()))
						->whereHas("gradebooks.groups.gradebookGroup", fn($q) => $q->whereIn("gradebook_groups.id", $groupIds));

					if ($lessonId) {
						$query->where("id", "!=", $lessonId);
					}

					if ($query->exists()) {
						$fail("LESSON_NUMBER_ALREADY_EXISTS");
					}
				}
			],
			"gradebooks" => ["required", "array", "min:1"],
			"gradebooks.*.id" => [
				"required",
				"exists:gradebooks,id",
				function (string $attribute, mixed $value, \Closure $fail) {
					$index = (int) explode(".", explode("*", $attribute)[0])[1];
					$groupIds = $this->input("gradebooks.$index.groupIds", []);

					if (empty($groupIds)) {
						return;
					}

					$invalid = \App\Models\GradebookGroup::whereIn("id", $groupIds)
						->where("gradebook_id", "!=", $value)
						->exists();

					if ($invalid) {
						$fail("GROUPS_DO_NOT_BELONG_TO_GRADEBOOK");
					}
				}
			],
			"gradebooks.*.groupIds" => ["array"],
			"gradebooks.*.groupIds.*" => ["exists:gradebook_groups,id"],
			"subjectId" => [
				"required",
				"exists:subjects,id",
				Rule::exists("subjects", "id")->where("active", true)
			],
			"topic" => ["required", "string", "max:255"],
			"date" => ["required", "date"],
			"startTime" => ["required", "date_format:H:i"],
			"endTime" => ["required", "date_format:H:i", "after:startTime"],
			"completed" => ["nullable", "boolean"],

			"assistingTeachers" => ["nullable", "array"],
			"assistingTeachers.*" => [
				"exists:employees,id",
				Rule::exists("employees", "id")->where("active", true)
			],
		];
	}
}

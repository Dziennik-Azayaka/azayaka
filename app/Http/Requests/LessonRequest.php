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
		$gradebookIds = $this->input("gradebookIds", []);
		$lessonId = $this->route("lesson")?->id;

		return [
			"number" => [
				"nullable",
				"integer",
				function (string $attribute, mixed $value, \Closure $fail) use ($subjectId, $gradebookIds, $lessonId) {
					$query = Lesson::where("number", $value)
						->where("subject_id", $subjectId)
						->whereHas("gradebooks", fn($q) => $q->whereIn("gradebooks.id", $gradebookIds))
						->whereHas("gradebookGroups", fn($q) => $q->where("gradebook_groups.id", $this->input("groups")));

					if ($lessonId) {
						$query->where("id", "!=", $lessonId);
					}

					if ($query->exists()) {
						$fail("LESSON_NUMBER_ALREADY_EXISTS");
					}
				}
			],
			"gradebookIds" => ["required", "array", "min:1"],
			"gradebookIds.*" => ["exists:gradebooks,id"],
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
			"groups" => ["required", "array"],
			"groups.*" => ["exists:gradebook_groups,id"],
		];
	}
}

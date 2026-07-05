<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Models\Lesson;
use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;

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
				function (string $attribute, mixed $value, \Closure $fail) {
					$subject = Subject::find($value);
					if ($subject && !$subject->active) {
						$fail("SUBJECT_NOT_ACTIVE");
					}
				}
			],
			"topic" => ["required", "string", "max:255"],
			"date" => ["required", "date"],
			"startTime" => ["required", "date_format:H:i"],
			"endTime" => ["required", "date_format:H:i", "after:startTime"],
			"completed" => ["nullable", "boolean"],

			"assistingTeachers" => ["nullable", "array"],
			"assistingTeachers.*" => [
				"exists:employees,id",
				function (string $attribute, mixed $value, \Closure $fail) {
					$employee = Employee::find($value);
					if ($employee && !$employee->active) {
						$fail("ASSISTING_TEACHER_NOT_ACTIVE");
					}
				}
			],
			"groups" => ["required", "array"],
			"groups.*" => ["exists:gradebook_groups,id"],
		];
	}
}

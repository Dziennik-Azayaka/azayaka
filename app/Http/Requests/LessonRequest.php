<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
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
		$uniqueNumberRule = Rule::unique("lessons")
			->where(fn(Builder $builder) => $builder->where("gradebook_id", $this->request->get("gradebookId")))
			->where(fn(Builder $builder) => $builder->where("subject_id", $this->request->get("subjectId")));

		// if editing, ignore the current lesson
		if ($this->route("lesson")) {
			$uniqueNumberRule = $uniqueNumberRule->ignore($this->route("lesson")->id);
		}

		return [
			"number" => [
				"required",
				"integer",
				$uniqueNumberRule
			],
			"gradebookId" => ["required", "exists:gradebooks,id"],
			"subjectId" => ["required", "exists:subjects,id"],
			"primaryTeacherId" => ["required", "exists:employees,id"],
			"topic" => ["required", "string", "max:255"],
			"date" => ["required", "date"],
			"startTime" => ["required", "date_format:H:i"],
			"endTime" => ["required", "date_format:H:i", "after:startTime"],
			"completed" => ["nullable", "boolean"],

			"assistingTeachers" => ["nullable", "array"],
			"assistingTeachers.*" => ["exists:employees,id"],
			"groups" => ["required", "array"],
			"groups.*" => ["exists:gradebook_groups,id"],
		];
	}
}

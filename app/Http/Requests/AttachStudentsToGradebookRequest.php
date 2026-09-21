<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttachStudentsToGradebookRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			"studentIds" => ["required", "array", "min:1", "max:255"],
			"studentIds.*" => ["required", "distinct", "exists:students,id"],
			"positions" => ["required", "array", "min:1", "max:255"],
			"positions.*" => ["required", "distinct", "integer", "min:1", "max:255"],
		];
	}

	public function withValidator(Validator $validator): void
	{
		$validator->after(function (Validator $validator) {
			if ($validator->errors()->isNotEmpty()) {
				return;
			}

			$studentIds = $this->input("studentIds");
			$positions = $this->input("positions");

			if (count($studentIds) !== count($positions)) {
				throw new HttpResponseException(\Response::json([
					"success" => false,
					"errors" => ["NUMBER_OF_STUDENTS_AND_POSITIONS_DO_NOT_MATCH"]
				], 422));
			}

			$gradebook = $this->route("gradebook");
			$schoolUnitId = $gradebook->classUnit->school_unit_id;
			$eligibleStudentIds = Student::whereHas("studentRegistry", function ($query) use ($schoolUnitId) {
				$query->where("school_unit_id", $schoolUnitId);
			})->whereIn("id", $studentIds)->pluck("id")->toArray();

			$invalidStudentIds = array_diff($studentIds, $eligibleStudentIds);
			if (!empty($invalidStudentIds)) {
				throw new HttpResponseException(\Response::json([
					"success" => false,
					"errors" => ["STUDENTS_NOT_IN_SCHOOL_UNIT"]
				], 422));
			}
		});
	}
}

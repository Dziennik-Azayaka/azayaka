<?php

namespace App\Http\Requests;

use App\Exceptions\EntityAlreadyExistsException;
use App\Models\ClassUnit;
use App\Models\Gradebook;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateGradebookRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			"classificationPeriodId" => ["required", "exists:classification_periods,id"],
			"classUnitId" => ["required", "exists:class_units,id"],
			"level" => ["nullable", "integer"],
		];
	}

	public function withValidator(Validator $validator): void
	{
		$validator->after(function (Validator $validator) {
			if ($validator->errors()->isNotEmpty()) {
				return;
			}

			$classUnit = ClassUnit::findOrFail($this->input("classUnitId"));
			$level = $this->input("level");

			if ($level !== null) {
				$validLevels = $classUnit->periods()->pluck("class_units_periods.level")->toArray();
				if (!in_array($level, $validLevels)) {
					throw new HttpResponseException(\Response::json([
						"success" => false,
						"errors" => ["LEVEL_OUTSIDE_CLASS_UNIT_RANGE"]
					], 422));
				}

				$existingLevelGradebooks = Gradebook::where("class_unit_id", $this->input("classUnitId"))
					->where(function ($query) use ($level) {
						$query->where("level", $level)
							->orWhereNull("level");
					})
					->get()
					->filter(fn($gradebook) => $gradebook->level === $level)
					->count();

				if ($existingLevelGradebooks > 0) {
					throw new EntityAlreadyExistsException("GRADEBOOK");
				}
			}

			if (Gradebook::where("classification_period_id", $this->input("classificationPeriodId"))
				->where("class_unit_id", $this->input("classUnitId"))
				->where(function ($query) use ($level) {
					if ($level !== null) {
						$query->where("level", $level);
					} else {
						$query->whereNull("level");
					}
				})
				->exists()) {
				throw new EntityAlreadyExistsException("GRADEBOOK");
			}
		});
	}
}

<?php

namespace App\Http\Controllers;

use App\Models\ClassificationPeriodDefaults;
use App\Utilities\ClassificationPeriodValidator;
use App\Utilities\ValidatorAssistant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassificationPeriodDefaultsController extends Controller
{
	public function list(Request $request)
	{
		$schoolYear = $request->get("schoolYear");
		if (!$schoolYear) {
			$now = Carbon::now();
			if ($now->month > 8) {
				$schoolYear = $now->year;
			} else {
				$schoolYear = $now->year - 1;
			}
		}
		return ClassificationPeriodDefaults::where("school_year", $schoolYear)
			->get()
			->toResourceCollection()
			->groupBy("school_unit_id");
	}

	public function save(Request $request, Int $schoolYear, Int $schoolUnitId)
	{
		$validator = ValidatorAssistant::validate($request, [
			"periodStart" => ["required", "array"],
			"periodEnd" => ["required", "array"]
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];

		$classificationPeriodValidatorResponse = ClassificationPeriodValidator::validate($validated);
		if ($classificationPeriodValidatorResponse) {
			return $classificationPeriodValidatorResponse;
		}

		$newDefaults = [];

		// laravel does not automatically generate timestamps when bulk inserting
		$now = Carbon::now();

		foreach ($validated["periodStart"] as $key => $periodStart) {
			$newDefaults[] = [
				"school_year" => $schoolYear,
				"school_unit_id" => $schoolUnitId,
				"period_start" => $periodStart,
				"period_end" => $validated["periodEnd"][$key],
				"period_number" => $key + 1,
				"created_at" => $now,
				"updated_at" => $now,
			];
		}

		ClassificationPeriodDefaults::where("school_year", $schoolYear)
			->where("school_unit_id", $schoolUnitId)
			->delete();

		if (!empty($newDefaults)) {
			ClassificationPeriodDefaults::insert($newDefaults);
		}

		return [
			"success" => true
		];
	}
}

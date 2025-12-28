<?php

namespace App\Http\Controllers;

use App\Models\ClassificationPeriodDefaults;
use App\Utilities\ClassificationPeriodAssistant;
use App\Utilities\ValidatorAssistant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassificationPeriodDefaultsController extends Controller
{
	public function list(Request $request)
	{
		$schoolYear = $request->get("schoolYear");
		if (!$schoolYear) {
			$schoolYear = ClassificationPeriodAssistant::getCurrentSchoolYear();
		}
		return ClassificationPeriodDefaults::where("school_year", $schoolYear)
			->get()
			->toResourceCollection()
			->groupBy("school_unit_id");
	}

	public function save(Request $request, Int $schoolYear, Int $schoolUnitId)
	{
		$validator = ValidatorAssistant::validate($request, [
			"periodEnd" => ["required", "array"]
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];

		$classificationPeriodValidatorResponse = ClassificationPeriodAssistant::validate($validated["periodEnd"]);
		if ($classificationPeriodValidatorResponse) {
			return $classificationPeriodValidatorResponse;
		}

		$newDefaults = [];

		// laravel does not automatically generate timestamps when bulk inserting
		$now = Carbon::now();

		foreach ($validated["periodEnd"] as $key => $periodEnd) {
			if ($key == 0) {
				$periodStart = "{$schoolYear}-09-01";
			} else {
				$periodStart = Carbon::parse($validated["periodEnd"][$key - 1])->addDay()->toDateString();
			}

			$newDefaults[] = [
				"school_year" => $schoolYear,
				"school_unit_id" => $schoolUnitId,
				"period_start" => $periodStart,
				"period_end" => $periodEnd,
				"period_number" => $key + 1,
				"created_at" => $now,
				"updated_at" => $now,
			];
		}

		$newDefaults[] = [
			"school_year" => $schoolYear,
			"school_unit_id" => $schoolUnitId,
			"period_start" => Carbon::parse(end($newDefaults)["period_end"])->addDay()->toDateString(),
			"period_end" => $schoolYear + 1 . "-08-31",
			"period_number" => count($newDefaults) + 1,
			"created_at" => $now,
			"updated_at" => $now,
		];

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

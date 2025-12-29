<?php

namespace App\Http\Controllers;

use App\Models\ClassificationPeriod;
use App\Utilities\ClassificationPeriodAssistant;
use App\Utilities\ValidatorAssistant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassificationPeriodController extends Controller
{
	public function list(Int $classUnitId, Int $schoolYear)
	{
		return ClassificationPeriod::where("class_unit_id", $classUnitId)
			->where("school_year", $schoolYear)
			->get()
			->toResourceCollection();
	}

	public function save(Request $request, Int $classUnitId, Int $schoolYear)
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

		$newPeriodsForClass = [];

		// laravel does not automatically generate timestamps when bulk inserting
		$now = Carbon::now();

		foreach ($validated["periodEnd"] as $key => $periodEnd) {
			if ($key == 0) {
				$periodStart = "{$schoolYear}-09-01";
			} else {
				$periodStart = Carbon::parse($validated["periodEnd"][$key - 1])->addDay()->toDateString();
			}

			$newPeriodsForClass[] = [
				"school_year" => $schoolYear,
				"class_unit_id" => $classUnitId,
				"period_start" => $periodStart,
				"period_end" => $periodEnd,
				"period_number" => $key + 1,
				"created_at" => $now,
				"updated_at" => $now,
			];
		}

		$newPeriodsForClass[] = [
			"school_year" => $schoolYear,
			"class_unit_id" => $classUnitId,
			"period_start" => Carbon::parse(end($newPeriodsForClass)["period_end"])->addDay()->toDateString(),
			"period_end" => $schoolYear + 1 . "-08-31",
			"period_number" => count($newPeriodsForClass) + 1,
			"created_at" => $now,
			"updated_at" => $now,
		];

		ClassificationPeriod::where("school_year", $schoolYear)
			->where("class_unit_id", $classUnitId)
			->delete();

		if (!empty($newPeriodsForClass)) {
			ClassificationPeriod::insert($newPeriodsForClass);
		}

		return [
			"success" => true
		];
	}

	public function delete(Int $classUnitId, Int $schoolYear) {
		// TODO: Implement checks to make sure no grade books have been created for this year
		ClassificationPeriod::where("school_year", $schoolYear)
			->where("class_unit_id", $classUnitId)
			->delete();

		return [
			"success" => true
		];
	}
}

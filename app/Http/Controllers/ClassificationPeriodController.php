<?php

namespace App\Http\Controllers;

use App\Models\ClassificationPeriod;
use App\Utilities\ClassificationPeriodAssistant;
use App\Utilities\ValidatorAssistant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassificationPeriodController extends Controller
{
	public function list(int $schoolUnitId, int $schoolYear)
	{
		return ClassificationPeriod::where("school_unit_id", $schoolUnitId)
			->where("school_year", $schoolYear)
			->get()
			->toResourceCollection();
	}

	public function save(Request $request, int $schoolUnitId, int $schoolYear)
	{
		$validator = ValidatorAssistant::validate($request, [
			"periodEnd" => ["required", "array"]
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];

		$classificationPeriodValidatorResponse = ClassificationPeriodAssistant::validate($validated["periodEnd"], $schoolYear);
		if ($classificationPeriodValidatorResponse) {
			return $classificationPeriodValidatorResponse;
		}

		$newPeriods = [];

		// laravel does not automatically generate timestamps when bulk inserting
		$now = Carbon::now();

		foreach ($validated["periodEnd"] as $key => $periodEnd) {
			if ($key == 0) {
				$periodStart = "{$schoolYear}-09-01";
			} else {
				$periodStart = Carbon::parse($validated["periodEnd"][$key - 1])->addDay()->toDateString();
			}

			$newPeriods[] = [
				"school_year" => $schoolYear,
				"school_unit_id" => $schoolUnitId,
				"period_start" => $periodStart,
				"period_end" => $periodEnd,
				"period_number" => $key + 1,
				"created_at" => $now,
				"updated_at" => $now,
			];
		}

		$newPeriods[] = [
			"school_year" => $schoolYear,
			"school_unit_id" => $schoolUnitId,
			"period_start" => Carbon::parse(end($newPeriods)["period_end"])->addDay()->toDateString(),
			"period_end" => $schoolYear + 1 . "-08-31",
			"period_number" => count($newPeriods) + 1,
			"created_at" => $now,
			"updated_at" => $now,
		];

		ClassificationPeriod::where("school_year", $schoolYear)
			->where("school_unit_id", $schoolUnitId)
			->delete();

		if (!empty($newPeriods)) {
			ClassificationPeriod::insert($newPeriods);
		}

		return [
			"success" => true
		];
	}

	public function delete(int $schoolUnitId, int $schoolYear)
	{
		// TODO: Implement checks to make sure no grade books have been created for this year
		ClassificationPeriod::where("school_year", $schoolYear)
			->where("school_unit_id", $schoolUnitId)
			->delete();

		return [
			"success" => true
		];
	}
}

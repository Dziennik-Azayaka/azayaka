<?php

namespace App\Http\Controllers;

use App\Models\ClassificationPeriod;
use App\Utilities\ClassificationPeriodAssistant;
use App\Utilities\ValidatorAssistant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassificationPeriodController extends Controller
{
	public function list(Request $request)
	{
		return ClassificationPeriod::all()
			->toResourceCollection()
			->groupBy("class_unit_id");
	}

	public function save(Request $request, Int $schoolYear, Int $classUnitId)
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
			$newPeriodsForClass[] = [
				"school_year" => $schoolYear,
				"class_unit_id" => $classUnitId,
				"period_start" => Carbon::parse($periodEnd)->subDay()->toDateString(),
				"period_end" => $periodEnd,
				"period_number" => $key + 1,
				"created_at" => $now,
				"updated_at" => $now,
			];
		}

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

	public function delete(Int $schoolYear, Int $classUnitId) {
		// TODO: Implement checks to make sure no grade books have been created for this year
		ClassificationPeriod::where("school_year", $schoolYear)
			->where("class_unit_id", $classUnitId)
			->delete();

		return [
			"success" => true
		];
	}
}

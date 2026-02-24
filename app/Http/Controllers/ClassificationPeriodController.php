<?php

namespace App\Http\Controllers;

use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\ClassUnitPeriod;
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

		ClassificationPeriod::where("school_year", $schoolYear)
			->where("school_unit_id", $schoolUnitId)
			->delete();

		$newPeriods = [];

		foreach ($validated["periodEnd"] as $key => $periodEnd) {
			if ($key == 0) {
				$periodStart = "{$schoolYear}-09-01";
			} else {
				$periodStart = Carbon::parse($validated["periodEnd"][$key - 1])->addDay()->toDateString();
			}

			$period = ClassificationPeriod::create([
				"school_year" => $schoolYear,
				"school_unit_id" => $schoolUnitId,
				"period_start" => $periodStart,
				"period_end" => $periodEnd,
				"period_number" => $key + 1
			]);
			$period->save();
			$newPeriods[] = $period;
		}

		$finalPeriod = ClassificationPeriod::create([
			"school_year" => $schoolYear,
			"school_unit_id" => $schoolUnitId,
			"period_start" => Carbon::parse(end($newPeriods)["period_end"])->addDay()->toDateString(),
			"period_end" => $schoolYear + 1 . "-08-31",
			"period_number" => count($newPeriods) + 1,
		]);
		$finalPeriod->save();
		$newPeriods[] = $finalPeriod;

		$pivotEntries = [];
		// Classification periods have been created, create appropriate records for class_units_periods
		ClassUnit::where("school_unit_id", $schoolUnitId)->with("periods")->get()->each(
			function (ClassUnit $classUnit) use ($newPeriods, $schoolYear, &$pivotEntries) {
				if ($classUnit->current_level >= $classUnit->teaching_cycle_length) return;
				if ($classUnit->promote_every == "year") {
					for ($i = 0; $i < count($newPeriods); $i++) {
						$pivotEntries[] = [
							"class_unit_id" => $classUnit->id,
							"classification_period_id" => $newPeriods[$i]->id,
							"level" => $classUnit->current_level + 1
						];
					}
				} else {
					$level = $classUnit->current_level;
					for ($i = 0; $i < count($newPeriods); $i++) {
						$level++;
						$pivotEntries[] = [
							"class_unit_id" => $classUnit->id,
							"classification_period_id" => $newPeriods[$i]->id,
							"level" => $level
						];
					}
				}
			}
		);

		ClassUnitPeriod::insert($pivotEntries);

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

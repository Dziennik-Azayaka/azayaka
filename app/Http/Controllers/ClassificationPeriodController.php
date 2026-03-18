<?php

namespace App\Http\Controllers;

use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\ClassUnitPeriod;
use App\Utilities\ClassificationPeriodAssistant;
use App\Utilities\ValidatorAssistant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

		$oldClassificationPeriodIds = ClassificationPeriod::where("school_year", $schoolYear)
			->where("school_unit_id", $schoolUnitId)
			->pluck("id");

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

		$futurePeriods = ClassificationPeriod::where("school_year", ">", $schoolYear)
			->where("school_unit_id", $schoolUnitId)
			->orderBy("school_year")
			->orderBy("period_start")
			->get();

		$futurePeriodIds = $futurePeriods->pluck("id");

		$pivotEntries = [];
		$processedClassUnitIds = [];

		ClassUnit::where("school_unit_id", $schoolUnitId)->with("periods")->get()->each(
			function (ClassUnit $classUnit) use (
				$newPeriods, $schoolYear, &$pivotEntries, $futurePeriods, &$processedClassUnitIds
			) {
				$processedClassUnitIds[] = $classUnit->id;

				// Update starting period
				if ($classUnit->startingPeriod && $classUnit->startingPeriod->school_year == $schoolYear) {
					foreach ($newPeriods as $newPeriod) {
						if ($classUnit->startingPeriod->period_start->between($newPeriod->period_start, $newPeriod->period_end)) {
							$classUnit->starting_classification_period_id = $newPeriod->id;
							$classUnit->save();
							break;
						}
					}
				}

				$level = $classUnit->currentPeriodEntry(Carbon::create($schoolYear, 8, 31))?->pivot?->level ?? 0;

				if ($level >= $classUnit->teaching_cycle_length) return;

				if ($classUnit->promote_every == "year") {
					foreach ($newPeriods as $newPeriod) {
						$pivotEntries[] = [
							"class_unit_id" => $classUnit->id,
							"classification_period_id" => $newPeriod->id,
							"level" => $level + 1
						];
					}
				} else {
					foreach ($newPeriods as $newPeriod) {
						$level++;
						$pivotEntries[] = [
							"class_unit_id" => $classUnit->id,
							"classification_period_id" => $newPeriod->id,
							"level" => $level
						];
					}

					foreach ($futurePeriods as $futurePeriod) {
						$level++;
						$pivotEntries[] = [
							"class_unit_id" => $classUnit->id,
							"classification_period_id" => $futurePeriod->id,
							"level" => $level
						];
					}
				}
			}
		);

		try {
			DB::transaction(function () use ($oldClassificationPeriodIds, $futurePeriodIds, $processedClassUnitIds, $schoolYear, $schoolUnitId, $pivotEntries) {
				ClassUnitPeriod::whereIn("classification_period_id", $oldClassificationPeriodIds)->delete();
				ClassUnitPeriod::whereIn("classification_period_id", $futurePeriodIds)
					->whereIn("class_unit_id", $processedClassUnitIds)
					->delete();

				ClassificationPeriod::whereIn("id", $oldClassificationPeriodIds)->delete();

				foreach (array_chunk($pivotEntries, 500) as $chunk) {
					ClassUnitPeriod::insert($chunk);
				}
			});
		} catch (\Throwable) {
			return \Response::json([
				"success" => false,
				"errors" => ["INTERNAL_SERVER_ERROR"]
			], 500);
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

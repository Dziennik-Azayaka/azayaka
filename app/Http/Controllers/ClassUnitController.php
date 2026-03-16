<?php

namespace App\Http\Controllers;

use App\Enums\ClassUnitCategory;
use App\Http\Resources\ClassUnitResource;
use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\ClassUnitPeriod;
use App\Models\Employee;
use App\Models\SchoolUnit;
use App\Utilities\ClassificationPeriodAssistant;
use App\Utilities\ValidatorAssistant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassUnitController extends Controller
{
	public function list(Request $request, int|string $schoolUnitId)
	{
		if ($schoolUnitId === "all") {
			$classUnits = ClassUnit::query();
		} else {
			$classUnits = ClassUnit::where("school_unit_id", $schoolUnitId);
		}
		$currentSchoolYear = ClassificationPeriodAssistant::getCurrentSchoolYear();

		$category = $request->get("category");
		if (isset($category) && !in_array($category, ClassUnitCategory::cases(), true)) {
			$category = ClassUnitCategory::from($request->get("category"));
			$classUnits->filterByCategory($category, $currentSchoolYear);
		}
		return $classUnits->get()->toResourceCollection();
	}

	public function create(Request $request, int $schoolUnitId)
	{
		if (!SchoolUnit::where("id", $schoolUnitId)->exists()) {
			return response()->json([
				"success" => false,
				"errors" => [
					"SCHOOL_UNIT_NOT_FOUND"
				]
			]);
		}

		$validator = $this->validateClassUnit($request);
		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$validated = $validator["data"];

		$classUnit = new ClassUnit();
		$classUnit->school_unit_id = $schoolUnitId;
		$classUnit->alias = $validated["alias"];
		$classUnit->mark = $validated["mark"];
		$classUnit->starting_classification_period_id = $validated["startingClassificationPeriodId"];
		$classUnit->teaching_cycle_length = $validated["teachingCycleLength"];
		$classUnit->promote_every = $validated["promoteEvery"];

		$classUnit->save();

		$classUnit->employees()->attach($validated["employeeIds"]);

		$startingPeriod = ClassificationPeriod::find($classUnit->starting_classification_period_id)->first();
		$periods = ClassificationPeriod::where("period_start", ">=", $startingPeriod->period_start);

		if ($classUnit->promote_every == "year") {
			$periods->where("school_year", "<=", $startingPeriod->school_year + $classUnit->teaching_cycle_length - 1);
		} else {
			$periods->orderBy("period_start")->limit($classUnit->teaching_cycle_length);
		}

		$pivotEntries = [];
		$level = 0;
		$periods->get()->each(function ($period) use ($classUnit, $startingPeriod, &$level, &$pivotEntries) {
			if ($classUnit->promote_every == "year") {
				$pivotEntries[] = [
					"class_unit_id" => $classUnit->id,
					"classification_period_id" => $period->id,
					"level" => $period->school_year - $startingPeriod->school_year + 1
				];
			} else {
				$level++;
				$pivotEntries[] = [
					"class_unit_id" => $classUnit->id,
					"classification_period_id" => $period->id,
					"level" => $level
				];
			}
		});

		ClassUnitPeriod::insert($pivotEntries);

		return \Response::json([
			"success" => true
		], 201);
	}

	public function update(Request $request, int $schoolUnitId, ClassUnit $classUnit)
	{
		$validator = $this->validateClassUnit($request);
		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$validated = $validator["data"];

		$classUnit->update($validated);

		$classUnit->employees()->sync($validated["employeeIds"]);
		return [
			"success" => true
		];
	}

	private function validateClassUnit(Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"alias" => ["string", "max:64", "nullable"],
			"mark" => ["string", "max:3", "required"],
			"startingClassificationPeriodId" => ["integer", "required", "exists:classification_periods,id"],
			"teachingCycleLength" => ["integer", "required", "between:2,8"],
			"promoteEvery" => ["string", "in:year,semester"],
			"employeeIds" => ["array", "required"],
		]);

		if (!$validator["success"]) {
			return $validator;
		}

		$validated = $validator["data"];

		$employeeIds = array_unique($validated["employeeIds"]);
		$employees = Employee::whereIn("id", $employeeIds)->get();
		$existingCount = $employees->count();
		if ($existingCount !== count($employeeIds)) {
			return [
				"success" => false,
				"errorResponse" => \Response::json([
					"success" => false,
					"errors" => [
						"EMPLOYEE_IDS_NOT_FOUND"
					]
				], 400)
			];
		}

		if ($employees->contains("active", false)) {
			return [
				"success" => false,
				"errorResponse" => \Response::json([
					"success" => false,
					"errors" => [
						"EMPLOYEES_MUST_BE_ACTIVE"
					]
				], 400)
			];
		}

		return [
			"success" => true,
			"data" => $validated,
			"employee_ids" => $employeeIds
		];
	}

	public function delete(int $schoolUnitId, ClassUnit $classUnit)
	{
		// TODO: Implement checks to make sure no grade books have been created for this class unit
		$classUnit->delete();
		return [
			"success" => true
		];
	}
}

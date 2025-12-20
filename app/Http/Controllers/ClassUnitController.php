<?php

namespace App\Http\Controllers;

use App\Models\ClassUnit;
use App\Models\Employee;
use App\Utilities\ValidatorAssistant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassUnitController extends Controller
{
	public function list(Request $request, int $schoolUnitId)
	{
		$classUnits = ClassUnit::where("school_unit_id", $schoolUnitId)->get();
		$showInactive = $request->get("showInactive");
		if ($showInactive != "true") {
			$now = Carbon::now();
			$currentSchoolYear = $now->year;
			if ($now->month <= 8) {
				$currentSchoolYear = $now->year - 1;
			}
			$classUnits = $classUnits->filter(function ($item) use ($currentSchoolYear) {
				$yearsDiff = $currentSchoolYear - $item->starting_school_year;
				return $yearsDiff <= $item->teaching_cycle_length;
			});
		}

		return $classUnits->toResourceCollection();
	}

	public function create(Request $request, int $schoolUnitId)
	{
		$validator = $this->validateClassUnit($request);
		$validated = $validator["data"];

		$classUnit = new ClassUnit();
		$classUnit->school_unit_id = $schoolUnitId;
		$classUnit->alias = $validated["alias"];
		$classUnit->mark = $validated["mark"];
		$classUnit->starting_school_year = $validated["startingSchoolYear"];
		$classUnit->teaching_cycle_length = $validated["teachingCycleLength"];

		$classUnit->save();

		$classUnit->employees()->attach($validated["employeeIds"]);

		return [
			"success" => true
		];
	}

	public function update(Request $request, int $schoolUnitId, ClassUnit $classUnit)
	{
		$validator = $this->validateClassUnit($request);
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
			"startingSchoolYear" => ["integer", "required"],
			"teachingCycleLength" => ["integer", "required", "between:2,8"],
			"employeeIds" => ["array", "required"],
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];

		$employeeIds = array_unique($validated["employeeIds"]);
		$existingCount = Employee::whereIn("id", $employeeIds)->count();
		if ($existingCount !== count($employeeIds)) {
			return [
				"success" => false,
				"errorResponse" => [
					\Response::json([
							"success" => false,
							"errors" => [
								"EMPLOYEE_IDS_NOT_FOUND"
							]
						],
						400
					)
				]
			];
		}

		return [
			"data" => $validated,
			"employee_ids" => $employeeIds
		];
	}

	public function delete(ClassUnit $classUnit) {
		// TODO: Implement checks to make sure no grade books have been created for this class unit
		$classUnit->delete();
		return [
			"success" => true
		];
	}
}

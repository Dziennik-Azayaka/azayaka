<?php

namespace App\Http\Controllers;

use App\Enums\ClassUnitCategory;
use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\ClassUnitFormTutors;
use App\Models\ClassUnitPeriod;
use App\Models\Employee;
use App\Models\SchoolUnit;
use App\Utilities\ClassificationPeriodAssistant;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
use App\Utilities\ValidatorAssistant\ValidatorAssistantException;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassUnitController extends Controller
{
	public function list(Request $request)
	{
		$classUnits = ClassUnit::query();
		$currentSchoolYear = ClassificationPeriodAssistant::getCurrentSchoolYear();

		$category = $request->input("category");
		if (isset($category) && !in_array($category, ClassUnitCategory::cases(), true)) {
			$category = ClassUnitCategory::from($request->input("category"));
			$classUnits->filterByCategory($category, $currentSchoolYear);
		}
		return $classUnits->with(["startingPeriod", "formTutors"])->get()->toResourceCollection();
	}

	public function create(Request $request)
	{
		$validated = $this->validateClassUnit($request);

		$classUnit = new ClassUnit();
		$classUnit->school_unit_id = $validated["schoolUnitId"];
		$classUnit->alias = $validated["alias"] ?? null;
		$classUnit->mark = $validated["mark"];
		$classUnit->starting_classification_period_id = $validated["startingClassificationPeriodId"];
		$classUnit->teaching_cycle_length = $validated["teachingCycleLength"];
		$classUnit->promote_every = $validated["promoteEvery"];

		$classUnit->save();

		$pivotEntries = $this->generatePivotEntries($validated["employees"], $classUnit);
		ClassUnitFormTutors::insert($pivotEntries);

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

	public function update(Request $request, ClassUnit $classUnit)
	{
		$validated = $this->validateClassUnit($request);

		$classUnit->update($validated);

		ClassUnitFormTutors::where("class_unit_id", $classUnit->id)->delete();
		$pivotEntries = $this->generatePivotEntries($validated["employees"], $classUnit);
		ClassUnitFormTutors::insert($pivotEntries);

		return [
			"success" => true
		];
	}

	private function validateClassUnit(Request $request)
	{
		$validated = ValidatorAssistant::validate($request, [
			"alias" => ["string", "max:64", "nullable"],
			"mark" => ["string", "max:3", "required"],
			"startingClassificationPeriodId" => ["integer", "required", "exists:classification_periods,id"],
			"teachingCycleLength" => ["integer", "required", "between:2,8"],
			"promoteEvery" => ["string", "in:year,semester"],
			"employees" => ["array", "required"],
			"schoolUnitId" => ["integer", "required", "exists:school_units,id"]
		]);
		$employeeIds = [];

		/*
		 * validation rules:
			- the starting date of at least one teacher should be equal to the start date of the first period of the class unit
			- no ending date should be earlier than a starting date
			- if promoting every year, no ending date should be later than STAR_YEAR+TEACHING_CYCLE_LENGTH-08-31
		 */

		$startingClassificationPeriod = ClassificationPeriod::where("id", "=", $validated["startingClassificationPeriodId"])->first();
		$foundTeacherStartingWithTheClassificationPeriod = false;
		$startingYear = Carbon::parse($startingClassificationPeriod->period_start)->year;
		$endingDate = Carbon::create($startingYear + $validated["teachingCycleLength"], 8, 31);
		foreach ($validated["employees"] as $employee) {
			$employeeIds[] = $employee["id"];

			$dateFrom = Carbon::parse($employee["dateFrom"]);
			$dateTo = Carbon::parse($employee["dateTo"]);
			if ($dateFrom->gt($dateTo)) {
				throw new ValidatorAssistantException(null, null, ["EMPLOYEE_DATE_FROM_MUST_NOT_BE_LATER_THAN_DATE_TO"]);
			}

			if ($dateFrom->eq($startingClassificationPeriod->period_start)) {
				$foundTeacherStartingWithTheClassificationPeriod = true;
			}

			if ($validated["promoteEvery"] == "year" && $dateTo->gt($endingDate)) {
				throw new ValidatorAssistantException(null, null, ["EMPLOYEE_DATE_TO_MUST_NOT_BE_LATER_THAN_THE_CLASS_UNIT_END_DATE"]);
			}
		}

		if (!$foundTeacherStartingWithTheClassificationPeriod) {
			throw new ValidatorAssistantException(null, null, ["AT_LEAST_ONE_FORM_TUTOR_MUST_START_ALONGSIDE_THE_STARTING_PERIOD"]);
		}

		$employees = Employee::whereIn("id", $employeeIds)->get();
		$existingCount = $employees->count();
		if ($existingCount !== count($employeeIds)) {
			throw new ValidatorAssistantException(null, null, ["EMPLOYEE_IDS_NOT_FOUND"]);
		}

		if ($employees->contains("active", false)) {
			throw new ValidatorAssistantException(null, null, ["EMPLOYEES_MUST_BE_ACTIVE"]);
		}

		return $validated;
	}

	public function delete(ClassUnit $classUnit)
	{
		// TODO: Implement checks to make sure no grade books have been created for this class unit
		$classUnit->delete();
		return [
			"success" => true
		];
	}

	public function generatePivotEntries($employees, ClassUnit $classUnit): array
	{
		$now = Carbon::now();
		$pivotEntries = [];
		foreach ($employees as $employee) {
			$pivotEntries[] = [
				"class_unit_id" => $classUnit->id,
				"employee_id" => $employee["id"],
				"date_from" => $employee["dateFrom"],
				"date_to" => $employee["dateTo"],
				"created_at" => $now,
				"updated_at" => $now,
			];
		}
		return $pivotEntries;
	}

	public function show(ClassUnit $classUnit)
	{
		return $classUnit->load(["startingPeriod", "formTutors", "schoolUnit"])->toResource();
	}
}

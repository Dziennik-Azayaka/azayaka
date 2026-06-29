<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityAlreadyExistsException;
use App\Exceptions\GradebooksExistException;
use App\Models\AccountAccess;
use App\Models\ClassUnit;
use App\Models\Gradebook;
use App\Models\GradebookStudents;
use App\Models\Student;
use App\Utilities\CaseConverter;
use Illuminate\Http\Request;

class GradebookController extends Controller
{
	public function list(Request $request, int $schoolUnitId)
	{
		$gradebooks = Gradebook::whereHas("classUnit", function ($query) use ($schoolUnitId) {
			$query->where("school_unit_id", "=", $schoolUnitId);
		});

		if ($request->has("classUnitId")) {
			$gradebooks = $gradebooks->where("class_unit_id", "=", $request->input("classUnitId"));
		}

		if ($request->has("schoolYear")) {
			$gradebooks = $gradebooks->whereHas("startingClassificationPeriod", function ($query) use ($request) {
				$query->where("school_year", "=", $request->input("schoolYear"));
			});
		}

		return $gradebooks->with(["classUnit", "startingClassificationPeriod"])->get()->toResourceCollection();
	}

	public function create(Request $request)
	{
		$validated = $request->validate([
			"classificationPeriodId" => "required|exists:classification_periods,id",
			"classUnitId" => "required|exists:class_units,id",
			"level" => "nullable|integer"
		]);

		$classUnit = ClassUnit::findOrFail($validated["classUnitId"]);
		$this->authorize("create", [Gradebook::class, $classUnit]);

		$classificationPeriodId = $validated["classificationPeriodId"];

		if (isset($validated["level"])) {
			$validLevels = $classUnit->periods()->pluck("class_units_periods.level")->toArray();
			if (!in_array($validated["level"], $validLevels)) {
				return \Response::json([
					"success" => false,
					"errors" => ["LEVEL_OUTSIDE_CLASS_UNIT_RANGE"]
				], 422);
			}

			$existingLevelGradebooks = Gradebook::where("class_unit_id", $validated["classUnitId"])
				->where(function ($query) use ($validated) {
					$query->where("level", $validated["level"])
						->orWhereNull("level");
				})
				->get()
				->filter(fn($gradebook) => $gradebook->level === $validated["level"])
				->count();

			if ($existingLevelGradebooks > 0) {
				throw new EntityAlreadyExistsException("GRADEBOOK");
			}
		}

		if (Gradebook::where("classification_period_id", $classificationPeriodId)
			->where("class_unit_id", $validated["classUnitId"])
			->where(function ($query) use ($validated) {
				if (isset($validated["level"])) {
					$query->where("level", $validated["level"]);
				} else {
					$query->whereNull("level");
				}
			})
			->exists()) {
			throw new EntityAlreadyExistsException("GRADEBOOK");
		}

		$gradebook = Gradebook::create(CaseConverter::toSnakeCase($validated));
		return \Response::json([
			"success" => true,
			"gradebookId" => $gradebook->id
		], 201);
	}

	public function listStudents(Request $request, Gradebook $gradebook)
	{
		$pivotEntries = GradebookStudents::where("gradebook_id", $gradebook->id)->with(["student"])->get();
		return $pivotEntries->map(function ($entry) {
			return [
				"studentId" => $entry->student->id,
				"studentName" => $entry->student->person->first_name,
				"studentSecondName" => $entry->student->person->second_name,
				"studentLastName" => $entry->student->person->last_name,
				"position" => $entry->position
			];
		});
	}

	public function attachStudentsToGradebook(Request $request, Gradebook $gradebook)
	{
		$this->authorize("manageStudents", [$gradebook]);

		$validated = $request->validate([
			"studentIds" => "required|array|min:1|max:255",
			"studentIds.*" => "required|distinct|exists:students,id",
			"positions" => "required|array|min:1|max:255",
			"positions.*" => "required|distinct|integer|min:1|max:255"
		]);

		$studentIdsSize = count($validated["studentIds"]);
		if ($studentIdsSize !== count($validated["positions"])) {
			return \Response::json([
				"success" => false,
				"errors" => [
					"NUMBER_OF_STUDENTS_AND_POSITIONS_DO_NOT_MATCH"
				]
			], 422);
		}

		$schoolUnitId = $gradebook->classUnit->school_unit_id;
		$eligibleStudentIds = Student::whereHas("studentRegistry", function ($query) use ($schoolUnitId) {
			$query->where("school_unit_id", $schoolUnitId);
		})->whereIn("id", $validated["studentIds"])->pluck("id")->toArray();

		$invalidStudentIds = array_diff($validated["studentIds"], $eligibleStudentIds);
		if (!empty($invalidStudentIds)) {
			return \Response::json([
				"success" => false,
				"errors" => ["STUDENTS_NOT_IN_SCHOOL_UNIT"]
			], 422);
		}

		$existingEntries = GradebookStudents::where("gradebook_id", $gradebook->id)->get();
		$pivotEntries = [];
		$now = now();
		for ($i = 0; $i < $studentIdsSize; $i++) {
			if ($existingEntries->contains("student_id", $validated["studentIds"][$i])) {
				$existingEntry = $existingEntries->firstWhere("student_id", $validated["studentIds"][$i]);
				$pivotEntries[] = [
					"student_id" => $validated["studentIds"][$i],
					"gradebook_id" => $gradebook->id,
					"position" => $validated["positions"][$i],
					"date_from" => $existingEntry->date_from,
					"date_to" => $existingEntry->date_to,
					"created_at" => $existingEntry->created_at,
					"updated_at" => $now
				];
				continue;
			}
			$pivotEntries[] = [
				"student_id" => $validated["studentIds"][$i],
				"gradebook_id" => $gradebook->id,
				"position" => $validated["positions"][$i],
				"date_from" => $gradebook->startingClassificationPeriod->period_start,
				"date_to" => null,
				"created_at" => $now,
				"updated_at" => $now
			];
		}

		\DB::transaction(function () use ($pivotEntries, $gradebook) {
			GradebookStudents::where("gradebook_id", $gradebook->id)->delete();
			GradebookStudents::insert($pivotEntries);
		});

		return \Response::json([
			"success" => true
		]);
	}

	public function getStudentGradebooks(Request $request)
	{
		$student = Student::getStudentFromAccessId($request);
		$gradebooks = GradebookStudents::where("student_id", $student->id)
			->with(["gradebook", "gradebook.classUnit"])->get();

		return $gradebooks->map(function ($gradebook) {
			return [
				"id" => $gradebook->gradebook->id,
				"classUnit" => $gradebook->gradebook->classUnit->toResource(),
				"schoolYear" => $gradebook->gradebook->startingClassificationPeriod->school_year
			];
		});
	}
}

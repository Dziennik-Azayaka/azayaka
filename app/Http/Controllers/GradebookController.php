<?php

namespace App\Http\Controllers;

use App\Models\Gradebook;
use App\Models\GradebookStudents;
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
			"classUnitId" => "required|exists:class_units,id"
		]);

		if (Gradebook::where("classification_period_id", $validated["classificationPeriodId"])
			->where("class_unit_id", $validated["classUnitId"])
			->exists()) {
			return \Response::json([
				"success" => false,
				"errors" => [
					"GRADEBOOK_ALREADY_EXISTS"
				]
			], 409);
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
				"studentName" => $entry->student->person-> first_name,
				"studentSecondName" => $entry->student->person->second_name,
				"studentLastName" => $entry->student->person->last_name,
				"position" => $entry->position
			];
		});
	}

	public function attachStudentsToGradebook(Request $request, Gradebook $gradebook)
	{
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
}

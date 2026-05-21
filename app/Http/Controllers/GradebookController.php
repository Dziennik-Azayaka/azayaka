<?php

namespace App\Http\Controllers;

use App\Models\Gradebook;
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
}

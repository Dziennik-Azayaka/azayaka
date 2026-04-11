<?php

namespace App\Http\Controllers;

use App\Models\ChildrenRegistry;
use App\Models\CompulsoryEducationFulfillment;
use App\Models\Student;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
use Illuminate\Http\Request;

class CompulsoryEducationFulfillmentController extends Controller
{
	public function create(Request $request, ChildrenRegistry $childrenRegistry, Student $student)
	{
		$validated = $this->validateFulfillmentData($request);
		$validated["children_registry_id"] = $childrenRegistry->id;
		$validated["student_id"] = $student->id;

		$fulfillment = new CompulsoryEducationFulfillment();
		$fulfillment->student_id = $student->id;
		$fulfillment->children_registry_id = $childrenRegistry->id;
		$fulfillment->school_year = $validated["schoolYear"];
		$fulfillment->control_date = $validated["controlDate"];
		$fulfillment->fulfillment_form = $validated["fulfillmentForm"];
		$fulfillment->level = $validated["level"];
		$fulfillment->relationship = $validated["relationship"];
		$fulfillment->save();

		return \Response::json([
			"success" => true
		], 201);
	}

	public function update(Request $request, ChildrenRegistry $childrenRegistry, Student $student, CompulsoryEducationFulfillment $fulfillment)
	{
		$validated = $this->validateFulfillmentData($request);
		$fulfillment->school_year = $validated["schoolYear"];
		$fulfillment->control_date = $validated["controlDate"];
		$fulfillment->fulfillment_form = $validated["fulfillmentForm"];
		$fulfillment->level = $validated["level"];
		$fulfillment->relationship = $validated["relationship"];
		$fulfillment->save();
		return [
			"success" => true
		];
	}

	public function destroy(ChildrenRegistry $childrenRegistry, Student $student, CompulsoryEducationFulfillment $fulfillment)
	{
		$fulfillment->delete();
		return [
			"success" => true
		];
	}

	protected function validateFulfillmentData(Request $request)
	{
		return ValidatorAssistant::validate($request, [
			"schoolYear" => "required|integer",
			"controlDate" => "required|date",
			"fulfillmentForm" => "required|string|max:255",
			"level" => "required|integer",
			"relationship" => "required|string|max:255",
		]);
	}
}

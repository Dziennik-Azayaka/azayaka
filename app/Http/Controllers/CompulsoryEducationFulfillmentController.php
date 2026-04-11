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

		CompulsoryEducationFulfillment::create($validated)->save();

		return \Response::json([
			"success" => true
		], 201);
	}

	public function update(Request $request, ChildrenRegistry $childrenRegistry, Student $student, CompulsoryEducationFulfillment $compulsoryEducationFulfillment)
	{
		$validated = $this->validateFulfillmentData($request);
		$compulsoryEducationFulfillment->update($validated);
		return [
			"success" => true
		];
	}

	public function destroy(ChildrenRegistry $childrenRegistry, Student $student, CompulsoryEducationFulfillment $compulsoryEducationFulfillment)
	{
		$compulsoryEducationFulfillment->delete();
		return [
			"success" => true
		];
	}

	protected function validateFulfillmentData(Request $request)
	{
		return ValidatorAssistant::validate($request, [
			"school_year" => "required|integer",
			"control_date" => "required|date",
			"fulfillment_form" => "required|string|max:255",
			"level" => "required|integer",
			"relationship" => "required|string|max:255",
		]);
	}
}

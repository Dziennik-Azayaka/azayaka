<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Http\Requests\CompulsoryEducationFulfillmentRequest;
use App\Models\Child;
use App\Models\ChildrenRegistry;
use App\Models\CompulsoryEducationFulfillment;

class CompulsoryEducationFulfillmentController extends Controller
{
	public function create(CompulsoryEducationFulfillmentRequest $request, Child $child)
	{
		$this->checkIfRegistryIsActive($child->childrenRegistry);
		$validated = $request->validated();
		$fulfillment = new CompulsoryEducationFulfillment();
		$fulfillment->child_id = $child->id;
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

	public function update(CompulsoryEducationFulfillmentRequest $request, Child $child, CompulsoryEducationFulfillment $fulfillment)
	{
		$this->checkIfRegistryIsActive($child->childrenRegistry);
		$validated = $request->validated();
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

	public function destroy(Child $child, CompulsoryEducationFulfillment $fulfillment)
	{
		$this->checkIfRegistryIsActive($child->childrenRegistry);
		$fulfillment->delete();
		return [
			"success" => true
		];
	}

	private function checkIfRegistryIsActive(ChildrenRegistry $childrenRegistry)
	{
		if ($childrenRegistry->isArchived()) {
			throw CustomValidationException::withMessages(["CHILDREN_REGISTRY_ARCHIVED"]);
		}
	}
}

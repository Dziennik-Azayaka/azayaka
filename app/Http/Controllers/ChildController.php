<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Models\Child;
use App\Models\ChildrenRegistry;
use App\Models\Student;
use App\Models\StudentRegistry;
use Illuminate\Http\Request;

class ChildController extends Controller
{
	public function list(ChildrenRegistry $childrenRegistry, Request $request)
	{
		$query = $childrenRegistry->children()->with(["person", "person.residenceAddress", "person.guardians"]);

		if ($request->has("birthYear")) {
			$query = $query->whereHas("person", function ($personQuery) use ($request) {
				$personQuery->whereYear("birthdate", "=", $request->input("birthYear"));
			});
		}

		if ($request->has("gender")) {
			$query = $query->whereHas("person", function ($personQuery) use ($request) {
				$personQuery->where("gender", "=", $request->input("gender"));
			});
		}

		return $query->get()->toResourceCollection();
	}

	public function create(Request $request, ChildrenRegistry $childrenRegistry)
	{
		$validated = $request->validate([
			"personId" => ["required", "exists:people,id"]
		]);

		$child = new Child();
		$child->person_id = $validated["personId"];
		$child->children_registry_id = $childrenRegistry->id;
		$child->saveOrFail();

		return \Response::json([
			"success" => true,
			"childId" => $child->id,
		], 201);
	}

	public function destroy(Child $child)
	{
		$this->checkIfRegistryIsActive($child->childrenRegistry);
		$child->delete();
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

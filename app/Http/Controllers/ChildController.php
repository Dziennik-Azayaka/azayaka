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
	public function list(ChildrenRegistry $childrenRegistry)
	{
		return $childrenRegistry->children()->with(["person", "person.residenceAddress"])->get()->toResourceCollection();
	}

	public function show(Child $child)
	{
		return $child->load(["person", "person.residenceAddress"])->toResource();
	}

	public function create(Request $request, ChildrenRegistry $childrenRegistry)
	{
		$validated = $request->validate([
			"personId" => ["required", "exists:people,id"]
		]);

		$child = new Child();
		$child->person_id = $validated["personId"];
		$child->children_registry_id = $childrenRegistry->id;
		try {
			$child->saveOrFail();
		} catch (\Throwable $e) {
			\Log::error($e);
			return response()->json([
				"success" => false,
				"errors" => ["UNKNOWN_SERVER_ERROR"]
			], 500);
		}

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

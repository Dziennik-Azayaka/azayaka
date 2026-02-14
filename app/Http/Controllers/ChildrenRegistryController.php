<?php

namespace App\Http\Controllers;

use App\Models\ChildrenRegistry;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;

class ChildrenRegistryController extends Controller
{
	public function list()
	{
		return ChildrenRegistry::all()->toResourceCollection();
	}

	public function create(Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"schoolUnitId" => "required|integer|exists:school_units,id"
		]);
		if (!$validator["success"]) return $validator["errorResponse"];
		$schoolUnitId = $validator["data"]["schoolUnitId"];
		if (ChildrenRegistry::where("school_unit_id", $schoolUnitId)->exists()) {
			return \Response::json([
				"success" => false,
				"errors" => [
					"CHILDREN_REGISTRY_ALREADY_EXISTS"
				]
			], 409);
		}

		$registry = new ChildrenRegistry();
		$registry->school_unit_id = $schoolUnitId;
		$registry->save();

		return \Response::json([
			"success" => true
		], 201);
	}
}

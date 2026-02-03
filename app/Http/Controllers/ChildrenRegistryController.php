<?php

namespace App\Http\Controllers;

use App\Models\ChildrenRegistry;
use Illuminate\Http\Request;

class ChildrenRegistryController extends Controller
{
	public function list()
	{
		return ChildrenRegistry::all()->toResourceCollection();
	}

	public function create(Request $request, Int $schoolUnitId)
	{
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

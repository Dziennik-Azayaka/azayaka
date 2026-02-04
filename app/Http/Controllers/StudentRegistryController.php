<?php

namespace App\Http\Controllers;

use App\Models\StudentRegistry;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;

class StudentRegistryController extends Controller
{
	public function list()
	{
		return StudentRegistry::all()->toResourceCollection();
	}

	public function create(Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"schoolUnitId" => "required|integer|exists:school_units,id"
		]);
		if (!$validator["success"]) return $validator["errorResponse"];
		$schoolUnitId = $validator["data"]["schoolUnitId"];
		if (StudentRegistry::where("school_unit_id", $schoolUnitId)->exists()) {
			return \Response::json([
				"success" => false,
				"errors" => [
					"STUDENT_REGISTRY_ALREADY_EXISTS"
				]
			], 409);
		}

		$registry = new StudentRegistry();
		$registry->school_unit_id = $schoolUnitId;
		$registry->save();

		return \Response::json([
			"success" => true
		], 201);
	}
}

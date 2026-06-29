<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolUnitRequest;
use App\Models\SchoolUnit;
use App\Utilities\CaseConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SchoolUnitController extends Controller
{
	public function list()
	{
		return SchoolUnit::all()->toResourceCollection();
	}

	public function create(SchoolUnitRequest $request)
	{
		$data = $request->validated();

		if (SchoolUnit::count() > 1 && $data["schoolComplexId"] == null) {
			return Response::json([
				"success" => false,
				"errors" => [
					"CANNOT_CREATE_MULTIPLE_SCHOOL_UNITS_WITHOUT_PARENT"
				]
			]);
		}

		SchoolUnit::create(CaseConverter::toSnakeCase($data));

		Response::json(["success" => true], 201);
	}

	public function update(SchoolUnitRequest $request, SchoolUnit $schoolUnit)
	{
		if (!$schoolUnit->active) {
			return Response::json([
				"success" => false,
				"errors" => [
					"SCHOOL_UNIT_NOT_ACTIVE"
				]
			]);
		}

		$data = $request->validated();
		$schoolUnit->create(CaseConverter::toSnakeCase($data));

		Response::json(["success" => true]);
	}

	public function archive(Request $request, SchoolUnit $schoolUnit)
	{
		$data = $request->validate([
			"password" => "required|current_password",
			"state" => "required|boolean"
		]);

		$schoolUnit->active = $data["state"];
		$schoolUnit->save();

		Response::json(["success" => true]);
	}
}

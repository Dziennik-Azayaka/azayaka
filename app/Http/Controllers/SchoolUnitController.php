<?php

namespace App\Http\Controllers;

use App\Enums\SchoolType;
use App\Enums\Voivodeship;
use App\Models\SchoolUnit;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class SchoolUnitController extends Controller
{
	public function list()
	{
		return SchoolUnit::all()->toResourceCollection();
	}

	public function create(Request $request) {
		$validator = $this->validateSchoolUnit($request);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$data = $validator["data"];

		if ($data["studentCategory"] != "childrenAndYouths" && $data["studentCategory"] != "adultsOnly") {
			return Response::json([
				"success" => false,
				"errors" => [
					"INVALID_STUDENT_CATEGORY"
				]
			], 400);
		}

		if (SchoolUnit::count() > 1 && $data["schoolComplexId"] == null) {
			return Response::json([
				"success" => false,
				"errors" => [
					"CANNOT_CREATE_MULTIPLE_SCHOOL_UNITS_WITHOUT_PARENT"
				]
			]);
		}

		$unit = new SchoolUnit();
		$unit->name = $data["name"];
		$unit->type = $data["type"];
		$unit->student_category = $data["studentCategory"];
		$unit->municipality = $data["municipality"];
		$unit->voivodeship = $data["voivodeship"];
		$unit->district = $data["district"];
		$unit->address = $data["address"];
		$unit->short_name = $data["shortName"];
		$unit->school_complex_id = $data["schoolComplexId"];
		$unit->save();
		return [
			"success" => true
		];
	}

	public function update(Request $request, SchoolUnit $schoolUnit) {
		if (!$schoolUnit->active) {
			return Response::json([
				"success" => false,
				"errors" => [
					"SCHOOL_UNIT_NOT_ACTIVE"
				]
			]);
		}

		$validator = $this->validateSchoolUnit($request);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$data = $validator["data"];

		if ($data["studentCategory"] != "childrenAndYouths" && $data["studentCategory"] != "adultsOnly") {
			return Response::json([
				"success" => false,
				"errors" => [
					"INVALID_STUDENT_CATEGORY"
				]
			], 400);
		}

		$schoolUnit->name = $data["name"];
		$schoolUnit->type = $data["type"];
		$schoolUnit->student_category = $data["studentCategory"];
		$schoolUnit->municipality = $data["municipality"];
		$schoolUnit->voivodeship = $data["voivodeship"];
		$schoolUnit->district = $data["district"];
		$schoolUnit->address = $data["address"];
		$schoolUnit->short_name = $data["shortName"];
		$schoolUnit->school_complex_id = $data["schoolComplexId"];
		$schoolUnit->save();

		return [
			"success" => true
		];
	}

	public function archive(Request $request, SchoolUnit $schoolUnit) {
		$validator = ValidatorAssistant::validate($request, [
			"password" => "required|current_password",
			"state" => "required|boolean"
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$schoolUnit->active = $validator["data"]["state"];
		$schoolUnit->save();
		return [
			"success" => true
		];
	}

	private function validateSchoolUnit(Request $request) {
		return ValidatorAssistant::validate($request, [
			"name" => ["required", "max:255"],
			"type" => ["required", Rule::enum(SchoolType::class)],
			"studentCategory" => ["required"],
			"municipality" => ["required", "max:255"],
			"voivodeship" => ["required", Rule::enum(Voivodeship::class)],
			"district" => ["nullable", "max:255"],
			"address" => ["required", "max:255"],
			"shortName" => ["required", "max:255"],
			"schoolComplexId" => ["nullable", "exists:school_complexes,id"]
		]);
	}
}

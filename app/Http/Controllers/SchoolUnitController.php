<?php

namespace App\Http\Controllers;

use App\Enums\SchoolType;
use App\Enums\Voivodeship;
use App\Models\SchoolUnit;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
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
		$data = $this->validateSchoolUnit($request);

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
		$unit->town = $data["town"];
		$unit->district = $data["district"];
		$unit->postal_code = $data["postalCode"];
		$unit->street = $data["street"];
		$unit->house_number = $data["houseNumber"];
		$unit->flat_number = $data["flatNumber"];
		$unit->short_name = $data["shortName"];
		$unit->school_complex_id = $data["schoolComplexId"];
		$unit->save();
		return \Response::json([
			"success" => true
		], 201);
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

		$data = $this->validateSchoolUnit($request);

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
		$schoolUnit->town = $data["town"];
		$schoolUnit->district = $data["district"];
		$schoolUnit->postal_code = $data["postalCode"];
		$schoolUnit->street = $data["street"];
		$schoolUnit->house_number = $data["houseNumber"];
		$schoolUnit->flat_number = $data["flatNumber"];
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

		$schoolUnit->active = $validator["state"];
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
			"town" => ["nullable", "max:255"],
			"district" => ["nullable", "max:255"],
			"postalCode" => ["required", "max:7"],
			"street" => ["required", "max:255"],
			"houseNumber" => ["required", "max:255"],
			"flatNumber" => ["nullable", "max:255"],
			"shortName" => ["required", "max:255"],
			"schoolComplexId" => ["nullable", "exists:school_complexes,id"]
		]);
	}
}

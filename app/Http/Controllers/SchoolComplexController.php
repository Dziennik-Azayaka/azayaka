<?php

namespace App\Http\Controllers;

use App\Enums\SchoolType;
use App\Models\SchoolComplex;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolComplexController extends Controller
{
	public function list()
	{
		return SchoolComplex::get(["id", "name", "type"]);
	}

	public function create(Request $request) {
		$validator = ValidatorAssistant::validate($request, [
			"name" => ["required", "max:255"],
			"type" => ["required", Rule::enum(SchoolType::class)]
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$data = $validator["data"];

		$schoolComplex = new SchoolComplex();
		$schoolComplex["name"] = $data["name"];
		$schoolComplex["type"] = $data["type"];
		$schoolComplex->save();

		return [
			"success" => true,
		];
	}

	public function update(Request $request, SchoolComplex $schoolComplex) {
		$validator = ValidatorAssistant::validate($request, [
			"name" => ["required", "max:255"],
			"type" => ["required", Rule::enum(SchoolType::class)]
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$data = $validator["data"];
		$schoolComplex["name"] = $data["name"];
		$schoolComplex["type"] = $data["type"];
		$schoolComplex->save();

		return [
			"success" => true,
		];
	}
}

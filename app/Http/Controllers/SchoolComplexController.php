<?php

namespace App\Http\Controllers;

use App\Enums\SchoolType;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
use Illuminate\Http\Request;

class SchoolComplexController extends Controller
{
	public function list()
	{
		return SchoolComplex::get(["id", "name", "type"]);
	}

	public function create(Request $request) {
		$data = ValidatorAssistant::validate($request, [
			"name" => ["required", "max:255"]
		]);

		$schoolComplex = new SchoolComplex();
		$schoolComplex["name"] = $data["name"];
		$schoolComplex["type"] = SchoolType::ZESPOL_SZKOL_I_PLACOWEK_OSWIATOWYCH;
		$schoolComplex->save();

		if (SchoolUnit::count() > 0) {
			SchoolUnit::query()->update(['school_complex_id' => $schoolComplex->id]);
		}

		return [
			"success" => true,
		];
	}

	public function update(Request $request, SchoolComplex $schoolComplex) {
		$data = ValidatorAssistant::validate($request, [
			"name" => ["required", "max:255"]
		]);

		$schoolComplex["name"] = $data["name"];
		$schoolComplex["type"] = SchoolType::ZESPOL_SZKOL_I_PLACOWEK_OSWIATOWYCH;
		$schoolComplex->save();

		return [
			"success" => true,
		];
	}
}

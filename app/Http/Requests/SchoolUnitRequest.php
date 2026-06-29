<?php

namespace App\Http\Requests;

use App\Enums\SchoolType;
use App\Enums\Voivodeship;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolUnitRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			"name" => ["required", "max:255"],
			"type" => ["required", Rule::enum(SchoolType::class)],
			"studentCategory" => ["required", Rule::in(['childrenAndYouths', 'adultsOnly'])],
			"municipality" => ["required", "max:255"],
			"voivodeship" => ["required", Rule::enum(Voivodeship::class)],
			"town" => ["required", "max:255"],
			"district" => ["nullable", "max:255"],
			"postalCode" => ["required", "max:7"],
			"street" => ["nullable", "max:255"],
			"houseNumber" => ["required", "max:255"],
			"flatNumber" => ["nullable", "max:255"],
			"shortName" => ["required", "max:255"],
			"schoolComplexId" => ["nullable", "exists:school_complexes,id"]
		];
	}
}

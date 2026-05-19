<?php

namespace App\Http\Requests;

use App\Rules\Pesel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreatePersonRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			"firstName" => ["required", "string", "max:255"],
			"lastName" => ["required", "string", "max:255"],
			"secondName" => ["nullable", "string", "max:255"],
			"pesel" => ["required_without:alternateIdentityDocument", new Pesel],
			"alternateIdentityDocument" => ["required_without:pesel", "max:255"],
			"birthdate" => ["required", "date"],
			"birthplace" => ["required", "string", "max:255"],
			"gender" => ["nullable", "in:male,female"],
			"studentRegistryId" => ["nullable", "exists:student_registries,id"],
			"admissionDate" => ["required_with:studentRegistryId", "date"],
			"childrenRegistryId" => ["nullable", "exists:children_registries,id"],
			"residenceAddressCountry" => ["required", "max:255"],
			"residenceAddressCommune" => ["nullable", "max:255"],
			"residenceAddressTown" => ["nullable", "max:255"],
			"residenceAddressPostalCode" => ["nullable", "max:255"],
			"residenceAddressStreet" => ["nullable", "max:255"],
			"residenceAddressHouseNumber" => ["nullable", "max:255"],
			"residenceAddressFlatNumber" => ["nullable", "max:255"],
		];
	}
}

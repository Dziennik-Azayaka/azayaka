<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GuardianRequest extends FormRequest
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
			"email" => ["nullable", "email", "max:255"],
			"phoneNumber" => ["nullable", "max:32"],
			"residenceAddressCountry" => ["required", "string", "max:255"],
			"residenceAddressCommune" => ["nullable", "string", "max:255"],
			"residenceAddressTown" => ["nullable", "string", "max:255"],
			"residenceAddressPostalCode" => ["nullable", "string", "max:255"],
			"residenceAddressStreet" => ["nullable", "string", "max:255"],
			"residenceAddressHouseNumber" => ["nullable", "max:255"],
			"residenceAddressFlatNumber" => ["nullable", "max:255"],
		];
	}
}

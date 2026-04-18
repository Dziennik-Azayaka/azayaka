<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GuardianRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

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
			"phoneNumber" => ["nullable", "max:16"]
		];
	}
}

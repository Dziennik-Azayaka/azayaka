<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompulsoryEducationFulfillmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			"schoolYear" => ["required", "integer"],
			"controlDate" => ["required", "date"],
			"kindergartenInfo" => ["nullable", "string", "max:512"],
			"postponementInfo" => ["nullable", "string", "max:512"],
			"schoolInfo" => ["nullable", "string", "max:512"],
			"outOfSchoolInfo" => ["nullable", "string", "max:512"],
			"level" => ["required", "integer"]
        ];
    }
}

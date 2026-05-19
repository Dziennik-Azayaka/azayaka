<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompulsoryEducationFulfillmentRequest extends FormRequest
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
			"schoolYear" => "required|integer",
			"controlDate" => "required|date",
			"fulfillmentForm" => "required|string|max:255",
			"level" => "required|integer",
			"relationship" => "required|string|max:255"
        ];
    }
}

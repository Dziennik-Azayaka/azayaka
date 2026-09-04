<?php

namespace App\Http\Requests;

use App\Enums\AttendancePrimitiveType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceComplexTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
		$uniqueRule = Rule::unique("attendance_complex_types");
		if ($this->method() === "PUT") {
			$uniqueRule = $uniqueRule->ignore($this->route("type")->id);
		}

        return [
			"name" => ["required", "string", "max:255"],
			"shortcut" => ["required", "string", "max:3", $uniqueRule],
			"mapsToPrimitiveType" => ["required", Rule::enum(AttendancePrimitiveType::class)]
		];
    }
}

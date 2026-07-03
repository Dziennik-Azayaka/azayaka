<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianAccessResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			"guardianFirstName" => $this->first_name,
			"guardianSecondName" => $this->second_name,
			"guardianLastName" => $this->last_name,
			"guardianId" => $this->id,
			"students" => $this->person->students->map(fn($student) => [
				"studentId" => $student->id,
				"firstName" => $student->person->first_name,
				"secondName" => $student->person->second_name,
				"lastName" => $student->person->last_name
			]),
			"accessWords" => $this->accountAccesses->filter(function ($access) {
				return $access->guardian_id != null && $access->student_id != null;
			})->first()
		];
	}
}

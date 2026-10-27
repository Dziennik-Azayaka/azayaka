<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianAccessResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		$student = $this->person->students->first();

		if (!$student) {
			throw new \RuntimeException("Guardian {$this->id} has no assigned student.");
		}

		$access = $this->accountAccesses->first(
			fn($access) => $access->student_id == $student->id
		);

		return [
			"firstName" => $this->first_name,
			"secondName" => $this->second_name,
			"lastName" => $this->last_name,
			"id" => $this->id,
			"student" => [
				"studentId" => $student->id,
				"firstName" => $student->person->first_name,
				"secondName" => $student->person->second_name,
				"lastName" => $student->person->last_name
			],
			"accessWords" => $access ? new AccountAccessResource($access) : null
		];
	}
}

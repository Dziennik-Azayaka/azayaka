<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradebookStudentResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			"id" => $this->student->id,
			"firstName" => $this->student->person->first_name,
			"secondName" => $this->student->person->second_name,
			"lastName" => $this->student->person->last_name,
			"position" => $this->position
		];
	}
}

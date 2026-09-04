<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentGradebookResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			"id" => $this->gradebook->id,
			"classUnit" => $this->gradebook->classUnit->toResource(),
			"schoolYear" => $this->gradebook->startingClassificationPeriod->school_year,
			"periodNumber" => $this->gradebook->startingClassificationPeriod->period_number
		];
	}
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassUnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			"alias" => $this->alias,
			"mark" => $this->mark,
			"startingSchoolYear" => $this->starting_school_year,
			"teachingCycleLength" => $this->teaching_cycle_length
		];
    }
}

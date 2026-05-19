<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompulsoryEducationFulfillmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			"id" => $this->id,
			"schoolYear" => $this->school_year,
			"controlDate" => $this->control_date,
			"kindergarten_info" => $this->kindergartenInfo,
			"postponement_info" => $this->postponementInfo,
			"school_info" => $this->schoolInfo,
			"out_of_school_info" => $this->outOfSchoolInfo,
			"level" => $this->level
		];
    }
}

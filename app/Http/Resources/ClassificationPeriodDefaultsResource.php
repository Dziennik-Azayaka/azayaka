<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassificationPeriodDefaultsResource extends JsonResource
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
			"schoolUnitId" => $this->school_unit_id,
			"schoolYear" => $this->school_year,
			"periodNumber" => $this->period_number,
			"periodStart" => $this->period_start,
			"periodEnd" => $this->period_end
		];
    }
}

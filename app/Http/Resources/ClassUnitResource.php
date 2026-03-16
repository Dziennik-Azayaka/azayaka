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
			"id" => $this->id,
			"schoolUnitId" => $this->school_unit_id,
			"alias" => $this->alias,
			"mark" => $this->mark,
			"startingClassificationPeriodId" => $this->starting_classification_period_id,
			"teachingCycleLength" => $this->teaching_cycle_length,
			"level" => $this->currentLevel
		];
    }
}

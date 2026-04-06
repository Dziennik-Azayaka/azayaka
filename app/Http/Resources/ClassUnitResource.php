<?php

namespace App\Http\Resources;

use App\Models\ClassUnitFormTutors;
use Carbon\Carbon;
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
			"startingClassificationPeriodId" => $this->startingPeriod->id,
			"startingClassificationPeriodYear" => $this->startingPeriod->school_year,
			"startingClassificationPeriodNumber" => $this->startingPeriod->period_number,
			"teachingCycleLength" => $this->teaching_cycle_length,
			"level" => $this->currentLevel,
			"formTutors" => $this->formTutors->map(fn($tutor) => [
				"employeeId" => $tutor->id,
				"firstName" => $tutor->first_name,
				"lastName" => $tutor->last_name,
				"dateFrom" => Carbon::parse($tutor->pivot->date_from)->format("Y-m-d"),
				"dateTo" => Carbon::parse($tutor->pivot->date_to)->format("Y-m-d")
			])
		];
    }
}

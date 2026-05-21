<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradebookResource extends JsonResource
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
			"schoolYear" => $this->startingClassificationPeriod->school_year, // TODO: check if this is fine for class units which are promoted every semester?
			"level" => $this->level,
			"classUnit" => new ClassUnitResource($this->whenLoaded("classUnit"))
		];
    }
}

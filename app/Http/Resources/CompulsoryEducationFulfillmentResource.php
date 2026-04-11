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
			"fulfillmentForm" => $this->fulfillmentForm,
			"level" => $this->level,
			"relationship" => $this->relationship
		];
    }
}

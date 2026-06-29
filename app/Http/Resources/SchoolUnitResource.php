<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolUnitResource extends JsonResource
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
			"name" => $this->name,
			"active" => $this->active == 1,
			"type" => $this->type,
			"studentCategory" => $this->student_category,
			"municipality" => $this->municipality,
			"voivodeship" => $this->voivodeship,
			"town" => $this->town,
			"district" => $this->district,
			"postalCode" => $this->postal_code,
			"post" => $this->post,
			"street" => $this->street,
			"houseNumber" => $this->house_number,
			"flatNumber" => $this->flat_number,
			"shortName" => $this->short_name,
			"schoolComplexId" => $this->school_complex_id
		];
    }
}

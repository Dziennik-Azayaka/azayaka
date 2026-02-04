<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidenceAddressResource extends JsonResource
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
			"country" => $this->country,
			"commune" => $this->commune,
			"town" => $this->town,
			"postalCode" => $this->postal_code,
			"street" => $this->street,
			"houseNumber" => $this->house_number
		];
    }
}

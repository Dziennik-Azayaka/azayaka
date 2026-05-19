<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChildResource extends JsonResource
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
			"person" => new PersonResource($this->whenLoaded("person")),
			"compulsoryEducationFulfillments" => $this->compulsoryEducationFulfillments
				->map(fn($fulfillment) => new CompulsoryEducationFulfillmentResource($fulfillment))
		];
    }
}

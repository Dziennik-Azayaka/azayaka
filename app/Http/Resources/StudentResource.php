<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
			"studentRegistryNumber" => $this->studentRegistryNumber,
			"person" => new PersonResource($this->whenLoaded("person")),
			"admissionDate" => $this->admission_date,
			"leaveDate" => $this->leave_date,
			"leaveReason" => $this->leave_reason
		];
	}
}

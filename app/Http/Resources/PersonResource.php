<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
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
			"firstName" => $this->first_name,
			"secondName" => $this->second_name,
			"lastName" => $this->last_name,
			"pesel" => $this->pesel,
			"alternateIdentityDocument" => $this->alternate_identity_document,
			"birthdate" => $this->birthdate,
			"birthplace" => $this->birthplace,
			"gender" => $this->gender,
			"residenceAddress" => new ResidenceAddressResource($this->whenLoaded("residenceAddress"))
		];
	}
}

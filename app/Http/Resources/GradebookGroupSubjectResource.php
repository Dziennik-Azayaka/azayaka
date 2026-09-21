<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradebookGroupSubjectResource extends JsonResource
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
			"description" => $this->description,
			"subject" => $this->whenLoaded("subject", fn() => [
				"id" => $this->subject->id,
				"name" => $this->subject->name,
			]),
			"teachers" => $this->whenLoaded("teachers", fn() => $this->teachers->map(fn($teacher) => [
				"id" => $teacher->id,
				"firstName" => $teacher->first_name,
				"lastName" => $teacher->last_name,
			])),
		];
	}
}

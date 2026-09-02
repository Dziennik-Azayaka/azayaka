<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradebookGroupResource extends JsonResource
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
			"shortcut" => $this->shortcut,
			"studentIds" => $this->students->pluck("id"),
			"isGradebookLevel" => false,
			"subjects" => $this->groupSubjects->map(fn($groupSubject) => new GradebookGroupSubjectResource($groupSubject))
		];
	}
}

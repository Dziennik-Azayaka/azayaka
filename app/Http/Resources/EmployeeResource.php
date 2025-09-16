<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
			"lastName" => $this->last_name,
			"shortcut" => $this->shortcut,
			"active" => $this->active == 1,
			"isAdmin" => $this->is_admin == 1,
			"isHeadmaster" => $this->is_headmaster == 1,
			"isSecretary" => $this->is_secretary == 1,
			"isTeacher" => $this->is_teacher == 1
		];
    }
}

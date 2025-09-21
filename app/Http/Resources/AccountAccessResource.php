<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountAccessResource extends JsonResource
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
			"studentId" => $this->student_id,
			"guardianId" => $this->guardian_id,
			"employeeId" => $this->employee_id,
			"user_id" => $this->user_id,
			"words" => $this->words,
			"active" => $this->words != null,
			"createdAt" => $this->created_at,
			"activatedAt" => $this->updated_at
		];
    }
}

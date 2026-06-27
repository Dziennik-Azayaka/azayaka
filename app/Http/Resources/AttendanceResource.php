<?php

namespace App\Http\Resources;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
			"primitiveType" => $this->primitive_type,
			"complexType" => $this->attendance_complex_type_id,
			"studentId" => $this->student_id,
			"employee" => $this->employee_id != null ?
				$this->employee->first_name . " " . $this->employee->last_name :
				"System"
		];
	}
}

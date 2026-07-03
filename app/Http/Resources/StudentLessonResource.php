<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentLessonResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			"id" => $this->id,
			"number" => $this->number,
			"topic" => $this->topic,
			"date" => $this->date,
			"startTime" => $this->start_time,
			"endTime" => $this->end_time,
			"completed" => $this->completed,
			"primaryTeacher" => $this->primaryTeacher->first_name . " " . $this->primaryTeacher->last_name,
			"subject" => $this->subject->name,
			"assistingTeachers" => $this->assistingTeachers->map(fn($teacher) => $teacher->first_name . " " . $teacher->last_name)
		];
	}
}

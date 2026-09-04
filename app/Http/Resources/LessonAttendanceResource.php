<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonAttendanceResource extends JsonResource
{
    protected $students;
    protected $includeDateField = false;

    public function __construct($resource, $students = null, $includeDateField = false)
    {
        parent::__construct($resource);
        $this->students = $students;
        $this->includeDateField = $includeDateField;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            "id" => $this->id,
            "subject" => $this->subject->name,
            "number" => $this->number,
            "startTime" => $this->start_time,
            "endTime" => $this->end_time,
            "students" => $this->students->map(fn($student) => [
                "id" => $student->id,
                "firstName" => $student->person->first_name,
                "secondName" => $student->person->second_name,
                "lastName" => $student->person->last_name,
                "position" => $student->pivot?->position ??
                    ($this->resource->gradebook?->students->find($student->id)?->pivot?->position)
            ]),
            "attendances" => AttendanceResource::collection($this->attendances),
        ];

        if ($this->includeDateField) {
            $data["date"] = $this->date;
        }

        return $data;
    }
}

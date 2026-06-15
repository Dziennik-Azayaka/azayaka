<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function PHPSTORM_META\map;

class LessonResource extends JsonResource
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
			"number" => $this->number,
			"primaryTeacher" => [
				"id" => $this->primaryTeacher->id,
				"firstName" => $this->primaryTeacher->first_name,
				"secondName" => $this->primaryTeacher->second_name,
				"lastName" => $this->primaryTeacher->last_name,
			],
			"assistingTeachers" => $this->assistingTeachers->map(fn($teacher) => [
				"id" => $teacher->id,
				"firstName" => $teacher->first_name,
				"secondName" => $teacher->second_name,
				"lastName" => $teacher->last_name,
			]),
			"groups" => $this->gradebookGroups->map(fn($group) => [
				"id" => $group->id,
				"name" => $group->name,
				"shortcut" => $group->shortcut,
			]),
			"date" => $this->date->format("Y-m-d"),
			"subject" => $this->subject->name,
			"topic" => $this->topic,
			"startTime" => $this->start_time,
			"endTime" => $this->end_time,
			"completed" => $this->completed
		];
	}
}

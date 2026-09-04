<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$entries = $this->gradebooks->map(function ($lessonGradebook) {
			$gradebook = $lessonGradebook->gradebook;
			$groups = $lessonGradebook->groups->map(fn($g) => $g->gradebookGroup);

			return [
				"classUnit" => $gradebook?->classUnit,
				"groups" => $groups,
			];
		});

		$classUnits = $entries
			->filter(fn($e) => $e["classUnit"] !== null)
			->groupBy(fn($e) => $e["classUnit"]->id)
			->map(fn($group, $classUnitId) => [
				"id" => $group->first()["classUnit"]->id,
				"alias" => $group->first()["classUnit"]->alias,
				"mark" => $group->first()["classUnit"]->mark,
				"level" => $group->first()["classUnit"]->currentLevel,
				"groups" => $group->flatMap(fn($e) => $e["groups"])->unique("id")->values()
					->map(fn($g) => [
						"id" => $g->id,
						"name" => $g->name,
						"shortcut" => $g->shortcut,
					])->values()
			])
			->values();

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
			"classUnits" => $classUnits,
			"date" => $this->date->format("Y-m-d"),
			"subject" => $this->subject->name,
			"topic" => $this->topic,
			"startTime" => $this->start_time,
			"endTime" => $this->end_time,
			"completed" => $this->completed
		];
	}
}

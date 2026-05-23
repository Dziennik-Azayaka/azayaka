<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lesson extends Model
{
	/** @use HasFactory<\Database\Factories\LessonFactory> */
	use HasFactory;

	protected $casts = [
		"date" => "date",
		"completed" => "boolean"
	];

	public function gradebook(): BelongsTo
	{
		return $this->belongsTo(Gradebook::class);
	}

	public function subject(): BelongsTo
	{
		return $this->belongsTo(Subject::class);
	}

	public function primaryTeacher(): BelongsTo
	{
		return $this->belongsTo(Employee::class, "primary_teacher_id");
	}

	public function assistingTeachers(): BelongsToMany
	{
		return $this->belongsToMany(
			Employee::class,
			"lessons_assisting_teachers",
			"lesson_id",
			"employee_id"
		);
	}

	public function gradebookGroups(): BelongsToMany
	{
		return $this->belongsToMany(
			GradebookGroup::class,
			"lessons_gradebook_group",
			"lesson_id",
			"gradebook_group_id"
		);
	}
}

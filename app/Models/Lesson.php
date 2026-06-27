<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
	/** @use HasFactory<\Database\Factories\LessonFactory> */
	use HasFactory, SoftDeletes;

	protected $casts = [
		"date" => "date",
		"completed" => "boolean"
	];

	public function gradebooks(): BelongsToMany
	{
		return $this->belongsToMany(
			Gradebook::class,
			"lessons_gradebooks",
			"lesson_id",
			"gradebook_id"
		);
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

	public function attendances(): HasMany
	{
		return $this->hasMany(Attendance::class);
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends BaseModel
{
	/** @use HasFactory<\Database\Factories\EmployeeFactory> */
	use HasFactory;

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function classUnits()
	{
		return $this->belongsToMany(ClassUnit::class, "class_units_employees", "employee_id", "class_unit_id");
	}

	public function taughtGroupSubjects(): BelongsToMany
	{
		return $this->belongsToMany(GradebookGroupSubject::class, "employee_gradebook_group_subject");
	}

	public function primaryLessons(): HasMany
	{
		return $this->hasMany(Lesson::class, "primary_teacher_id");
	}

	public function assistingLessons(): BelongsToMany
	{
		return $this->belongsToMany(
			Lesson::class,
			"lessons_assisting_teachers",
			"employee_id",
			"lesson_id"
		);
	}

	public function scopeActiveFormTutor(Builder $query): void
	{
		$date = now();

		$query->where("date_from", "<=", $date)
			->where(function (Builder $query) use ($date) {
				$query->whereNull("date_to")
					->orWhere("date_to", ">=", $date);
			});
	}
}

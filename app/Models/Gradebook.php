<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gradebook extends Model
{
	/** @use HasFactory<\Database\Factories\GradebookFactory> */
	use HasFactory;

	protected $fillable = ["classification_period_id", "class_unit_id", "level"];

	public function classUnit(): BelongsTo
	{
		return $this->belongsTo(ClassUnit::class);
	}

	public function startingClassificationPeriod(): BelongsTo
	{
		return $this->belongsTo(ClassificationPeriod::class, "classification_period_id");
	}

	public function students(): BelongsToMany
	{
		return $this->belongsToMany(Student::class, "gradebooks_students", "gradebook_id", "student_id")
			->withPivot("id", "date_from", "date_to")->withTimestamps();
	}

	public function getLevelAttribute(): ?int
	{
		return $this->attributes["level"] ?? $this->classUnit->getLevelDuringClassificationPeriod($this->startingClassificationPeriod->id);
	}

	public function groups(): HasMany
	{
		return $this->hasMany(GradebookGroup::class);
	}

	public function subjects(): HasMany
	{
		return $this->hasMany(GradebookGroupSubject::class, "gradebook_id")->whereNull("gradebook_group_id");
	}

	public function lessons(): BelongsToMany
	{
		return $this->belongsToMany(
			Lesson::class,
			"lessons_gradebooks",
			"gradebook_id",
			"lesson_id"
		);
	}

	public function scopeInSchoolUnit($query, int $schoolUnitId)
	{
		return $query->whereHas("classUnit", function ($query) use ($schoolUnitId) {
			$query->where("school_unit_id", "=", $schoolUnitId);
		});
	}

	public function scopeForClassUnit($query, int $classUnitId)
	{
		return $query->where("class_unit_id", "=", $classUnitId);
	}

	public function scopeInSchoolYear($query, int $schoolYear)
	{
		return $query->whereHas("startingClassificationPeriod", function ($query) use ($schoolYear) {
			$query->where("school_year", "=", $schoolYear);
		});
	}
}

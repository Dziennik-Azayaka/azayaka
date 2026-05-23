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

	protected $fillable = ["classification_period_id", "class_unit_id"];

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
		return $this->belongsToMany(Student::class, "gradebooks_students", "gradebook_id", "student_id");
	}

	public function getLevelAttribute(): ?int
	{
		return $this->classUnit->getLevelDuringClassificationPeriod($this->startingClassificationPeriod->id);
	}

	public function groups(): HasMany
	{
		return $this->hasMany(GradebookGroup::class);
	}

	public function lessons(): HasMany
	{
		return $this->hasMany(Lesson::class);
	}
}

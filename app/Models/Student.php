<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends BaseModel
{
	/** @use HasFactory<\Database\Factories\StudentFactory> */
	use HasFactory, SoftDeletes;

	public function studentRegistry(): BelongsTo
	{
		return $this->belongsTo(StudentRegistry::class);
	}

	public function person(): BelongsTo
	{
		return $this->belongsTo(Person::class);
	}

	public function classUnits(): BelongsToMany
	{
		return $this->belongsToMany(ClassUnit::class, "class_units_students", "student_id", "class_unit_id");
	}

	public function classUnits(): BelongsToMany
	{
		return $this->belongsToMany(ClassUnit::class, "class_units_students", "student_id", "class_unit_id");
	}
}

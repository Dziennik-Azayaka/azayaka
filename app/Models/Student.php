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

	public function gradebooks(): BelongsToMany
	{
		return $this->belongsToMany(Gradebook::class, "gradebooks_students", "student_id", "gradebook_id");
	}

	public function gradebookGroups(): BelongsToMany
	{
		return $this->belongsToMany(GradebookGroup::class);
	}

	public function accountAccesses(): HasMany
	{
		return $this->hasMany(AccountAccess::class);
	}
}

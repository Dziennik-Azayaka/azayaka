<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends BaseModel
{
	/** @use HasFactory<\Database\Factories\StudentFactory> */
	use HasFactory;

	function guardians()
	{
		return $this->belongsToMany(Guardian::class);
	}

	public function studentRegistries(): BelongsToMany
	{
		return $this->belongsToMany(StudentRegistry::class, "student_registry_student")->withPivot("id");
	}

	public function childrenRegistries(): BelongsToMany
	{
		return $this->belongsToMany(ChildrenRegistry::class, "children_registry_student")->withPivot("id");
	}
}

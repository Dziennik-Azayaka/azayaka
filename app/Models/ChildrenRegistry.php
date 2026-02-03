<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChildrenRegistry extends Model
{
	public function schoolUnit(): BelongsTo
	{
		return $this->belongsTo(SchoolUnit::class);
	}

	public function students(): BelongsToMany
	{
		return $this->belongsToMany(Student::class, "children_registry_student")->withPivot("id");
	}
}

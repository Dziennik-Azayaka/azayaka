<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChildrenRegistry extends Model
{

	protected $fillable = ["school_unit_id"];
	public function schoolUnit(): BelongsTo
	{
		return $this->belongsTo(SchoolUnit::class);
	}

	public function students(): HasMany
	{
		return $this->hasMany(Student::class);
	}

	public function isArchived() {
		return $this->schoolUnit->active == false;
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChildrenRegistry extends Model
{
	use HasFactory;

	protected $fillable = ["school_unit_id"];
	public function schoolUnit(): BelongsTo
	{
		return $this->belongsTo(SchoolUnit::class);
	}

	public function children(): HasMany
	{
		return $this->hasMany(Child::class);
	}

	public function isArchived(): bool
	{
		return $this->schoolUnit->active == false;
	}
}

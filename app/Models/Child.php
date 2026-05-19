<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Child extends Model
{
    /** @use HasFactory<\Database\Factories\ChildFactory> */
    use HasFactory, SoftDeletes;

	public function person(): BelongsTo {
		return $this->belongsTo(Person::class);
	}

	public function childrenRegistry(): BelongsTo {
		return $this->belongsTo(ChildrenRegistry::class);
	}

	public function compulsoryEducationFulfillments(): HasMany {
		return $this->hasMany(CompulsoryEducationFulfillment::class);
	}
}

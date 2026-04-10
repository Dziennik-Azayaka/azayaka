<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompulsoryEducationFulfillment extends Model
{
	/** @use HasFactory<\Database\Factories\CompulsoryEducationFulfillmentFactory> */
	use HasFactory;

	public function student(): BelongsTo
	{
		return $this->belongsTo(Student::class);
	}

	public function childrenRegistry(): BelongsTo
	{
		return $this->belongsTo(ChildrenRegistry::class);
	}
}

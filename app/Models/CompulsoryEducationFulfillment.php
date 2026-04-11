<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompulsoryEducationFulfillment extends Model
{
	/** @use HasFactory<\Database\Factories\CompulsoryEducationFulfillmentFactory> */
	use HasFactory;

	protected $fillable = [
		"student_id",
		"children_registry_id",
		"school_year",
		"control_date",
		"fulfillment_form",
		"level",
		"relationship"
	];

	public function student(): BelongsTo
	{
		return $this->belongsTo(Student::class);
	}

	public function childrenRegistry(): BelongsTo
	{
		return $this->belongsTo(ChildrenRegistry::class);
	}
}

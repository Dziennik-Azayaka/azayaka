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
		"child_id",
		"school_year",
		"control_date",
		"fulfillment_form",
		"level",
		"relationship"
	];

	public function child(): BelongsTo
	{
		return $this->belongsTo(Child::class);
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends BaseModel
{
	/** @use HasFactory<\Database\Factories\StudentFactory> */
	use HasFactory;

	protected $fillable = ["first_name", "last_name", "second_name", "pesel", "alternate_identity_document",
		"birthdate", "birthplace", "gender", "admission_date"];

	function guardians(): HasMany
	{
		return $this->hasMany(Guardian::class);
	}

	public function studentRegistry(): BelongsTo
	{
		return $this->belongsTo(StudentRegistry::class);
	}

	public function childrenRegistry(): BelongsTo
	{
		return $this->belongsTo(ChildrenRegistry::class);
	}

	public function residenceAddress(): BelongsTo
	{
		return $this->belongsTo(
			ResidenceAddress::class,
			'residence_address_id',  // Foreign key on students table
			'id'                     // Primary key on residence_addresses table
		);
	}

	public function compulsoryEducationFulfillment(): HasMany
	{
		return $this->hasMany(CompulsoryEducationFulfillment::class);
	}
}

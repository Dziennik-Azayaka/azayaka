<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends BaseModel
{
	/** @use HasFactory<\Database\Factories\StudentFactory> */
	use HasFactory;

	protected $fillable = ["first_name", "last_name", "second_name", "pesel", "alternate_identity_document",
		"birthdate", "birthplace", "gender", "admission_date"];

	function guardians()
	{
		return $this->belongsToMany(Guardian::class);
	}

	public function studentRegistries(): BelongsToMany
	{
		return $this->belongsToMany(StudentRegistry::class, "student_registry_student");
	}

	public function childrenRegistries(): BelongsToMany
	{
		return $this->belongsToMany(ChildrenRegistry::class, "children_registry_student")->withPivot("id");
	}

	public function residenceAddress(): BelongsTo
	{
		return $this->belongsTo(
			ResidenceAddress::class,
			'residence_address_id',  // Foreign key on students table
			'id'                     // Primary key on residence_addresses table
		);
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
	/** @use HasFactory<\Database\Factories\PersonFactory> */
	use HasFactory;

	protected $fillable = ["first_name", "last_name", "pesel", "birth_date"];

	public function children(): HasMany
	{
		return $this->hasMany(Child::class);
	}

	public function students(): HasMany
	{
		return $this->hasMany(Student::class);
	}

	public function guardians(): HasMany
	{
		return $this->hasMany(Guardian::class);
	}

	public function schoolUnit(): BelongsTo {
		return $this->belongsTo(SchoolUnit::class);
	}

	public function residenceAddress(): BelongsTo
	{
		return $this->belongsTo(
			ResidenceAddress::class,
			"residence_address_id"
		);
	}
}

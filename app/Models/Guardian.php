<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guardian extends BaseModel
{
    /** @use HasFactory<\Database\Factories\GuardianFactory> */
    use HasFactory;
	protected $fillable = ["first_name", "last_name", "phone_number", "email"];

	function student() {
		return $this->belongsTo(Student::class);
	}

	public function residenceAddress(): BelongsTo
	{
		return $this->belongsTo(
			ResidenceAddress::class,
			'residence_address_id'
		);
	}
}

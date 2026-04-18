<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidenceAddress extends Model
{
    /** @use HasFactory<\Database\Factories\ResidenceAddressFactory> */
    use HasFactory;

	protected $fillable = ["country", "commune", "town", "postal_code", "street", "house_number", "flat_number"];
	public function students()
	{
		return $this->hasMany(Student::class, 'residence_address_id');
	}
}

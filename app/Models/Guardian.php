<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends BaseModel
{
    /** @use HasFactory<\Database\Factories\GuardianFactory> */
    use HasFactory;
	protected $fillable = ["first_name", "last_name", "phone_number", "email"];

	function student() {
		return $this->belongsTo(Student::class);
	}
}

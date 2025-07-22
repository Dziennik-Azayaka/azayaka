<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountAccess extends Model
{
    /** @use HasFactory<\Database\Factories\ActivationCodeFactory> */
    use HasFactory;

	public function student() {
		return $this->hasOne(Student::class);
	}

	public function employee() {
		return $this->hasOne(Employee::class);
	}

	public function user() {
		return $this->belongsTo(User::class);
	}
}

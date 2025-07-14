<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivationCode extends Model
{
    /** @use HasFactory<\Database\Factories\ActivationCodeFactory> */
    use HasFactory;

	public function student() {
		return $this->belongsTo(Student::class);
	}

	public function teacher() {
		return $this->belongsTo(Teacher::class);
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountAccess extends BaseModel
{
    /** @use HasFactory<\Database\Factories\AccountAccessFactory> */
    use HasFactory;

	public function student() {
		return $this->hasOne(Student::class, "id", "student_id");
	}

	public function guardian() {
		return $this->hasOne(Guardian::class, "id", "guardian_id");
	}

	public function employee() {
		return $this->hasOne(Employee::class, "id", "employee_id");
	}

	public function user() {
		return $this->belongsTo(User::class, "id", "user_id");
	}
}

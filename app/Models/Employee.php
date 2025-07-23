<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends BaseModel
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;

	public function user() {
		return $this->belongsTo(User::class);
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassUnit extends Model
{
    /** @use HasFactory<\Database\Factories\ClassUnitFactory> */
    use HasFactory;

	public function employees() {
		return $this->hasMany(Employee::class);
	}
}

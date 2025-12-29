<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassUnit extends Model
{
    /** @use HasFactory<\Database\Factories\ClassUnitFactory> */
    use HasFactory;

	protected $fillable = ["alias", "school_unit_id", "mark", "starting_school_year", "teaching_cycle_length"];
	public function employees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Employee::class, "class_units_employees", "class_unit_id", "employee_id");
	}
}

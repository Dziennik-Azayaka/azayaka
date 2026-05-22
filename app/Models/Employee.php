<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends BaseModel
{
	/** @use HasFactory<\Database\Factories\EmployeeFactory> */
	use HasFactory;

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function classUnits()
	{
		return $this->belongsToMany(ClassUnit::class, "class_units_employees", "employee_id", "class_unit_id");
	}

	public function taughtGroupSubjects(): BelongsToMany
	{
		return $this->belongsToMany(GradebookGroupSubject::class, "employee_gradebook_group_subject");
	}
}

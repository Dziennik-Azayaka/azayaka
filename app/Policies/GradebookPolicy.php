<?php

namespace App\Policies;

use App\Models\ClassUnit;
use App\Models\Employee;
use App\Models\Gradebook;
use DB;

class GradebookPolicy
{
	public function create(Employee $employee, ClassUnit $classUnit): bool
	{
		if ($employee->is_admin || $employee->is_headmaster) {
			return true;
		}

		return DB::table("class_units_form_tutors")
			->where("employee_id", $employee->id)
			->where("class_unit_id", $classUnit->id)
			->where("date_from", "<=", now())
			->where(function ($query) {
				$query->whereNull("date_to")->orWhere("date_to", ">=", now());
			})
			->exists();
	}

	public function manageStudents(Employee $employee, Gradebook $gradebook): bool
	{
		return $this->isAuthorisedToManage($employee, $gradebook);
	}

	public function manageGroups(Employee $employee, Gradebook $gradebook): bool
	{
		return $this->isAuthorisedToManage($employee, $gradebook);
	}

	private function isAuthorisedToManage(Employee $employee, Gradebook $gradebook): bool
	{
		if ($employee->is_admin || $employee->is_headmaster) {
			return true;
		}

		return $this->isFormTutorOfGradebook($employee, $gradebook);
	}

	private function isFormTutorOfGradebook(Employee $employee, Gradebook $gradebook): bool
	{
		return DB::table("class_units_form_tutors")
			->join("gradebooks", "class_units_form_tutors.class_unit_id", "=", "gradebooks.class_unit_id")
			->where("gradebooks.id", $gradebook->id)
			->where("class_units_form_tutors.employee_id", $employee->id)
			->where("class_units_form_tutors.date_from", "<=", now())
			->where(function ($query) {
				$query->whereNull("date_to")->orWhere("date_to", ">=", now());
			})
			->exists();
	}
}

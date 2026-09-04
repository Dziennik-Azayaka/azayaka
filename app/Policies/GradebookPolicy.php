<?php

namespace App\Policies;

use App\Models\ClassUnit;
use App\Models\Employee;
use App\Models\Gradebook;

class GradebookPolicy
{
	public function create(Employee $employee, ClassUnit $classUnit): bool
	{
		if ($employee->is_admin || $employee->is_headmaster) {
			return true;
		}

		return $classUnit->formTutors()
			->whereKey($employee->id)
			->whereHas("employee", function ($query) {
				$query->activeFormTutor();
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
		return $gradebook->classUnit()
			->whereHas("formTutors", function ($query) use ($employee) {
				$query->whereKey($employee->id)
					->activeFormTutor();
			})
			->exists();
	}
}

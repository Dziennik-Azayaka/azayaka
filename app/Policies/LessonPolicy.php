<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\Lesson;

class LessonPolicy
{
	public function create(Employee $employee, Lesson $lesson): bool
	{
		return $this->isAuthorisedToManage($employee, $lesson);
	}

	public function update(Employee $employee, Lesson $lesson): bool
	{
		return $this->isAuthorisedToManage($employee, $lesson);
	}

	public function markAsCompleted(Employee $employee, Lesson $lesson): bool
	{
		return $this->isAuthorisedToManage($employee, $lesson);
	}

	public function editAttendance(Employee $employee, Lesson $lesson): bool
	{
		if ($lesson->primary_teacher_id === $employee->id) {
			return true;
		}

		return $lesson->gradebooks()
			->whereHas("classUnit.formTutors", function ($query) use ($employee) {
				$query->where("employee_id", $employee->id)
					->activeFormTutor();
			})
			->exists();
	}

	protected function isAuthorisedToManage(Employee $employee, Lesson $lesson): bool
	{
		return $lesson->primary_teacher_id === $employee->id;
	}
}

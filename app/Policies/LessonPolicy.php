<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\Lesson;
use DB;

class LessonPolicy
{
	public function create(Employee $employee, Lesson $lesson): bool
	{
		return $lesson->primary_teacher_id === $employee->id;
	}

	public function update(Employee $employee, Lesson $lesson): bool
	{
		return $this->create($employee, $lesson);
	}

	public function markAsCompleted(Employee $employee, Lesson $lesson): bool
	{
		return $this->update($employee, $lesson);
	}

	public function editAttendance(Employee $employee, Lesson $lesson): bool
	{
		if ($lesson->primary_teacher_id === $employee->id) {
			return true;
		}

		return DB::table("class_units_form_tutors")
			->join("gradebooks", "class_units_form_tutors.class_unit_id", "=", "gradebooks.class_unit_id")
			->join("lessons_gradebooks", "gradebooks.id", "=", "lessons_gradebooks.gradebook_id")
			->where("lessons_gradebooks.lesson_id", $lesson->id)
			->where("class_units_form_tutors.employee_id", $employee->id)
			->where("class_units_form_tutors.date_from", "<=", $lesson->date)
			->where("class_units_form_tutors.date_to", ">=", $lesson->date)
			->exists();
	}
}

<?php

namespace App\Services;

use App\Exceptions\InvalidAccessIdException;
use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\Guardian;
use App\Models\Student;

class AccessContext
{
	private ?AccountAccess $access = null;

	public function set(AccountAccess $access): void
	{
		$this->access = $access;
	}

	public function hasAccess(): bool
	{
		return $this->access !== null;
	}

	public function currentAccountAccess(): AccountAccess
	{
		if ($this->access === null) {
			throw new InvalidAccessIdException();
		}
		return $this->access;
	}

	public function currentEmployee(): Employee
	{
		$employee = $this->currentAccountAccess()->employee;
		if ($employee === null) {
			throw new InvalidAccessIdException();
		}
		return $employee;
	}

	public function currentStudent(): Student
	{
		$student = $this->currentAccountAccess()->student;
		if ($student === null) {
			throw new InvalidAccessIdException();
		}
		return $student;
	}

	public function currentGuardian(): Guardian
	{
		$guardian = $this->currentAccountAccess()->guardian;
		if ($guardian === null) {
			throw new InvalidAccessIdException();
		}
		return $guardian;
	}

	public function personaType(): ?string
	{
		if ($this->access === null) {
			return null;
		}
		if ($this->access->employee_id !== null) return "employee";
		if ($this->access->student_id !== null) return "student";
		if ($this->access->guardian_id !== null) return "guardian";
		return null;
	}
}

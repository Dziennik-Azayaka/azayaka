<?php

namespace Tests;

use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	protected User $actingAdminUser;
	protected User $actingStudentUser;
	protected int $actingAdminUserAccessId;
	protected int $actingStudentUserAccessId;

	protected function actingAdminUser(): User
	{
		$user = User::factory()->create();
		$this->be($user);
		$this->actingAdminUser = $user;
		$employee = Employee::factory()->create([
			"is_admin" => true,
			"is_secretary" => true,
			"is_teacher" => true
		]);
		$accountAccess = AccountAccess::create();
		$accountAccess->employee_id = $employee->id;
		$accountAccess->user_id = $user->id;
		$accountAccess->save();
		$this->actingAdminUserAccessId = $accountAccess->id;
		$this->withHeaders([
			"Accept" => "application/json",
			"Access-ID" => $this->actingAdminUserAccessId
		]);
		return $user;
	}

	protected function actingStudent(): User
	{
		$user = User::factory()->create();
		$this->be($user);
		$this->actingStudentUser = $user;
		$student = Student::factory()->create();
		$accountAccess = AccountAccess::create();
		$accountAccess->student_id = $student->id;
		$accountAccess->user_id = $user->id;
		$accountAccess->save();
		$this->actingStudentUserAccessId = $accountAccess->id;
		$this->withHeaders([
			"Accept" => "application/json",
			"Access-ID" => $this->actingStudentUserAccessId
		]);
		return $user;
	}
}

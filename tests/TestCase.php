<?php

namespace Tests;

use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	protected User $actingUser;
	protected int $actingUserAccessId;

	protected function actingUser(): User
	{
		$user = User::factory()->create();
		$this->be($user);
		$this->actingUser = $user;
		$employee = Employee::factory()->create([
			"is_admin" => true,
			"is_secretary" => true
		]);
		$accountAccess = AccountAccess::create();
		$accountAccess->employee_id = $employee->id;
		$accountAccess->user_id = $user->id;
		$accountAccess->save();
		$this->actingUserAccessId = $accountAccess->id;
		$this->withHeaders([
			"Accept" => "application/json",
			"Access-ID" => $this->actingUserAccessId
		]);
		return $user;
	}
}

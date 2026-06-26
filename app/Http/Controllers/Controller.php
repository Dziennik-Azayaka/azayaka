<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Models\AccountAccess;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

abstract class Controller
{
	protected function getUserEmployee(Request $request): Employee
	{
		$accessID = $request->header("Access-ID") ?? $request->route("accessId");
		$employeeAccess = AccountAccess::where("user_id", $request->user()->id)
			->where("id", $accessID)
			->with("employee")->first();
		return $employeeAccess->employee;
	}

	protected function authorize(string $ability, mixed $arguments = []): void
	{
		$employee = $this->getUserEmployee(request());
		if (!Gate::forUser($employee)->allows($ability, $arguments)) {
			response()->json([
				"success" => false,
				"errors" => [
					"UNAUTHORIZED_TO_PERFORM_ACTION"
				]
			], 403)->throwResponse();
		}
	}
}

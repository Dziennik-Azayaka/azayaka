<?php

namespace App\Http\Middleware;

use App\Models\AccountAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeHasRole
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
	 */
	public function handle(Request $request, Closure $next, string $module): Response
	{
		$accessID = $request->header("Access-ID") ?? $request->route("accessId");
		$employeeAccess = AccountAccess::where("user_id", $request->user()->id)
			->where("id", $accessID)
			->with("employee")->first();


		if (!$employeeAccess) return $this->returnForbiddenResponse($request->wantsJson());

		$hasAccess = false;
		/* not having breaks is fine in this scenario, because only one of these needs to pass - we don't want to stop
		a user when the route allows admins or teachers, but the check fails because of the
		break statement in the admin case. */
		switch ($module) {
			/** @noinspection PhpMissingBreakStatementInspection */
			case "administrator":
				if ($employeeAccess->employee->is_headmaster || $employeeAccess->employee->is_admin) $hasAccess = true;
			/** @noinspection PhpMissingBreakStatementInspection */
			case "headmaster":
				if ($employeeAccess->employee->is_headmaster) $hasAccess = true;
			/** @noinspection PhpMissingBreakStatementInspection */
			case "secretary":
				if ($employeeAccess->employee->is_headmaster || $employeeAccess->employee->is_secretary) $hasAccess = true;
			case "teacher":
				if ($employeeAccess->employee->is_teacher) $hasAccess = true;
		}

		if (!$hasAccess) return $this->returnForbiddenResponse($request->wantsJson());

		return $next($request);
	}

	private function returnForbiddenResponse(bool $wantsJson): Response
	{
		if ($wantsJson) {
			return response()->json([
				"success" => false,
				"errors" => ["INVALID_ACCESS_ID_OR_INSUFFICIENT_PRIVILEGES"]
			], 403);
		}
		abort(403);
	}
}

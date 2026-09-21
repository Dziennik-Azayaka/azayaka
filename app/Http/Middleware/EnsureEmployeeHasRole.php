<?php

namespace App\Http\Middleware;

use App\Services\AccessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeHasRole
{
	public function __construct(private AccessContext $context) {}

	/**
	 * Handle an incoming request.
	 *
	 * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
	 */
	public function handle(Request $request, Closure $next, string $module): Response
	{
		if (!$this->context->hasAccess()) {
			return $this->returnForbiddenResponse($request->wantsJson());
		}

		$employee = $this->context->currentEmployee();

		$hasAccess = false;
		/* not having breaks is fine in this scenario, because only one of these needs to pass - we don't want to stop
		a user when the route allows admins or teachers, but the check fails because of the
		break statement in the admin case. */
		switch ($module) {
			/** @noinspection PhpMissingBreakStatementInspection */
			case "administrator":
				if ($employee->is_headmaster || $employee->is_admin) $hasAccess = true;
			/** @noinspection PhpMissingBreakStatementInspection */
			case "headmaster":
				if ($employee->is_headmaster) $hasAccess = true;
			/** @noinspection PhpMissingBreakStatementInspection */
			case "secretary":
				if ($employee->is_headmaster || $employee->is_secretary) $hasAccess = true;
			case "teacher":
				if ($employee->is_teacher) $hasAccess = true;
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

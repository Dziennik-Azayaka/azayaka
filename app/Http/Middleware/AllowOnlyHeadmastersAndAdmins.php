<?php

namespace App\Http\Middleware;

use App\Models\AccountAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowOnlyHeadmastersAndAdmins
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$accessID = $request->header("Access-ID") ?? $request->route("accessId");
		$employeeAccess = AccountAccess::where("user_id", $request->user()->id)
			->where("id", $accessID)
			->with("employee")->first();
		if (!$employeeAccess || !($employeeAccess->employee->is_headmaster || $employeeAccess->employee->is_admin)) {
			abort(Response::HTTP_FORBIDDEN);
		}
		return $next($request);
	}
}

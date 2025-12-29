<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowOnlyHeadmastersAndAdmins
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
		$employeeAccesses = $request->user()->employees;
		$hasAppropriateAccess = false;
		foreach ($employeeAccesses as $employeeAccess) {
			if ($employeeAccess->is_headmaster || $employeeAccess->is_admin) {
				$hasAppropriateAccess = true;
				break;
			}
		}
		if (!$hasAppropriateAccess) {
			abort(Response::HTTP_FORBIDDEN);
		}
        return $next($request);
    }
}

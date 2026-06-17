<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidAccessIdException;
use App\Models\AccountAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowOnlyStudentsOrGuardians
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Closure(Request): (Response) $next
	 * @throws InvalidAccessIdException
	 */
    public function handle(Request $request, Closure $next): Response
    {
		$accessID = $request->header("Access-ID") ?? $request->route("accessId");
		$studentAccess = AccountAccess::where("user_id", $request->user()->id)
			->where("id", $accessID)->first();
		if (!$studentAccess) throw new InvalidAccessIdException();

		return $next($request);
    }
}

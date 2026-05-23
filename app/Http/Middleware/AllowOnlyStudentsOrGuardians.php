<?php

namespace App\Http\Middleware;

use App\Models\AccountAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowOnlyStudentsOrGuardians
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
		$accessID = $request->header("Access-ID") ?? $request->route("accessId");
		$studentAccess = AccountAccess::where("user_id", $request->user()->id)
			->where("id", $accessID)->first();
		if (!$studentAccess) return $this->returnForbiddenResponse($request->wantsJson());

		return $next($request);
    }

	// TODO: Abstract this to an exception
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

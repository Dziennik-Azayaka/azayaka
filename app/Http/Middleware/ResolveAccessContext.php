<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidAccessIdException;
use App\Models\AccountAccess;
use App\Services\AccessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class ResolveAccessContext
{
	public function __construct(private AccessContext $context) {}

	/**
	 * Handle an incoming request.
	 *
	 * @param Closure(Request): (Response) $next
	 * @throws InvalidAccessIdException
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$accessID = $request->header("Access-ID") ?? $request->route("accessId");

		if ($accessID === null) {
			return $next($request);
		}

		$access = AccountAccess::where("user_id", $request->user()->id)
			->where("id", $accessID)
			->with(["employee", "student", "guardian"])
			->first();

		if (!$access) {
			throw new InvalidAccessIdException();
		}

		$this->context->set($access);

		return $next($request);
	}
}

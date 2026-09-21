<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidAccessIdException;
use App\Services\AccessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowOnlyStudentsOrGuardians
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
		$persona = $this->context->personaType();
		if ($persona !== "student" && $persona !== "guardian") {
			throw new InvalidAccessIdException();
		}

		return $next($request);
	}
}

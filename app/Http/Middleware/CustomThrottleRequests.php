<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\MissingRateLimiterException;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

class CustomThrottleRequests extends ThrottleRequests
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
	 * @throws MissingRateLimiterException
	 */
	public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = ""): RedirectResponse|Response
	{
		$key = $prefix.$this->resolveRequestSignature($request);

		$maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

		if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
			$retryAfter = $this->getTimeUntilNextRetry($key);
			return \Response::json(
				[
					"success" => false,
					"retryAfter" => $retryAfter,
					"errors" => [
						"TOO_MANY_REQUESTS"
					]
				], 429);
		}

		$this->limiter->hit($key, $decayMinutes * 60);

		$response = $next($request);

		return $this->addHeaders(
			$response, $maxAttempts,
			$this->calculateRemainingAttempts($key, $maxAttempts)
		);
	}
}

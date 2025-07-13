<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DenyIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
		if (Auth::guard($guard)->check()) {
			return \Illuminate\Support\Facades\Response::json([
				"success" => false,
				"errors" => ["ALREADY_LOGGED_IN"],
			], 401);
		}

        return $next($request);
    }
}

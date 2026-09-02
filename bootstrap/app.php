<?php

use App\Http\Middleware\AllowOnlyStudentsOrGuardians;
use App\Http\Middleware\EnsureEmployeeHasRole;
use App\Http\Middleware\CustomThrottleRequests;
use App\Http\Middleware\DenyIfAuthenticated;
use App\Http\Middleware\ResolveAccessContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
			"access.context" => ResolveAccessContext::class,
			"students.guardians" => AllowOnlyStudentsOrGuardians::class,
			"auth.deny" => DenyIfAuthenticated::class,
			"employee.role" => EnsureEmployeeHasRole::class,
			"throttle" => CustomThrottleRequests::class
		]);
			$middleware->validateCsrfTokens(except: [
				"*"
			]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response) {
			if ($response->getStatusCode() == 401) {
				return \Illuminate\Support\Facades\Response::json([
					"success" => false,
					"errors" => [
						"USER_NOT_LOGGED_IN"
					]
				], 401);
			} else if ($response->getStatusCode() == 500) {
				return \Illuminate\Support\Facades\Response::json([
					"success" => false,
					"errors" => [
						"UNKNOWN_SERVER_ERROR"
					]
				], 500);
			}

			return $response;
		});

		$exceptions->renderable(function (\Illuminate\Validation\ValidationException $exception, $request) {
			if (!$request->wantsJson()) {
				return null; // return null to display the default Laravel error page
			}

			throw \App\Exceptions\CustomValidationException::withMessages(
				$exception->validator?->errors()?->toArray() ?? ["UNKNOWN_ERROR"]
			);
		});

		$exceptions->renderable(function (AccessDeniedHttpException $exception, $request) {
			if (!$request->wantsJson()) {
				return null;
			}

			return \Illuminate\Support\Facades\Response::json([
				"success" => false,
				"errors" => ["UNAUTHORIZED_TO_PERFORM_ACTION"]
			], 403);
		});
    })->create();

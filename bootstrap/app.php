<?php

use App\Http\Middleware\CustomThrottleRequests;
use App\Http\Middleware\DenyIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Middleware\ThrottleRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
			"auth.deny" => DenyIfAuthenticated::class,
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
			}

			return $response;
		});
    })->create();

<?php

use App\Http\Middleware\CustomThrottleRequests;
use App\Http\Middleware\DenyIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

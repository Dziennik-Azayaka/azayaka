<?php

namespace App\Http\Controllers;

use App\Services\AccessContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
	use AuthorizesRequests;

	public function authorize($ability, $arguments = [])
	{
		[$ability, $arguments] = $this->parseAbilityAndArguments($ability, $arguments);

		$context = app(AccessContext::class);

		if ($context->hasAccess() && $context->personaType() === "employee") {
			return $this->authorizeForUser($context->currentEmployee(), $ability, $arguments);
		}

		return app(Gate::class)->authorize($ability, $arguments);
	}
}

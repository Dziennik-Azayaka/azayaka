<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvalidAccessIdException extends Exception
{
	public function render(Request $request)
	{
		if ($request->wantsJson()) {
			return new JsonResponse([
				"success" => false,
				"errors" => ["INVALID_ACCESS_ID_OR_INSUFFICIENT_PRIVILEGES"]
			], Response::HTTP_FORBIDDEN);
		}
		abort(403);
	}
}

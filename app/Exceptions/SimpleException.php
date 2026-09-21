<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class SimpleException extends Exception
{
	protected $message = "UNKNOWN_SEVER_ERROR";
	protected int $status = Response::HTTP_INTERNAL_SERVER_ERROR;

	public function render(Request $request)
	{
		if ($request->wantsJson()) {
			return new JsonResponse([
				"success" => false,
				"errors" => [$this->message]
			], $this->status);
		}

		abort($this->status);
	}
}

<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistryArchivedException extends Exception
{
	public function __construct(?string $registryType = null)
	{
		if ($registryType === "student")
			$message = "Student_REGISTRY_ARCHIVED";
		else if ($registryType === "children")
			$message = "CHILDREN_REGISTRY_ARCHIVED";
		else
			$message = "REGISTRY_ARCHIVED";
		parent::__construct($message);
	}

	public function render(Request $request)
	{
		if ($request->wantsJson()) {
			return new JsonResponse([
				"success" => false,
				"errors" => [$this->message]
			], Response::HTTP_CONFLICT);
			// 409 Conflict, because 423 Locked is part of WebDAV.
			// The request is well-formed, and the resource exists, but the current state of the resource (archived)
			// conflicts with the requested operation.
		}
		abort(403);
	}
}

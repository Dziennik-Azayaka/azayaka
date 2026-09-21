<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistryArchivedException extends SimpleException
{
	protected int $status = Response::HTTP_CONFLICT;

	public function __construct(?string $registryType = null)
	{
		if ($registryType === "student")
			$this->message = "STUDENT_REGISTRY_ARCHIVED";
		else if ($registryType === "children")
			$this->message = "CHILDREN_REGISTRY_ARCHIVED";
		else
			$this->message = "REGISTRY_ARCHIVED";
		parent::__construct();
	}
}

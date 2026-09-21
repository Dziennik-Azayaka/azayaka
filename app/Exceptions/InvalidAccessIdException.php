<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvalidAccessIdException extends SimpleException
{
	protected $message = "INVALID_ACCESS_ID_OR_INSUFFICIENT_PRIVILEGES";
	protected int $status = Response::HTTP_FORBIDDEN;
}

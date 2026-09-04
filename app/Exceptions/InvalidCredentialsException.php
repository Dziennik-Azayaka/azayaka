<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidCredentialsException extends SimpleException
{
    protected $message = "INVALID_CREDENTIALS";
	protected int $status = Response::HTTP_FORBIDDEN;
}

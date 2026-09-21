<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UnknownServerErrorException extends SimpleException
{
    protected $message = "UNKNOWN_SERVER_ERROR";
	protected int $status = Response::HTTP_INTERNAL_SERVER_ERROR;
}

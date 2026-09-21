<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends SimpleException
{
	protected $message = "NOT_FOUND";
	protected int $status = Response::HTTP_NOT_FOUND;

	public function __construct(?string $entity = null)
	{
		$this->message = $entity ? $entity. "_NOT_FOUND" : $this->message;
		parent::__construct($this->message);
	}
}

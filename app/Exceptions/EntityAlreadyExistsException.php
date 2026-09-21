<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class EntityAlreadyExistsException extends SimpleException
{
	protected $message = "ENTITY_ALREADY_EXISTS";
	protected int $status = Response::HTTP_CONFLICT;

	public function __construct(?string $entity = null)
	{
		$this->message = $entity ? $entity . "_ALREADY_EXISTS" : $this->message;
		parent::__construct($this->message);
	}
}

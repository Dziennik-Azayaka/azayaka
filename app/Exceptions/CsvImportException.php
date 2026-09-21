<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CsvImportException extends Exception
{
	protected array $messages;

	public function __construct(array $messages)
	{
		$this->messages = $messages;
		parent::__construct($messages[0]);
	}

	public function render(): JsonResponse
	{
		return new JsonResponse([
			"success" => false,
			"errors" => $this->messages
		], \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
	}
}

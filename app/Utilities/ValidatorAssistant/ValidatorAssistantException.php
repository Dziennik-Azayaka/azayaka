<?php

namespace App\Utilities\ValidatorAssistant;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class ValidatorAssistantException extends ValidationException {
	/**
	 * Create a new exception instance.
	 *
	 * @param Validator|null $validator
	 * @param JsonResponse|null $response
	 * @param array $errorCodes
	 */
	public function __construct(?Validator $validator = null, ?JsonResponse $response = null, array $errorCodes = []) {
		parent::__construct($validator);
		if ($response == null) {
			$response = Response::json([
				"success" => false,
				"errors" => $errorCodes
			], 422);
		}

		$this->validator = $validator;
		$this->response = $response;
		$this->status = $response->getStatusCode();
	}

	// Override the base class method which summarizes the errors. Not needed here, as we generate our own error
	// messages anyway, and keeping the base function makes it incompatible with our constructor (nullable validator).
	protected static function summarize($validator): string
	{
		return "An error occured during validation.";
	}
}

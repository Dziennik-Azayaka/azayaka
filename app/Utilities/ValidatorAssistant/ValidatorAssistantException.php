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
	 * @param Validator $validator
	 * @param JsonResponse|null $response
	 * @param array $errorCodes
	 */
	public function __construct(Validator $validator, ?JsonResponse $response = null, array $errorCodes = []) {
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
}

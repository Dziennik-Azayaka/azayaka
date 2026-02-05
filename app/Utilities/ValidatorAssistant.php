<?php

namespace App\Utilities;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ValidatorAssistant
{
	private static function toUppercaseWithUnderscores($string): string
	{
		// Remove leading/trailing whitespace
		$string = trim($string);

		// Replace multiple consecutive spaces/whitespace with single space
		$string = preg_replace('/\s+/', ' ', $string);

		// Handle camelCase and PascalCase by inserting spaces before uppercase letters
		$string = preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);

		// Replace any non-alphanumeric characters (except spaces) with spaces
		$string = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $string);

		// Replace spaces with underscores
		$string = str_replace(' ', '_', $string);

		// Convert to uppercase
		$string = strtoupper($string);

		// Remove any duplicate underscores
		$string = preg_replace('/_+/', '_', $string);

		// Remove leading/trailing underscores
		return trim($string, '_');
	}


	static function validate(Request $request, array $rules): array
	{
		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$errors = $validator->errors()->toArray();
			$errorsFields = array_keys($errors);
			$errorCodes = [];

			foreach ($errorsFields as $field) {
				$fieldErrors = $errors[$field];
				foreach ($fieldErrors as $fieldError) {
					if (str_contains($fieldError, "password is incorrect")) {
						$errorCodes[] = "WRONG_PASSWORD";
					} else if (str_contains($fieldError, "must be at least")) {
						$errorCodes[] = strtoupper($field) . "_TOO_SHORT";
					} else if (str_contains($fieldError, "must not be greater")) {
						$errorCodes[] = strtoupper($field) . "_TOO_LONG";
					} else if (str_contains($fieldError, "must be a number")) {
						$errorCodes[] = strtoupper($field) . "_MUST_BE_A_NUMBER";
					} else if (str_contains($fieldError, "is required")) {
						$errorCodes[] = strtoupper($field) . "_IS_REQUIRED";
					} else if (str_contains($fieldError, "has already been taken")) {
						$errorCodes[] = strtoupper($field) . "_TAKEN";
					} else if (str_contains($fieldError, "email")) {
						$errorCodes[] = strtoupper($field) . "_MUST_BE_AN_EMAIL";
					} else if (str_contains($fieldError, "true or false")) {
						$errorCodes[] = strtoupper($field) . "_MUST_BE_A_BOOLEAN";
					} else {
						$errorCodes[] = self::toUppercaseWithUnderscores($field) . "_" . self::toUppercaseWithUnderscores($fieldError);
					}
				}
			}

			return [
				"success" => false,
				"errorResponse" => Response::json([
					"success" => false,
					"errors" => $errorCodes
				], 400)
			];
		}

		return [
			"success" => true,
			"data" => $validator->validated()
		];
	}
}

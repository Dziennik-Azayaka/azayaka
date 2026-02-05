<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Pesel implements ValidationRule
{
	/**
	 * Run the validation rule.
	 *
	 * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
	 */
	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		$length = strlen($value);
		if ($length !== 11) {
			$fail("PESEL_MUST_BE_11_CHARACTERS_LONG");
		}

		$multipliers = [1, 3, 7, 9];
		$sum = 0;
		for ($i = 0; $i < $length - 1; $i++) {
			if (!is_numeric($value[$i])) $fail("INVALID_PESEL_CHARACTER");
			$sum += ((int)$value[$i]) * $multipliers[$i % 4];
		}

		$checksum = (10 - ($sum % 10)) % 10;
		if ($checksum !== (int)$value[$length - 1]) {
			$fail("INVALID_PESEL_CHECKSUM");
		}
	}
}

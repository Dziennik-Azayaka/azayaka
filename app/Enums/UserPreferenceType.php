<?php

namespace App\Enums;

/**
 * This enum is used to define the available keys of user preferences.
 * It is backed by a string which contains the validation rules for the value, eg. "string|max:3"
 * This is the only place in the application where column-seperated rules are used, as PHP enums can only be backed by strings or ints.
 * @see https://laravel.com/docs/validation#available-validation-rules
 */
enum UserPreferenceType: string
{
    case AutofillAttendance = "boolean";

	public static function tryFromName(string $name): ?self
	{
		foreach (self::cases() as $case) {
			if ($case->name === $name) {
				return $case;
			}
		}
		return null;
	}
}

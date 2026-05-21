<?php

namespace App\Utilities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class CaseConverter
{
	private static function convertArray(array|Collection|SupportCollection $array, \Closure $callback): array
	{
		if (!is_array($array)) {
			$array = $array->toArray();
		}

		$result = [];
		foreach ($array as $key => $value) {
			if (!is_string($key)) {
				$newKey = $key;
			} else {
				$newKey = $callback($key);
			}

			if (is_array($value)) {
				$value = CaseConverter::convertArray($value, $callback);
			}

			if ($value instanceof SupportCollection) {
				$value = CaseConverter::convertArray($value->toArray(), $callback);
			}

			if ($value instanceof \stdClass || $value instanceof BaseModel) {
				$value = CaseConverter::convertArray((array)$value, $callback);
			}

			$result[$newKey] = $value;
		}

		return $result;
	}

	static function toSnakeCase(array|Collection|SupportCollection $array): array
	{
		return self::convertArray($array, fn($key) => strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key)));
	}

	static function toCamelCase(array|Collection|SupportCollection $array): array
	{
		return self::convertArray($array,
			fn($key) => lcfirst(str_replace('_', '', ucwords($key, '_'))));
	}
}

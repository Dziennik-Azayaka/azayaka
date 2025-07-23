<?php

namespace App\Utilities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class ArrayCameliser
{
	static function camelise(array|Collection|SupportCollection $array): array
	{
		if (!is_array($array)) {
			$array = $array->toArray();
		}

		$result = [];
		foreach ($array as $key => $value) {
			if (!is_string($key)) {
				$newKey = $key;
			} else {
				$newKey = '';
				$upperNextChar = false;
				$keyLength = strlen($key);

				for ($i = 0; $i < $keyLength; $i++) {
					$char = $key[$i];

					if ($char === '_') {
						$upperNextChar = true;
						continue;
					}

					if ($upperNextChar) {
						$newKey .= strtoupper($char);
						$upperNextChar = false;
					} else {
						$newKey .= $char;
					}
				}
			}

			if (is_array($value)) {
				$value = ArrayCameliser::camelise($value);
			}

			if ($value instanceof SupportCollection) {
				$value = ArrayCameliser::camelise($value->toArray());
			}

			if ($value instanceof \stdClass || $value instanceof BaseModel) {
				$value = ArrayCameliser::camelise((array) $value);
			}

			$result[$newKey] = $value;
		}

		return $result;
	}
}

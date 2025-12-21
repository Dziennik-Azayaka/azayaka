<?php

namespace App\Utilities;

class ClassificationPeriodValidator
{
	static function validate(Array $periods): ?\Illuminate\Http\JsonResponse
	{
		if (count($periods["periodStart"]) != count($periods["periodEnd"])) {
			return \Response::json([
				"success" => false,
				"errors" => ["PERIOD_START_AND_END_DATES_NOT_EQUAL_IN_LENGTH"]
			]);
		}

		if (!str_contains($periods["periodStart"][0], "-09-01")) {
			return \Response::json([
				"success" => false,
				"errors" => ["FIRST_PERIOD_START_MUST_BE_1ST_OF_SEPTEMBER"]
			]);
		}

		$count = count($periods["periodStart"]);

		if (!str_contains($periods["periodEnd"][$count - 1], "-08-31")) {
			return \Response::json([
				"success" => false,
				"errors" => ["LAST_PERIOD_END_MUST_BE_31ST_OF_AUGUST"]
			]);
		}

		for ($i = 0; $i < $count; $i++) {
			$currentStart = $periods["periodStart"][$i];
			$currentEnd = $periods["periodEnd"][$i];

			if ($currentStart > $currentEnd) {
				return \Response::json([
					"success" => false,
					"errors" => ["PERIOD_START_CANNOT_BE_AFTER_END_DATE"]
				]);
			}

			if ($i > 0) {
				$previousEnd = $periods["periodEnd"][$i - 1];
				if ($currentStart <= $previousEnd) {
					return \Response::json([
						"success" => false,
						"errors" => ["PERIODS_CANNOT_OVERLAP"]
					]);
				}
			}
		}

		return null;
	}
}

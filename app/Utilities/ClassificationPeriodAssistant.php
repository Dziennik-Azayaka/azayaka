<?php

namespace App\Utilities;

use Carbon\Carbon;

class ClassificationPeriodAssistant
{
	public static function validate(array $periodEnds, Int $schoolYear): ?\Illuminate\Http\JsonResponse
	{
		if (count($periodEnds) > 4) {
			return \Response::json([
				"success" => false,
				"errors" => ["TOO_MANY_PERIODS"]
			], 400);
		}

		$periodStart = Carbon::create($schoolYear, 9)->startOfDay();
		$schoolYearEnd = $periodStart->copy();
		$schoolYearEnd->addYear()->month(8)->day(31);

		foreach ($periodEnds as $periodEnd) {
			try {
				$periodEnd = Carbon::parse($periodEnd)->startOfDay();
			} catch (\Exception $e) {
				return \Response::json([
					"success" => false,
					"errors" => ["INVALID_DATE_STRING_FORMAT"]
				], 400);
			}

			if ($periodEnd->lessThanOrEqualTo($periodStart)) {
				return \Response::json([
					"success" => false,
					"errors" => ["PERIODS_OVERLAP"]
				], 400);
			}

			$periodStart = $periodEnd->copy()->addDay();
		}

		if (isset($periodEnd) && $periodEnd->greaterThanOrEqualTo($schoolYearEnd)) {
			return \Response::json([
				"success" => false,
				"errors" => ["LAST_PERIOD_MUST_END_BEFORE_AUGUST_31"]
			], 400);
		}

		return null;
	}

	public static function getCurrentSchoolYear(): int
	{
		$now = Carbon::now();
		if ($now->month > 8) {
			$schoolYear = $now->year;
		} else {
			$schoolYear = $now->year - 1;
		}

		return $schoolYear;
	}
}

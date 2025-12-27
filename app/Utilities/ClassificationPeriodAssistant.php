<?php

namespace App\Utilities;

use Carbon\Carbon;

class ClassificationPeriodAssistant
{
	public static function validate(Array $periodEnds): ?\Illuminate\Http\JsonResponse
	{
		if (count($periodEnds) > 5) {
			return \Response::json([
				"success" => false,
				"errors" => ["TOO_MANY_PERIODS"]
			]);
		}

		$schoolYear = (int) str_split($periodEnds[0], "-")[0];
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
				]);
			}

			if ($periodEnd->lt($periodStart)) {
				return \Response::json([
					"success" => false,
					"errors" => ["PERIODS_OVERLAP"]
				]);
			}

			$periodStart = $periodEnd->copy()->addDay();
		}

		if (isset($periodEnd) && $periodEnd->greaterThanOrEqualTo($schoolYearEnd)) {
			return \Response::json([
				"success" => false,
				"errors" => ["LAST_PERIOD_MUST_END_BEFORE_AUGUST_31"]
			]);
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

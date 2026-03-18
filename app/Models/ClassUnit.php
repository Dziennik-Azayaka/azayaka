<?php

namespace App\Models;

use App\Enums\ClassUnitCategory;
use App\Utilities\ClassificationPeriodAssistant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassUnit extends Model
{
	/** @use HasFactory<\Database\Factories\ClassUnitFactory> */
	use HasFactory;

	protected $fillable = ["alias", "school_unit_id", "mark", "starting_classification_period_id", "teaching_cycle_length"];

	public function employees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Employee::class, "class_units_employees", "class_unit_id", "employee_id");
	}

	public function schoolUnit() {
		return $this->belongsTo(SchoolUnit::class);
	}

	public function startingPeriod()
	{
		return $this->belongsTo(ClassificationPeriod::class, "starting_classification_period_id");
	}

	public function periods() {
		return $this->belongsToMany(ClassificationPeriod::class, "class_units_periods",
			"class_unit_id", "classification_period_id")
			->using(ClassUnitPeriod::class)
			->withPivot("level", "id");
	}

	public function currentPeriodEntry($date = null)
	{
		if ($date == null) {
			$date = now();
		}
		return $this->periods()->where("period_start", "<=", $date)->where("period_end", ">=", $date)->first();
	}

	public function getCurrentLevelAttribute() {
		return $this->currentPeriodEntry()->pivot->level ?? null;
	}

	public function scopeFilterByCategory($query, ClassUnitCategory $category, $currentSchoolYear = null)
	{
		return match ($category) {
			ClassUnitCategory::CURRENT => $query->whereHas("periods", function ($query) {
				$query->active();
			}),
			ClassUnitCategory::ARCHIVE => $query->whereHas("periods")
				->whereDoesntHave("periods", function ($query) {
				$query->where("period_end", ">=", now());
			}),
			ClassUnitCategory::FUTURE => $query->whereHas("startingPeriod", function ($query) {
				$query->where("period_start", ">", now());
			})
		};
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassificationPeriod extends Model
{
	protected $fillable = [
		"school_unit_id", "school_year", "period_number", "period_start", "period_end"
	];

	public function scopeActive($query)
	{
		$now = now();
		return $query->where("period_start", "<=", $now)
			->where("period_end", ">=", $now);
	}
}

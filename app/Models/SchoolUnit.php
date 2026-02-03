<?php

namespace App\Models;

use App\Enums\SchoolType;
use App\Enums\Voivodeship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolUnit extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolUnitFactory> */
    use HasFactory;

	public $casts = [
		"type" => SchoolType::class,
		"voivodeship" => Voivodeship::class,
	];

	public function studentRegistry(): HasOne
	{
		return $this->hasOne(StudentRegistry::class);
	}

	public function childrenRegistry(): HasOne
	{
		return $this->hasOne(ChildrenRegistry::class);
	}
}

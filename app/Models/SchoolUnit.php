<?php

namespace App\Models;

use App\Enums\SchoolType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolUnit extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolUnitFactory> */
    use HasFactory;

	public $casts = [
		"type" => SchoolType::class
	];
}

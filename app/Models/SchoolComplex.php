<?php

namespace App\Models;

use App\Enums\SchoolType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolComplex extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolComplexFactory> */
    use HasFactory;

	public $casts = [
		"type" => SchoolType::class
	];
}

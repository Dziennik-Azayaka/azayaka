<?php

namespace App\Models;

use App\Enums\AttendancePrimitiveType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceComplexType extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceComplexTypeFactory> */
    use HasFactory;

	protected $fillable = [
		"name",
		"shortcut",
		"maps_to_primitive_type",
		"active",
	];

	public $casts = [
		"maps_to_primitive_type" => AttendancePrimitiveType::class,
		"built_in" => "boolean",
		"active" => "boolean"
	];
}

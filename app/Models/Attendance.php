<?php

namespace App\Models;

use App\Enums\AttendancePrimitiveType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
	/** @use HasFactory<\Database\Factories\AttendanceFactory> */
	use HasFactory;

	protected $fillable = [
		'lesson_id',
		'student_id',
		'primitive_type',
		'attendance_complex_type_id',
		'employee_id',
	];

	protected $casts = [
		"primitive_type" => AttendancePrimitiveType::class,
	];

	public function lesson(): BelongsTo
	{
		return $this->belongsTo(Lesson::class);
	}

	public function student(): BelongsTo
	{
		return $this->belongsTo(Student::class);
	}

	public function complexType(): BelongsTo
	{
		return $this->belongsTo(AttendanceComplexType::class, "attendance_complex_type_id");
	}

	public function employee(): BelongsTo
	{
		return $this->belongsTo(Employee::class);
	}
}

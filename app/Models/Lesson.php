<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
	/** @use HasFactory<\Database\Factories\LessonFactory> */
	use HasFactory, SoftDeletes;

	protected $casts = [
		"date" => "date",
		"completed" => "boolean"
	];

	public function gradebooks(): BelongsToMany
	{
		return $this->belongsToMany(
			Gradebook::class,
			"lessons_gradebooks",
			"lesson_id",
			"gradebook_id"
		);
	}

	public function subject(): BelongsTo
	{
		return $this->belongsTo(Subject::class);
	}

	public function primaryTeacher(): BelongsTo
	{
		return $this->belongsTo(Employee::class, "primary_teacher_id");
	}

	public function assistingTeachers(): BelongsToMany
	{
		return $this->belongsToMany(
			Employee::class,
			"lessons_assisting_teachers",
			"lesson_id",
			"employee_id"
		);
	}

	public function gradebookGroups(): BelongsToMany
	{
		return $this->belongsToMany(
			GradebookGroup::class,
			"lessons_gradebook_group",
			"lesson_id",
			"gradebook_group_id"
		);
	}

	public function attendances(): HasMany
	{
		return $this->hasMany(Attendance::class);
	}

	public function students(): BelongsToMany
	{
		return $this->belongsToMany(
			Student::class,
			"attendances",
			"lesson_id",
			"student_id"
		)->withPivot("id", "attendance_complex_type_id", "employee_id", "created_at", "updated_at")
			->withTimestamps();
	}

	public function scopeDateFrom($query, $dateFrom)
	{
		return $query->whereDate("date", ">=", $dateFrom);
	}

	public function scopeDateTo($query, $dateTo)
	{
		return $query->whereDate("date", "<=", $dateTo);
	}

	public function scopeCompleted($query, $completed)
	{
		return $query->where("completed", "=", $completed);
	}

	public function scopeForSubject($query, $subjectId)
	{
		return $query->where("subject_id", "=", $subjectId);
	}

	public function scopeForPrimaryTeacher($query, $primaryTeacherId)
	{
		return $query->where("primary_teacher_id", "=", $primaryTeacherId);
	}

	public function scopeTopicLike($query, $topic)
	{
		return $query->where("topic", "like", "%" . $topic . "%");
	}

	public function scopeForAssistingTeacher($query, $employeeId)
	{
		return $query->whereHas("assistingTeachers", function ($q) use ($employeeId) {
			$q->where("employee_id", "=", $employeeId);
		});
	}
}

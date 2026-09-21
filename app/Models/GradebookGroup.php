<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradebookGroup extends Model
{
	/** @use HasFactory<\Database\Factories\GradebookGroupFactory> */
	use HasFactory;

	protected $fillable = [
		'name',
		'shortcut',
	];

	public function gradebook(): BelongsTo
	{
		return $this->belongsTo(Gradebook::class);
	}

	public function students(): BelongsToMany
	{
		return $this->belongsToMany(Student::class)->withTimestamps();
	}

	public function groupSubjects(): HasMany
	{
		return $this->hasMany(GradebookGroupSubject::class);
	}

	public function lessons(): BelongsToMany
	{
		return $this->belongsToMany(
			Lesson::class,
			"lesson_gradebook_groups",
			"gradebook_group_id",
			"lesson_gradebook_id"
		)->join("lesson_gradebooks", "lesson_gradebooks.id", "=", "lesson_gradebook_groups.lesson_gradebook_id")
			->whereColumn("lesson_gradebooks.lesson_id", "lessons.id")
			->select("lessons.*");
	}
}

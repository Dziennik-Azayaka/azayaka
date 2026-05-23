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
			"lessons_gradebook_group",
			"gradebook_group_id",
			"lesson_id"
		);
	}
}

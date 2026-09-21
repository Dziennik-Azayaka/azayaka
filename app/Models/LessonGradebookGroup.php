<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonGradebookGroup extends Model
{
	/** @use HasFactory<\Database\Factories\LessonGradebookGroupFactory> */
	use HasFactory;

	protected $fillable = ["lesson_gradebook_id", "gradebook_group_id"];

	public function lessonGradebook(): BelongsTo
	{
		return $this->belongsTo(LessonGradebook::class);
	}

	public function gradebookGroup(): BelongsTo
	{
		return $this->belongsTo(GradebookGroup::class);
	}
}

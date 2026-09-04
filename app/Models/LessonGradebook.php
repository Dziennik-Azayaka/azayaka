<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonGradebook extends Model
{
	/** @use HasFactory<\Database\Factories\LessonGradebookFactory> */
	use HasFactory;

	protected $fillable = ["lesson_id", "gradebook_id"];

	public function lesson(): BelongsTo
	{
		return $this->belongsTo(Lesson::class);
	}

	public function gradebook(): BelongsTo
	{
		return $this->belongsTo(Gradebook::class);
	}

	public function groups(): HasMany
	{
		return $this->hasMany(LessonGradebookGroup::class);
	}
}

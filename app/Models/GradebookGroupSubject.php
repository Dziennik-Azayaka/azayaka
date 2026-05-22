<?php

namespace App\Models;

use App\Enums\GradebookSubjectType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GradebookGroupSubject extends Model
{
	use HasFactory;

	protected $fillable = [
		'subject_id',
		'description',
	];

	public $casts = [
		"description" => GradebookSubjectType::class
	];

	public function group(): BelongsTo
	{
		return $this->belongsTo(GradebookGroup::class, "gradebook_group_id");
	}

	public function subject(): BelongsTo
	{
		return $this->belongsTo(Subject::class);
	}

	public function teachers(): BelongsToMany
	{
		return $this->belongsToMany(
			Employee::class,
			"employee_gradebook_group_subject",
			"gradebook_group_subject_id",
			"employee_id"
		)->withTimestamps();
	}
}

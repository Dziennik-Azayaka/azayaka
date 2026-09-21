<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GradebookStudents extends Pivot
{
	protected $table = "gradebooks_students";

	public function student(): BelongsTo
	{
		return $this->belongsTo(Student::class);
	}

	public function gradebook(): BelongsTo
	{
		return $this->belongsTo(Gradebook::class);
	}
}

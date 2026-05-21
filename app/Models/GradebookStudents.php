<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GradebookStudents extends Pivot
{
	protected $table = "gradebooks_students";
}

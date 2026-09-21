<?php

namespace App\Providers;

use App\Models\Gradebook;
use App\Models\Lesson;
use App\Policies\GradebookPolicy;
use App\Policies\LessonPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		Gate::policy(Gradebook::class, GradebookPolicy::class);
		Gate::policy(Lesson::class, LessonPolicy::class);
	}
}

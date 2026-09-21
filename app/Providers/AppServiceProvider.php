<?php

namespace App\Providers;

use App\Services\AccessContext;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(AccessContext::class, fn () => new AccessContext());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
		JsonResource::withoutWrapping();
    }
}

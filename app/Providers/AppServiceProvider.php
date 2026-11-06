<?php

namespace App\Providers;

use App\Support\CampusContext;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->scoped(CampusContext::class);
        URL::defaults(['campus' => config('campus.default_slug')]);
    }
}

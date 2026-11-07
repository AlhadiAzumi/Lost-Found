<?php

namespace App\Providers;

use App\Database\PostgresConnection;
use App\Support\CampusContext;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new PostgresConnection($connection, $database, $prefix, $config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->scoped(CampusContext::class);
        URL::defaults(['campus' => config('campus.default_slug')]);
        Password::defaults(fn () => Password::min(8));
    }
}

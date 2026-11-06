<?php

namespace App\Http\Middleware;

use App\Models\Campus;
use App\Support\CampusContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCampus
{
    public function handle(Request $request, Closure $next): Response
    {
        $campus = $request->route('campus');

        if ($campus === null) {
            $campus = $request->user()?->campus
                ?? Campus::query()->where('slug', config('campus.default_slug'))->first();

            if ($campus !== null) {
                URL::defaults(['campus' => $campus->slug]);
            }

            view()->share('currentCampus', null);

            return $next($request);
        }

        if (! $campus instanceof Campus) {
            $campus = Campus::query()->where('slug', $campus)->firstOrFail();
        }

        abort_unless($campus->is_active, 404);

        app(CampusContext::class)->set($campus);
        URL::defaults(['campus' => $campus->slug]);
        view()->share('currentCampus', $campus);
        $request->route()->forgetParameter('campus');

        return $next($request);
    }
}

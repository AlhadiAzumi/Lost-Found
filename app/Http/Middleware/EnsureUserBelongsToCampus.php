<?php

namespace App\Http\Middleware;

use App\Support\CampusContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToCampus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        abort_unless(
            $user !== null && (int) $user->campus_id === app(CampusContext::class)->id(),
            403,
            'Akun ini terdaftar di portal kampus lain.'
        );

        return $next($request);
    }
}

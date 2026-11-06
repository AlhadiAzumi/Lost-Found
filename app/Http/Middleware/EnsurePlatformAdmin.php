<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminEmail = config('platform.admin_email');
        $user = $request->user();

        abort_unless(
            filled($adminEmail)
            && $user !== null
            && $user->role === 'admin'
            && strcasecmp($user->email, $adminEmail) === 0,
            403,
            'Akses hanya untuk admin platform.'
        );

        return $next($request);
    }
}

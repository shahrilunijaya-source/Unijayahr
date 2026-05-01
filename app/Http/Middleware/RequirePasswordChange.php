<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            $profileUrl = '/app/my-profile';

            if ($request->path() !== ltrim($profileUrl, '/') && ! $request->is('app/logout')) {
                return redirect($profileUrl)->with('warning', 'You must change your password before continuing.');
            }
        }

        return $next($request);
    }
}

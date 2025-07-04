<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrCreator
{
    /**
     * Handle an incoming request ensuring the user is an admin or a creator.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && in_array($user->role, ['admin', 'creator'])) {
            return $next($request);
        }

        abort(403, 'Admins or creators only.');
    }
}

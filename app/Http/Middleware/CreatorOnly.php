<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatorOnly
{
    /**
     * Handle an incoming request ensuring the user is a creator.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->role === 'creator') {
            return $next($request);
        }

        abort(403, 'Creators only.');
    }
}

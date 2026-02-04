<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateJwt
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($header, 7);
        $payload = app(JwtService::class)->decode($token);

        if (!$payload || !isset($payload['sub'])) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($payload['sub']);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}

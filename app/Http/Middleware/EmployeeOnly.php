<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeOnly
{
    /**
     * Handle an incoming request ensuring the user is an employee.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->role === 'employee') {
            return $next($request);
        }

        abort(403, 'Employees only.');
    }
}

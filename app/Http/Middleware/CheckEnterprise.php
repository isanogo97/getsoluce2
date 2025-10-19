<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEnterprise
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Le créateur n'a pas besoin d'appartenir à une entreprise
        if ($user->role === 'creator') {
            return $next($request);
        }

        // Les autres doivent appartenir à une entreprise
        if (!$user->enterprise_id) {
            return redirect()->route('home')
                ->with('error', 'Vous devez être associé à une entreprise pour accéder à cette page.');
        }

        // Vérifier que l'entreprise est active
        if ($user->enterprise && !$user->enterprise->is_active) {
            return redirect()->route('home')
                ->with('error', 'Votre entreprise est actuellement inactive. Contactez votre administrateur.');
        }

        return $next($request);
    }
}

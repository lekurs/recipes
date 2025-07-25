<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginAsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Seulement en mode développement/local
        if (!app()->environment(['local', 'staging'])) {
            abort(404);
        }

        // Pour l'instant, on accepte tous les utilisateurs connectés
        // Tu pourras réactiver la vérification du rôle plus tard si besoin
        if (!Auth::check()) {
            abort(403, 'Unauthorized - Not authenticated');
        }

        return $next($request);
    }
}

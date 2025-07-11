<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica si el usuario está autenticado y tiene el rol de administrador
        if (Auth::check() && Auth::user()->role_id === 2) {
            return $next($request);
        }

        if (!Auth::check() || Auth::user()->role_id !== 2) {
            return redirect('/')->with('error', 'No tienes acceso a esta página.');
        }
    }
}
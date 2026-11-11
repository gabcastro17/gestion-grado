<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $rol
    ): Response {

        $usuario = $request->user();

        if (!$usuario || !$usuario->activo || $usuario->role !== $rol) {
            abort(403, 'No tienes permiso para acceder.');
        }

        return $next($request);
    }
}
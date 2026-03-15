<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdminOrOrganizer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            abort(403, 'Необходима авторизация.');
        }

        if (! $request->user()->isAdmin() && ! $request->user()->isOrganizer()) {
            abort(403, 'Доступ только для администраторов и организаторов.');
        }

        return $next($request);
    }
}

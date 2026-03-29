<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClient
{
    /**
     * Handle an incoming request.
     * Only allows client users to access the route.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // If superadmin tries to access a client route, redirect them to their superadmin dashboard
        if ($request->user()->isSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        if ($request->user()->isClient()) {
            return $next($request);
        }

        abort(403, 'Nemate pristup ovoj stranici.');
    }
}

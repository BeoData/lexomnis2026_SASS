<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     * Only allows superadmin users to access the route.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isSuperAdmin()) {
            // If client, redirect to client dashboard
            if ($request->user() && $request->user()->isClient()) {
                return redirect()->route('client.dashboard');
            }

            abort(403, 'Nemate pristup ovoj stranici.');
        }

        return $next($request);
    }
}

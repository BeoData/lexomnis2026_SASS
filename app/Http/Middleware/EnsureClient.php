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

        // Superadmins can also view client pages (for support purposes)
        // but clients cannot access admin pages
        if ($request->user()->isClient() || $request->user()->isSuperAdmin()) {
            return $next($request);
        }

        abort(403, 'Nemate pristup ovoj stranici.');
    }
}

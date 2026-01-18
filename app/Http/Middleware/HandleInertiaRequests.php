<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Get the authenticated user directly from the guard
        // This ensures we always get the current user, not a cached one
        $user = auth()->user();
        
        $shared = [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ] : null,
            ],
        ];
        
        // Ensure auth is always set, even if null
        if (!isset($shared['auth'])) {
            $shared['auth'] = ['user' => null];
        }
        
        return $shared;
    }

    /**
     * Set the root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     */
    public function rootView(Request $request): string
    {
        return $this->rootView;
    }

    /**
     * Handle Inertia requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, \Closure $next)
    {
        $response = parent::handle($request, $next);
        
        // Add CSRF token to response headers for auto-refresh
        if ($request->hasSession()) {
            $response->headers->set('X-CSRF-TOKEN', $request->session()->token());
        }
        
        return $response;
    }
}

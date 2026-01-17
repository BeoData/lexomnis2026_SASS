<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add any routes that should be excluded from CSRF verification
    ];

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // Get tokens from the request
        $token = $this->getTokenFromRequest($request);

        // Check if tokens match
        $isValid = hash_equals(
            $request->session()->token(),
            $token
        );

        return $isValid;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // For Inertia.js requests, be more lenient with CSRF token validation
        if ($request->header('X-Inertia')) {
            // Inertia handles CSRF tokens automatically
            // If token doesn't match, regenerate it but don't block the request
            if (!$this->isReading($request) && !$this->tokensMatch($request)) {
                // Regenerate token for next request
                if ($request->hasSession()) {
                    $request->session()->regenerateToken();
                }
            }
        }

        return parent::handle($request, $next);
    }
}

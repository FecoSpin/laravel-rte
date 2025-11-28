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
        // Add your API routes or webhook endpoints that should be excluded from CSRF protection
        // Example: 'api/*', 'webhook/*'
    ];

    /**
     * Determine if the request should be considered a "read" operation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isReading($request)
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // If the request is an API request, we'll check the token in the header
        if ($request->is('api/*')) {
            return $this->tokensMatchForApi($request);
        }

        return parent::tokensMatch($request);
    }

    /**
     * Determine if the session and input CSRF tokens match for API requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatchForApi($request)
    {
        $token = $request->header('X-CSRF-TOKEN') ?: $request->input('_token');
        
        if (!$token) {
            return false;
        }

        return hash_equals(
            (string) $request->session()->token(),
            (string) $token
        );
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces the Accept header to application/json on API requests.
 *
 * Without this, a client that forgets the Accept header causes Laravel's
 * Authenticate middleware to try route('login') on 401, which throws a
 * RouteNotFoundException (since this is a pure API with no login route).
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}

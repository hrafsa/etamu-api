<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * For API routes we return null so a 401 JSON is generated instead of redirecting
     * to the login page.
     */
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson() || str_starts_with($request->path(), 'api/')) {
            return null; // Trigger 401 JSON via exception handler
        }
        return route('login');
    }
}


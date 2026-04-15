<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        // Paksa header Accept JSON agar konsisten
        $request->headers->set('Accept', 'application/json');

        // Ambil user via guard sanctum (Bearer token). Tidak gunakan session redirect.
        $user = auth('sanctum')->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Block admin accounts from public API as per requirement
        if ($user->role !== 'user') {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden: API is for user accounts only.',
            ], 403);
        }

        // Set user pada request (optional, biasanya sudah)
        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $isApi = str_starts_with($request->path(), 'api/');
        if ($isApi) {
            // Force JSON expectations
            $request->headers->set('Accept', 'application/json');
            // Jaga-jaga beberapa library pakai header ini
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        }

        $response = $next($request);

        if (! $isApi) {
            return $response; // hanya modifikasi untuk API
        }

        // Jika redirect (302/301) arahkan ke JSON 401
        if ($response instanceof RedirectResponse) {
            $target = $response->getTargetUrl();
            if (str_contains($target, '/login')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
        }

        // Jika content-type HTML (login page) dan status 200 -> convert
        $contentType = $response->headers->get('Content-Type');
        if ($contentType && str_contains($contentType, 'text/html')) {
            $content = $response->getContent();
            // heuristik sederhana deteksi form login laravel
            if (str_contains($content, '<form') && (str_contains($content, 'password') || str_contains(strtolower($content), 'login'))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
        }

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiNormalizeUnauthorized
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
        } catch (AuthenticationException $e) {
            if ($this->isApi($request)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
            throw $e; // non-api fallback
        }

        // Convert redirect-to-login (302) into JSON for API calls
        if ($this->isApi($request) && $this->isLoginRedirect($response)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($this->isApi($request) && $response->getStatusCode() === 401) {
            if ($response instanceof JsonResponse) {
                $data = $response->getData(true);
                if (! array_key_exists('status', $data)) {
                    $data = ['status' => false] + $data;
                    $response->setData($data);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
        }

        return $response;
    }

    private function isApi(Request $request): bool
    {
        return str_starts_with($request->path(), 'api/') || $request->expectsJson();
    }

    private function isLoginRedirect($response): bool
    {
        if (!method_exists($response, 'isRedirection') || ! $response->isRedirection()) {
            return false;
        }
        // target url could be full URL or path
        $target = method_exists($response, 'getTargetUrl') ? $response->getTargetUrl() : '';
        if (! $target) return false;
        // Normalize to only path
        return str_contains($target, '/login');
    }
}

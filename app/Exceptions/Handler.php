<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (ValidationException $e, $request) {
            if ($this->isApi($request)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Ensure AuthenticationException always returns JSON with status key for API
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($this->isApi($request)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->isApi($request)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        return redirect()->guest(route('login'));
    }

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);
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

        // Fallback: jika API tapi hasil HTML (misal form login) ubah ke JSON 401
        if ($this->isApi($request)) {
            $contentType = $response->headers->get('Content-Type');
            if ($contentType && str_contains($contentType, 'text/html') && $response->getStatusCode() === 200) {
                $content = $response->getContent();
                if (str_contains($content, '<form') && (str_contains($content, 'password') || str_contains(strtolower($content), 'login'))) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthenticated',
                    ], 401);
                }
            }
        }
        return $response;
    }

    private function isApi($request): bool
    {
        // Treat any request hitting /api/* or expecting JSON as API
        return $request->expectsJson() || str_starts_with($request->path(), 'api/');
    }
}

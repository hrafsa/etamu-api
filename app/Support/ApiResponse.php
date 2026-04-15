<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(?string $message = null, $data = null, int $status = 200, array $extra = []): JsonResponse
    {
        $payload = ['status' => true];
        if ($message !== null) {
            $payload['message'] = $message;
        }
        if ($data !== null) {
            $payload['data'] = $data;
        }
        if ($extra) {
            $payload = array_merge($payload, $extra);
        }
        return response()->json($payload, $status);
    }

    protected function error(string $message, int $status = 400, $errors = null, array $extra = []): JsonResponse
    {
        $payload = ['status' => false, 'message' => $message];
        if ($errors !== null) {
            $payload['errors'] = $errors;
        }
        if ($extra) {
            $payload = array_merge($payload, $extra);
        }
        return response()->json($payload, $status);
    }
}


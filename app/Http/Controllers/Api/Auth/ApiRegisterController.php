<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Auth\ApiRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class ApiRegisterController extends ApiController
{
    public function register(ApiRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['email'] = strtolower($data['email']);
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'user';
        $data['status'] = true; // default active

        $user = User::create($data);

        // Bersihkan token lama (harusnya tidak ada) dan buat baru
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success('User registered successfully', [
            'user' => new UserResource($user),
            'token'=> $token,
        ], 201);
    }
}

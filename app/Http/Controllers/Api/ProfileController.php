<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\ProfileUpdateRequest;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends ApiController
{
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->fill($data);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        return $this->success('Profile updated successfully', [
            'user' => new UserResource($user),
        ]);
    }

    public function updatePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $current = $request->input('current_password');

        if (! Hash::check($current, $user->password)) {
            return $this->error('Current password is incorrect.', 422, [
                'current_password' => ['Current password is incorrect.']
            ]);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return $this->success('Password updated successfully');
    }
}


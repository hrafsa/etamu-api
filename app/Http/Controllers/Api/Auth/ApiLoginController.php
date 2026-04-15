<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Auth\ApiLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ApiLoginController extends ApiController
{
    private const ATTEMPT_LIMIT = 5;      // max attempts per decay window
    private const DECAY_SECONDS = 60;     // window length

    public function login(ApiLoginRequest $request): JsonResponse
    {
        $email = Str::lower($request->input('email'));
        $ip    = $request->ip();

        $userKey = $this->userThrottleKey($email, $ip);
        $ipKey   = $this->ipThrottleKey($ip);

        if ($this->tooManyAttempts($userKey) || $this->tooManyAttempts($ipKey)) {
            $seconds = RateLimiter::availableIn($this->tooManyAttempts($userKey) ? $userKey : $ipKey);
            return $this->error('Too many login attempts. Try again in '.$seconds.' seconds.', 429);
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $this->hit($userKey);
            $this->hit($ipKey);
            usleep(random_int(100_000, 300_000)); // basic jitter anti brute force
            return $this->error('These credentials do not match our records.', 401);
        }

        if (! $user->status) {
            $this->hit($userKey); // tetap hit agar enumerasi akun inactive melambat
            return $this->error('Account is inactive. Please contact support.', 403);
        }

        if ($user->role === 'admin') { // batasi admin pakai API publik
            return $this->error('Access denied for this account type.', 403);
        }

        $this->clear($userKey);
        $this->clear($ipKey);

        // Rotate tokens untuk keamanan (hindari penumpukan token lama)
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success('Login successful', [
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $user->tokens()->delete();
        }
        return $this->success('Logged out successfully');
    }

    private function tooManyAttempts(string $key): bool
    {
        return RateLimiter::tooManyAttempts($key, self::ATTEMPT_LIMIT);
    }

    private function hit(string $key): void
    {
        RateLimiter::hit($key, self::DECAY_SECONDS);
    }

    private function clear(string $key): void
    {
        RateLimiter::clear($key);
    }

    private function userThrottleKey(string $email, string $ip): string
    {
        return 'login:user:'.sha1($email.'|'.$ip);
    }

    private function ipThrottleKey(string $ip): string
    {
        return 'login:ip:'.sha1($ip);
    }
}

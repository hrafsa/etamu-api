<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

function registerPayload(array $overrides = []): array {
    return array_merge([
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'phone' => '+10000000000',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ], $overrides);
}

it('registers a user successfully', function () {
    $response = $this->postJson('/api/register', registerPayload());

    $response->assertCreated()
        ->assertJsonStructure([
            'status', 'message', 'user' => ['id','name','email','phone','role','status'], 'token'
        ])
        ->assertJson([
            'status' => true,
            'message' => 'User registered successfully'
        ]);

    expect(User::whereEmail('testuser@example.com')->exists())->toBeTrue();
});

it('rejects duplicate email on register', function () {
    User::factory()->create(['email' => 'dup@example.com', 'phone' => '+19999999999']);

    $response = $this->postJson('/api/register', registerPayload([
        'email' => 'dup@example.com',
        'phone' => '+18888888888'
    ]));

    $response->assertStatus(422)
        ->assertJsonPath('errors.email.0', 'Email is already registered.');
});

it('returns only required error when password missing (bail rule)', function () {
    $resp = $this->postJson('/api/register', registerPayload([
        'password' => '',
        'password_confirmation' => ''
    ]));

    $resp->assertStatus(422)
        ->assertJsonPath('errors.password.0', 'Password is required.')
        ->assertJsonMissingPath('errors.password.1');
});

it('returns only min length error when password too short (ordering)', function () {
    $resp = $this->postJson('/api/register', registerPayload([
        'password' => 'short',
        'password_confirmation' => 'shortdiff'
    ]));

    $resp->assertStatus(422)
        ->assertJsonPath('errors.password.0', 'Password must be at least 8 characters.')
        ->assertJsonMissingPath('errors.password.1'); // confirmed not evaluated due to bail
});

it('returns confirmation error when length ok but confirmation mismatched', function () {
    $resp = $this->postJson('/api/register', registerPayload([
        'password' => 'Password123',
        'password_confirmation' => 'Password124'
    ]));

    $resp->assertStatus(422)
        ->assertJsonPath('errors.password.0', 'Password confirmation does not match.');
});

it('logs in successfully and returns token', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('Password123!')
    ]);

    $resp = $this->postJson('/api/login', [
        'email' => 'login@example.com',
        'password' => 'Password123!'
    ]);

    $resp->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonStructure(['token','user' => ['id','email']]);
});

it('prevents login for inactive user', function () {
    $user = User::factory()->inactive()->create([
        'email' => 'inactive@example.com',
        'password' => Hash::make('Password123!')
    ]);

    $resp = $this->postJson('/api/login', [
        'email' => 'inactive@example.com',
        'password' => 'Password123!'
    ]);

    $resp->assertStatus(403)
        ->assertJsonPath('message', 'Account is inactive. Please contact support.');
});

it('rate limits after multiple failed login attempts', function () {
    // ensure limiter starts clean
    RateLimiter::clear('login:user:'.sha1('ratelimit@example.com|127.0.0.1'));
    RateLimiter::clear('login:ip:'.sha1('127.0.0.1'));

    for ($i=0; $i<5; $i++) {
        $this->postJson('/api/login', [
            'email' => 'ratelimit@example.com',
            'password' => 'WrongPassword'
        ])->assertStatus(401);
    }

    // sixth should be 429
    $this->postJson('/api/login', [
        'email' => 'ratelimit@example.com',
        'password' => 'WrongPassword'
    ])->assertStatus(429);
});

it('returns json 404 for unknown api endpoint', function () {
    $this->getJson('/api/unknown-endpoint-xyz')
        ->assertStatus(404)
        ->assertJsonPath('message', 'Endpoint not found.');
});

it('admin cannot login via api', function () {
    $admin = User::factory()->admin()->create([
        'email' => 'adminapi@example.com',
        'password' => Hash::make('Password123!')
    ]);

    $resp = $this->postJson('/api/login', [
        'email' => 'adminapi@example.com',
        'password' => 'Password123!'
    ]);

    $resp->assertStatus(403)
        ->assertJsonPath('message', 'Access denied for this account type.');
});

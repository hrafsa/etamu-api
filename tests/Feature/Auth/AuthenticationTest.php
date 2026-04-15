<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('admin can authenticate using the login screen', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($admin);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('non-admin user cannot authenticate using the login screen', function () {
    $user = User::factory()->create(); // role user

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['email']);
});

test('users can not authenticate with invalid password (admin case)', function () {
    $admin = User::factory()->admin()->create();

    $this->post('/login', [
        'email' => $admin->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('admin can logout', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

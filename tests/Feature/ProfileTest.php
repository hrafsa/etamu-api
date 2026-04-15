<?php

use App\Models\User;

function makeAdmin() { return User::factory()->admin()->create(); }

test('profile page is displayed', function () {
    $admin = makeAdmin();
    $response = $this->actingAs($admin)->get('/profile');
    $response->assertOk();
});

test('profile information can be updated', function () {
    $admin = makeAdmin();
    $response = $this->actingAs($admin)->patch('/profile', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    $response->assertSessionHasNoErrors()->assertRedirect('/profile');
    $admin->refresh();
    $this->assertSame('Test User', $admin->name);
    $this->assertSame('test@example.com', $admin->email);
    $this->assertNull($admin->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $admin = makeAdmin();
    $response = $this->actingAs($admin)->patch('/profile', [
        'name' => 'Test User',
        'email' => $admin->email,
    ]);
    $response->assertSessionHasNoErrors()->assertRedirect('/profile');
    $this->assertNotNull($admin->refresh()->email_verified_at);
});

test('user can delete their account (admin)', function () {
    $admin = makeAdmin();
    $response = $this->actingAs($admin)->delete('/profile', [ 'password' => 'password' ]);
    $response->assertSessionHasNoErrors()->assertRedirect('/');
    $this->assertGuest();
    $this->assertNull($admin->fresh());
});

test('correct password must be provided to delete account (admin)', function () {
    $admin = makeAdmin();
    $response = $this->actingAs($admin)->from('/profile')->delete('/profile', [ 'password' => 'wrong-password' ]);
    $response->assertSessionHasErrorsIn('userDeletion', 'password')->assertRedirect('/profile');
    $this->assertNotNull($admin->fresh());
});

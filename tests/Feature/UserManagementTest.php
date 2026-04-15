<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\delete;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create an admin and a regular user for tests
    $this->admin = User::factory()->admin()->create([
        'email' => 'admin@example.test',
        'phone' => '+10000000001',
        'status' => true,
    ]);

    $this->user = User::factory()->create([
        'email' => 'user1@example.test',
        'phone' => '+10000000002',
        'status' => true,
    ]);
});

it('denies non-admin from accessing user management index', function () {
    actingAs($this->user);
    get(route('user-management.index'))
        ->assertForbidden();
});

it('allows admin to view user management index', function () {
    actingAs($this->admin);
    get(route('user-management.index'))
        ->assertOk()
        ->assertSee('User Management');
});

it('admin can update a user without changing password when left blank', function () {
    actingAs($this->admin);
    $originalHash = $this->user->password;

    patch(route('user-management.update', $this->user), [
        'name' => 'Updated Name',
        'email' => 'user1@example.test', // unchanged
        'phone' => '+10000000002', // unchanged
        'password' => '', // blank -> no change
    ])->assertRedirect(route('user-management.index'));

    $this->user->refresh();
    expect($this->user->name)->toBe('Updated Name');
    expect($this->user->password)->toBe($originalHash); // password unchanged
});

it('admin can update a user with new password when provided', function () {
    actingAs($this->admin);

    patch(route('user-management.update', $this->user), [
        'name' => 'Another Name',
        'email' => 'user1@example.test',
        'phone' => '+10000000002',
        'password' => 'newsecurepass',
    ])->assertRedirect(route('user-management.index'));

    $this->user->refresh();
    expect(Hash::check('newsecurepass', $this->user->password))->toBeTrue();
});

it('admin can delete a user', function () {
    actingAs($this->admin);

    delete(route('user-management.destroy', $this->user))
        ->assertRedirect(route('user-management.index'));

    expect(User::where('id', $this->user->id)->exists())->toBeFalse();
});

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Support\ActivityLogger; // added

class UserManagementController extends Controller
{
    // List users with pagination + search + filters
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 5);
        $perPage = $perPage > 0 && $perPage <= 50 ? $perPage : 5;

        $q = trim((string) $request->get('q')) ?: null;
        $role = $request->get('role');
        $status = $request->get('status'); // 'active' | 'inactive'

        $query = User::query()->latest('id');

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%");
            });
        }
        if (in_array($role, ['admin', 'user'], true)) {
            $query->where('role', $role);
        }
        if ($status === 'active') {
            $query->where('status', true);
        } elseif ($status === 'inactive') {
            $query->where('status', false);
        }

        $users = $query->paginate($perPage)->withQueryString();

        return view('user-management.index', [
            'users' => $users,
            'filters' => [
                'q' => $q,
                'role' => $role,
                'status' => $status,
                'per_page' => $perPage,
            ],
        ]);
    }

    // Show edit form
    public function edit(User $user)
    {
        return view('user-management.edit', compact('user'));
    }

    // Update user (optional password + status)
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            // NOTE: DB migration makes phone required & unique; requirement says nullable. We'll treat as sometimes and keep existing if blank.
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'status' => ['nullable','in:active,inactive'],
        ]);

        $original = $user->only(['name','email','phone','status']);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (array_key_exists('phone', $validated) && $validated['phone'] !== null && $validated['phone'] !== '') {
            $user->phone = $validated['phone'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (array_key_exists('status', $validated) && $validated['status'] !== null) {
            $user->status = $validated['status'] === 'active';
        }

        $user->save();

        $changes = [];
        foreach (['name','email','phone','status'] as $field) {
            $old = $original[$field];
            $new = $user->{$field};
            if ($old !== $new) {
                if ($field === 'status') {
                    $changes[$field] = ['from' => $old ? 'active' : 'inactive', 'to' => $new ? 'active' : 'inactive'];
                } else {
                    $changes[$field] = ['from' => $old, 'to' => $new];
                }
            }
        }
        if (!empty($validated['password'])) {
            $changes['password'] = ['changed' => true];
        }
        if ($changes) {
            ActivityLogger::log('user.updated', $user, 'User diperbarui', ['changes' => $changes]);
        }

        return redirect()
            ->route('user-management.index')
            ->with('status', 'User updated successfully.');
    }

    // Delete user
    public function destroy(User $user)
    {
        $snapshot = $user->only(['id','name','email']);
        $user->delete();
        ActivityLogger::log('user.deleted', null, 'User dihapus', $snapshot);

        return redirect()
            ->route('user-management.index')
            ->with('status', 'User deleted successfully.');
    }
}

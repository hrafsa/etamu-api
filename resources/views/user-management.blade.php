@php
    // Helpers
    function statusBadgeClasses($status) {
      return match ($status) {
        'Active'    => 'border-emerald-500/25 bg-emerald-500/10 text-emerald-300',
        'Inactive' => 'border-rose-500/25 bg-rose-500/10 text-rose-300',
        default     => 'border-neutral-700 bg-neutral-800 text-neutral-300',
      };
    }
    function initials_of($name) {
      $parts = array_values(array_filter(explode(' ', trim($name))));
      $parts = array_slice($parts, 0, 2);
      return strtoupper(implode('', array_map(fn($p) => mb_substr($p, 0, 1), $parts))) ?: 'NA';
    }

@endphp

<x-app-layout>
    <!-- Page root: removed top-level Alpine x-data -->
    <x-slot name="header">
    </x-slot>
    <div class="min-h-full bg-gray-900 text-neutral-100">
        <!-- Page header -->
        <div class="px-4 sm:px-6 lg:px-8 pt-6 ">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between ">
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold tracking-tight">User Management</h1>
                    <p class="text-sm text-gray-400">Manage users, roles, and account status</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-4 sm:px-6 lg:px-8 pt-4">
            <div class="flex flex-wrap items-center justify-between gap-2">

                <div class="flex flex-wrap gap-2">
                    <!-- Filter: User Role -->
                    <div class="relative" x-data="{ open:false }" @keydown.escape.window="open=false"
                         @click.outside="open=false">
                        <button @click="open=!open" type="button"
                                class="inline-flex items-center gap-2 rounded-md bg-neutral-900/70 border border-gray-700 px-3 py-2 text-sm text-neutral-200 hover:bg-neutral-900 focus:outline-none">
                            <span class="text-neutral-400">User Role:</span>
                            <span class="font-medium">Admin</span>
                            <svg class="h-4 w-4 text-neutral-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                      clip-rule="evenodd"/>
                        </button>
                        <div x-show="open" x-transition.origin.top.left
                             class="absolute z-20 mt-1 w-48 rounded-md border border-gray-700 bg-neutral-900 p-1 shadow-xl">
                            <button
                                class="w-full text-left px-3 py-2 text-sm rounded hover:bg-neutral-800">Admin</button>

                        </div>
                    </div>

                    <!-- Filter: Status -->
                    <div class="relative" x-data="{ open:false }" @keydown.escape.window="open=false"
                         @click.outside="open=false">
                        <button @click="open=!open" type="button"
                                class="inline-flex items-center gap-2 rounded-md bg-neutral-900/70 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 hover:bg-neutral-900 focus:outline-none">
                            <span class="text-neutral-400">Status:</span>
                            <span class="font-medium">Active</span>
                            <svg class="h-4 w-4 text-neutral-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                      clip-rule="evenodd"/>
                        </button>
                        <div x-show="open" x-transition.origin.top.left
                            </svg>
                             class="absolute z-20 mt-1 w-48 rounded-md border border-neutral-800 bg-neutral-900 p-1 shadow-xl">
                            <button
                                class="w-full text-left px-3 py-2 text-sm rounded hover:bg-neutral-800">Active</button>

                        </div>
                    </div>

                </div>

                <!-- Add new user button -->
                <button type="button"
                        class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-3.5 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/70">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Add new user
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="overflow-x-auto rounded-lg border border-gray-800 bg-gray-900">
                <table class="min-w-[800px] w-full text-sm">
                    <thead class="bg-gray-700/50">
                    <tr class="text-left text-neutral-300">
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox"
                                   class="h-4 w-4 rounded border-neutral-700 bg-neutral-900 text-indigo-600 focus:ring-indigo-500"/>
                        </th>
                        <th class="px-3 py-3 font-medium">User</th>
                        <th class="px-3 py-3 font-medium">User Role</th>
                        <th class="px-3 py-3 font-medium">Email</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="w-12 px-3 py-3"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                    @forelse ($users as $user)
                        @php
                            $statusLabel = $user->status ? 'Active' : 'Inactive';
                        @endphp
                        <tr class="hover:bg-neutral-800/50">
                            <!-- Checkbox -->
                            <td class="px-4 py-3 align-middle">
                                <input type="checkbox"
                                       class="h-4 w-4 rounded border-neutral-700 bg-neutral-900 text-indigo-600 focus:ring-indigo-500"/>
                            </td>
                            <!-- User: avatar + name -->
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 shrink-0 rounded-full bg-neutral-800 ring-1 ring-neutral-700 flex items-center justify-center text-xs font-semibold text-neutral-300">{{ initials_of($user->name) }}</div>
                                    <div class="min-w-0">
                                        <div class="truncate font-medium">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <!-- Role -->
                            <td class="px-3 py-3 text-neutral-200">{{ ucfirst($user->role) }}</td>
                            <!-- Email -->
                            <td class="px-3 py-3 text-neutral-300">
                                <a href="mailto:{{ $user->email }}" class="hover:underline">{{ $user->email }}</a>
                            </td>
                            <!-- Status -->
                            <td class="px-3 py-3">
                                <span
                                    class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium {{ statusBadgeClasses($statusLabel) }}">{{ $statusLabel }}</span>
                            </td>
                            <!-- Actions -->
                            <td class="px-3 py-3">
                                <div class="relative" x-data="{ open:false }" @keydown.escape.window="open=false"
                                     @click.outside="open=false">
                                    <button @click="open=!open"
                                            class="inline-flex items-center rounded-md p-1.5 hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-neutral-700">
                                        <svg class="h-5 w-5 text-neutral-300" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="5" cy="12" r="1.5"/>
                                            <circle cx="12" cy="12" r="1.5"/>
                                            <circle cx="19" cy="12" r="1.5"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition.origin.top.right
                                         class="absolute right-0 z-30 mt-2 w-36 rounded-md border border-neutral-800 bg-neutral-900 p-1 shadow-xl">
                                        <button class="w-full text-left px-3 py-2 text-sm rounded hover:bg-neutral-800">
                                            Edit
                                        </button>
                                        <button class="w-full text-left px-3 py-2 text-sm rounded hover:bg-neutral-800">
                                            Preview
                                        </button>
                                        <button
                                            class="w-full text-left px-3 py-2 text-sm rounded hover:bg-red-500/10 text-red-300">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-neutral-400">
                                No users match the current filters.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

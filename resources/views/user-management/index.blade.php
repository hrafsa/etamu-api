<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ __('User Management') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-600/10 border border-emerald-600/30 text-emerald-300 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-gray-900 shadow-sm border border-gray-800 rounded-lg overflow-hidden">
                <div class="px-4 pt-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-100">Users</h3>
                            <p class="text-sm text-gray-400">All registered users</p>
                        </div>
                        <form method="GET" class="w-full md:w-auto flex flex-col sm:flex-row gap-3 sm:items-end">
                            <div class="flex-1">
                                <label class="block text-xs uppercase tracking-wide text-gray-400 mb-1" for="q">Search</label>
                                <input id="q" name="q" value="{{ request('q') }}" placeholder="Name, email, phone" class="w-full bg-gray-800 border border-gray-700 rounded text-sm text-gray-200 placeholder-gray-500" />
                            </div>
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-gray-400 mb-1" for="role">Role</label>
                                <select id="role" name="role" class="bg-gray-800 border-gray-700 rounded text-sm text-gray-200">
                                    <option value="">All</option>
                                    <option value="admin" @selected(request('role')==='admin')>Admin</option>
                                    <option value="user" @selected(request('role')==='user')>User</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-gray-400 mb-1" for="status">Status</label>
                                <select id="status" name="status" class="bg-gray-800 border-gray-700 rounded text-sm text-gray-200">
                                    <option value="">All</option>
                                    <option value="active" @selected(request('status')==='active')>Active</option>
                                    <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs uppercase tracking-wide text-gray-400 mb-1" for="per_page">Per Page</label>
                                <select id="per_page" name="per_page" class="bg-gray-800 border-gray-700 rounded text-sm text-gray-200">
                                    @foreach([5,10,25,50] as $size)
                                        <option value="{{ $size }}" @selected(request('per_page',5)==$size)>{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex gap-2 sm:self-end">
                                <button class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded cursor-pointer">Filter</button>
                                @if(request()->query())
                                    <a href="{{ route('user-management.index') }}" class="inline-flex items-center gap-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium px-4 py-2 rounded">Reset</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800 text-sm">
                        <thead class="bg-gray-800/50 text-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Email</th>
                            <th class="px-4 py-3 text-left font-medium">Phone</th>
                            <th class="px-4 py-3 text-left font-medium">Role</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                        @forelse($users as $user)
                            <tr x-data="{ openDelete:false }" class="hover:bg-gray-800/40">
                                <td class="px-4 py-3 font-medium text-gray-100">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-gray-300">
                                    <a class="hover:underline" href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-300">
                                    <a class="hover:underline" href="https://wa.me/{{ $user->phone }}" target="_blank">{{ $user->phone }}</a>
                                </td>
                                <td class="px-4 py-3 capitalize text-gray-300">{{ $user->role }}</td>
                                <td class="px-4 py-3">
                                    @php $statusLabel = $user->status ? 'Active' : 'Inactive'; @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border {{ $user->status ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/25' : 'bg-rose-500/10 text-rose-300 border-rose-500/25' }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('user-management.edit', $user) }}" class="inline-flex items-center gap-1 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium px-2.5 py-1.5">
                                            Edit
                                        </a>
                                        <button type="button" @click="openDelete=true" class="inline-flex items-center gap-1 rounded bg-red-600/80 hover:bg-red-600 text-white text-xs font-medium px-2.5 py-1.5 cursor-pointer">
                                            Delete
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div x-show="openDelete" x-cloak class="fixed inset-0 z-40 flex items-center justify-center" aria-modal="true" role="dialog">
                                        <div @click="openDelete=false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

                                        <div x-transition.opacity.scale.90 class="relative z-50 w-full max-w-sm mx-auto bg-gray-900 border border-gray-700 rounded-lg shadow-lg p-6">
                                            <h4 class="text-lg font-semibold text-gray-100 mb-2">Delete User</h4>
                                            <p class="text-sm text-gray-400 mb-4">Are you sure you want to delete <span class="font-medium text-gray-200">{{ $user->name }}</span>? This action cannot be undone.</p>
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" @click="openDelete=false" class="px-3.5 py-2 text-sm rounded border border-gray-600 text-gray-200 hover:bg-gray-800 cursor-pointer">Cancel</button>
                                                <form method="POST" action="{{ route('user-management.destroy', $user) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3.5 py-2 text-sm rounded bg-red-600 hover:bg-red-500 text-white font-medium cursor-pointer">Confirm</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Delete Modal -->
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">No users found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-4 border-t border-gray-800 bg-gray-900/70">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


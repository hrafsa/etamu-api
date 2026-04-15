<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ __('Edit User') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-gray-900 shadow-sm border border-gray-800 rounded-lg p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-100">Update User</h3>
                    <a href="{{ route('user-management.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">&larr; Back</a>
                </div>

                <form method="POST" action="{{ route('user-management.update', $user) }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                               class="mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                               class="mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-300">Phone <span class="text-gray-500 font-normal">(optional)</span></label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                               class="mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('phone')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-300">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" @selected(old('status', $user->status ? 'active' : 'inactive')==='active')>Active</option>
                            <option value="inactive" @selected(old('status', $user->status ? 'active' : 'inactive')==='inactive')>Inactive</option>
                        </select>
                        @error('status')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">New Password <span class="text-gray-500 font-normal">(leave blank to keep current)</span></label>
                        <input id="password" name="password" type="password"
                               class="mt-1 block w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('user-management.index') }}" class="px-4 py-2 text-sm rounded border border-gray-600 text-gray-200 hover:bg-gray-800">Cancel</a>
                        <button type="submit" class="px-4 py-2 text-sm rounded bg-indigo-600 hover:bg-indigo-500 text-white font-medium cursor-pointer">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

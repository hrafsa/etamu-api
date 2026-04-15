<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ __('Categories & Sub Categories') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-emerald-600/10 border border-emerald-600/30 text-emerald-300 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-rose-600/10 border border-rose-600/40 text-rose-300 px-4 py-3 rounded space-y-1 text-sm">
                    @foreach($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <div class="bg-gray-900 border border-gray-800 shadow-sm rounded-lg p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-end gap-4">
                    <form method="POST" action="{{ route('admin.categories.store') }}" class="flex-1 grid gap-3 md:grid-cols-4">
                        @csrf
                        <div class="md:col-span-3">
                            <label class="block text-sm text-gray-300 mb-1">Nama Kategori</label>
                            <input name="name" required class="w-full bg-gray-800 border-gray-700 rounded text-gray-200 text-sm" placeholder="e.g. AKD" />
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <button class="w-full inline-flex justify-center items-center gap-2 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 cursor-pointer">Tambah</button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-800">
                        <thead class="bg-gray-800/50 text-gray-300">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium">Action</th>
                            <th class="px-3 py-2 text-left font-medium">Kategori</th>
                            <th class="px-3 py-2 text-left font-medium w-1/2">Sub Kategori</th>
                            <th class="px-3 py-2 text-left font-medium">Tambah Sub</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                        @forelse($categories as $category)
                            <tr x-data="{ openCategoryDelete:false }">
                                <td class="px-3 py-3 align-top">
                                    <button type="button" @click="openCategoryDelete=true" class="inline-flex items-center gap-1 rounded bg-red-600/80 hover:bg-red-600 text-white text-xs font-medium px-2.5 py-1.5 cursor-pointer">Hapus</button>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <div class="font-medium text-gray-100 flex items-start justify-start gap-4">
                                        <span>{{ $category->name }}</span>
                                    </div>
                                    <p class="mt-1 text-[11px] uppercase tracking-wide text-gray-500">{{ $category->subCategories->count() }} Sub</p>
                                    <!-- Category Delete Modal -->
                                    <div x-show="openCategoryDelete" x-cloak class="fixed inset-0 z-40 flex items-center justify-center" role="dialog" aria-modal="true">
                                        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="openCategoryDelete=false"></div>
                                        <div x-transition class="relative z-50 w-full max-w-sm bg-gray-900 border border-gray-700 rounded-lg shadow-lg p-6">
                                            <h4 class="text-lg font-semibold text-gray-100 mb-2">Hapus Kategori</h4>
                                            <p class="text-sm text-gray-400 mb-4">Kategori <span class="font-medium text-gray-200">{{ $category->name }}</span> dan semua sub kategori terkait akan dihapus. Lanjutkan?</p>
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" @click="openCategoryDelete=false" class="px-3.5 py-2 text-sm rounded border border-gray-600 text-gray-200 hover:bg-gray-800 cursor-pointer">Batal</button>
                                                <form method="POST" action="{{ route('admin.categories.destroy',$category) }}" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="px-3.5 py-2 text-sm rounded bg-red-600 hover:bg-red-500 text-white font-medium cursor-pointer">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Category Delete Modal -->
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($category->subCategories as $sub)
                                            <div x-data="{ openSubDelete:false }" class="inline-block">
                                                <button type="button" @click="openSubDelete=true" class="cursor-pointer inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-800 border border-gray-700 text-gray-300 text-xs hover:bg-gray-700">
                                                    {{ $sub->name }}
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none"><path d="M6 18 18 6M6 6l12 12" class="stroke-current" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                </button>
                                                <!-- Sub Delete Modal -->
                                                <div x-show="openSubDelete" x-cloak class="fixed inset-0 z-40 flex items-center justify-center" role="dialog" aria-modal="true">
                                                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="openSubDelete=false"></div>
                                                    <div x-transition class="relative z-50 w-full max-w-sm bg-gray-900 border border-gray-700 rounded-lg shadow-lg p-6">
                                                        <h4 class="text-lg font-semibold text-gray-100 mb-2">Hapus Sub Kategori</h4>
                                                        <p class="text-sm text-gray-400 mb-4">Hapus sub kategori <span class="font-medium text-gray-200">{{ $sub->name }}</span>? Tindakan ini tidak bisa dibatalkan.</p>
                                                        <div class="flex items-center justify-end gap-2">
                                                            <button type="button" @click="openSubDelete=false" class="px-3.5 py-2 text-sm rounded border border-gray-600 text-gray-200 hover:bg-gray-800 cursor-pointer">Batal</button>
                                                            <form method="POST" action="{{ route('admin.categories.sub.destroy',[$category,$sub]) }}" class="inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="px-3.5 py-2 text-sm rounded bg-red-600 hover:bg-red-500 text-white font-medium cursor-pointer">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /Sub Delete Modal -->
                                            </div>
                                        @empty
                                            <span class="text-gray-500 text-xs">Tidak ada sub kategori</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <form method="POST" action="{{ route('admin.categories.sub.store',$category) }}" class="flex gap-2">
                                        @csrf
                                        <input name="name" required class="flex-1 bg-gray-800 border-gray-700 rounded text-gray-200 text-xs" placeholder="Sub kategori" />
                                        <button class="inline-flex items-center justify-center px-3 py-1.5 text-xs rounded bg-emerald-600 hover:bg-emerald-500 text-white font-medium cursor-pointer">Tambah</button>
                                    </form>
                                </td>
                                <td class="px-3 py-3"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-gray-400 text-sm">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

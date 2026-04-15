<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ __('Daftar Pengajuan') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 space-y-4">
                <form method="GET" class="grid gap-4 md:grid-cols-6">
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-gray-400 mb-1">Status</label>
                        <select name="status" class="w-full bg-gray-800 border-gray-700 rounded text-sm text-gray-200">
                            <option value="">Semua</option>
                            @foreach(['pending'=>'Pending','disetujui'=>'Disetujui','ditolak'=>'Ditolak'] as $k=>$v)
                                <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-gray-400 mb-1">Kategori</label>
                        <select name="kategori" class="w-full bg-gray-800 border-gray-700 rounded text-sm text-gray-200">
                            <option value="">Semua</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected((string)request('kategori')===(string)$c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs uppercase tracking-wide text-gray-400 mb-1">Search</label>
                        <input name="q" value="{{ request('q') }}" placeholder="Nomor, instansi, atas nama, email, phone" class="w-full bg-gray-800 border border-gray-700 rounded text-sm text-gray-200 placeholder-gray-500" />
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wide text-gray-400 mb-1">Per Page</label>
                        <select name="per_page" class="w-full bg-gray-800 border-gray-700 rounded text-sm text-gray-200">
                            @foreach(($perPageAllowed ?? [5,10,25,50]) as $size)
                                <option value="{{ $size }}" @selected((int)request('per_page', $perPage ?? 10) === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded cursor-pointer">Filter</button>
                        @if(request()->query())
                            <a href="{{ route('admin.pengajuan.index') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-500 text-white text-sm font-medium px-4 py-2 rounded">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-800">
                        <thead class="bg-gray-800/60 text-gray-300">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium">Nomor</th>
                            <th class="px-3 py-2 text-left font-medium">Instansi</th>
                            <th class="px-3 py-2 text-left font-medium">Atas Nama</th>
                            <th class="px-3 py-2 text-left font-medium">Tanggal</th>
                            <th class="px-3 py-2 text-left font-medium">Waktu</th>
                            <th class="px-3 py-2 text-left font-medium">Kategori</th>
                            <th class="px-3 py-2 text-left font-medium">Sub</th>
                            <th class="px-3 py-2 text-left font-medium">Status</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                        @forelse($pengajuans as $p)
                            <tr class="hover:bg-gray-800/40">
                                <td class="px-3 py-2 font-mono text-xs text-indigo-300">{{ $p->nomor_pengajuan }}</td>
                                <td class="px-3 py-2 text-gray-100">{{ $p->nama_instansi }}</td>
                                <td class="px-3 py-2 text-gray-300">{{ $p->atas_nama }}</td>
                                <td class="px-3 py-2 text-gray-300">{{ $p->tanggal_kunjungan->format('Y-m-d') }}</td>
                                <td class="px-3 py-2 text-gray-300">{{ substr($p->waktu_kunjungan,0,5) }}</td>
                                <td class="px-3 py-2 text-gray-300">{{ $p->category->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-300">{{ $p->subCategory->name ?? '-' }}</td>
                                <td class="px-3 py-2">@include('admin.pengajuan.partials.status-badge',['status'=>$p->status])</td>
                                <td class="px-3 py-2 text-right">
                                    <a href="{{ route('admin.pengajuan.show',$p) }}" class="inline-flex items-center px-2 py-1.5 rounded bg-gray-800 hover:bg-gray-700 text-xs text-gray-200">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-10 text-center text-gray-400 text-sm">Tidak ada data</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-gray-800">{{ $pengajuans->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

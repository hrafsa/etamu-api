{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 px-4 md:px-6 lg:px-8 space-y-8 bg-gray-900">
        <!-- KPI Cards -->
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-5 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-600/10 rounded-full blur-lg"></div>
                <p class="text-xs font-medium tracking-wide text-gray-400 uppercase">Total Users</p>
                <div class="mt-2 flex items-end gap-2">
                    <h3 class="text-3xl font-bold text-gray-100">{{ number_format($totalUsers) }}</h3>
                </div>
            </div>
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-5 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-600/10 rounded-full blur-lg"></div>
                <p class="text-xs font-medium tracking-wide text-gray-400 uppercase">Total Pengajuan</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-100">{{ number_format($totalPengajuan) }}</h3>
            </div>
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-5 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-500/10 rounded-full blur-lg"></div>
                <p class="text-xs font-medium tracking-wide text-gray-400 uppercase">Total Tamu (Peserta Disetujui)</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-100">{{ number_format($totalTamu) }}</h3>
            </div>
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-5 flex flex-col justify-between">
                <p class="text-xs font-medium tracking-wide text-gray-400 uppercase">Pending Pengajuan</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-100">{{ number_format($pendingPengajuan) }}</h3>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-700 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-white">Recent Activity</h3>
                    <p class="text-sm text-gray-400">Log aktivitas terbaru sistem E-Tamu</p>
                </div>
                <form method="GET" class="grid gap-3 sm:grid-cols-3 md:grid-cols-3 w-full md:w-auto">
                    <div class="sm:col-span-1">
                        <label class="block text-[11px] uppercase tracking-wide text-gray-400 mb-1">Aktivitas</label>
                        <select name="activity" class="w-full bg-gray-900 border border-gray-600 rounded text-sm text-gray-200">
                            <option value="">Semua</option>
                            <option value="create" @selected($activityType==='create')>Create</option>
                            <option value="status" @selected($activityType==='status')>Status</option>
                            <option value="update" @selected($activityType==='update')>Update</option>
                            <option value="delete" @selected($activityType==='delete')>Delete</option>
                        </select>
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-[11px] uppercase tracking-wide text-gray-400 mb-1">Per Page</label>
                        <select name="per_page" class="w-full bg-gray-900 border border-gray-600 rounded text-sm text-gray-200">
                            @foreach($perPageAllowed as $size)
                                <option value="{{ $size }}" @selected($perPage===$size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-1 flex items-end gap-2">
                        <button class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded cursor-pointer">Filter</button>
                        @if($activityType || request('per_page') )
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium px-4 py-2 rounded">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-700">
                    <thead class="bg-gray-700/40 text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">Aktivitas</th>
                        <th class="px-4 py-3 text-left font-medium">Keterangan</th>
                        <th class="px-4 py-3 text-left font-medium">Aktor</th>
                        <th class="px-4 py-3 text-left font-medium">Waktu</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-gray-300">
                    @forelse($activities as $log)
                        <tr class="hover:bg-gray-700/20">
                            <td class="px-4 py-3 align-top text-xs text-gray-400 font-mono">{{ $log['id'] }}</td>
                            <td class="px-4 py-3 align-top whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-100 font-medium text-xs uppercase tracking-wide">{{ str_replace(['pengajuan.','user.','category.','subcategory.'], '', $log['type']) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top text-sm leading-relaxed">
                                <div class="text-gray-200">{{ $log['description'] ?? '-' }}</div>
                                @if(!empty($log['properties']))
                                    <dl class="mt-1 grid gap-x-4 gap-y-1 text-xs text-gray-400" style="grid-template-columns: auto 1fr;">
                                        @foreach($log['properties'] as $k=>$v)
                                            @if(is_array($v) && isset($v['from']) && array_key_exists('to',$v))
                                                <dt class="font-medium">{{ $k }}</dt>
                                                <dd class="text-gray-300"><span class="line-through text-gray-500">{{ $v['from'] ?? '' }}</span> → <span class="text-indigo-300">{{ $v['to'] ?? '' }}</span></dd>
                                            @elseif(is_array($v) && isset($v['changed']))
                                                <dt class="font-medium">{{ $k }}</dt><dd class="text-amber-300">updated</dd>
                                            @else
                                                <dt class="font-medium">{{ $k }}</dt><dd>{{ is_scalar($v) ? $v : json_encode($v) }}</dd>
                                            @endif
                                        @endforeach
                                    </dl>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-gray-400">{{ $log['actor'] }}</td>
                            <td class="px-4 py-3 align-top text-xs text-gray-400 whitespace-nowrap">{{ $log['time'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada aktivitas.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-4 border-t border-gray-700 bg-gray-800/70">
                {{ $activities->links() }}
            </div>
        </div>

    </div>
</x-app-layout>

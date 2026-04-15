<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Detail Pengajuan</h2>
    </x-slot>

    <div class="py-6" x-data="{ openApprove:false, openReject:false }">
        <div class="mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="bg-emerald-600/10 border border-emerald-600/30 text-emerald-300 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-rose-600/10 border border-rose-600/30 text-rose-300 px-4 py-3 rounded space-y-1 text-sm">
                    @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
                </div>
            @endif

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wider text-gray-400 mb-1">Nomor Pengajuan</div>
                        <div class="font-mono text-indigo-300 text-sm">{{ $pengajuan->nomor_pengajuan }}</div>
                    </div>
                    <div>@include('admin.pengajuan.partials.status-badge',['status'=>$pengajuan->status])</div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-gray-400">Nama Instansi</div>
                            <div class="text-gray-100 font-medium">{{ $pengajuan->nama_instansi }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">Atas Nama</div>
                            <div class="text-gray-200">{{ $pengajuan->atas_nama }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-400">Jumlah Peserta</div>
                                <div class="text-gray-200">{{ $pengajuan->jumlah_peserta }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Phone</div>
                                <div class="text-gray-200">{{ $pengajuan->phone}}</div>
                            </div>

                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-400">Tanggal</div>
                                <div class="text-gray-200">{{ $pengajuan->tanggal_kunjungan->format('Y-m-d') }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Waktu</div>
                                <div class="text-gray-200">{{ substr($pengajuan->waktu_kunjungan,0,5) }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">Email</div>
                            <div class="text-gray-200">{{ $pengajuan->email }}</div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-400">Kategori</div>
                                <div class="text-gray-200">{{ $pengajuan->category->name ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Sub Kategori</div>
                                <div class="text-gray-200">{{ $pengajuan->subCategory->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">Tujuan</div>
                            <div class="text-gray-300 leading-relaxed whitespace-pre-line">{{ $pengajuan->tujuan }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">Dokumen</div>
                            @if($dokumenUrl)
                                <a href="{{ $dokumenUrl }}" target="_blank" class="inline-flex items-center gap-2 text-indigo-300 hover:text-indigo-200 text-sm underline">Lihat Dokumen</a>
                            @else
                                <div class="text-gray-500">Tidak ada dokumen</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">Diajukan Oleh</div>
                            <div class="text-gray-200">{{ $pengajuan->user->name ?? '-' }} <span class="text-gray-500">({{ $pengajuan->user->email ?? '-' }})</span></div>
                        </div>
                    </div>
                </div>

                @if($pengajuan->status === \App\Models\Pengajuan::STATUS_PENDING)
                    <div class="pt-6 border-t border-gray-800 flex flex-wrap gap-3">
                        <button type="button" @click="openApprove=true" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium px-4 py-2 rounded cursor-pointer">Setujui</button>
                        <button type="button" @click="openReject=true" class="inline-flex items-center gap-2 bg-rose-600/90 hover:bg-rose-600 text-white text-sm font-medium px-4 py-2 rounded cursor-pointer">Tolak</button>
                        <a href="{{ route('admin.pengajuan.index') }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-200 text-sm font-medium px-4 py-2 rounded">Kembali</a>
                    </div>

                    <!-- Approve Modal -->
                    <div x-show="openApprove" x-cloak class="fixed inset-0 z-40 flex items-center justify-center" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="openApprove=false"></div>
                        <div x-transition.opacity.scale.90 class="relative z-50 w-full max-w-sm bg-gray-900 border border-gray-700 rounded-lg shadow-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-100 mb-2">Konfirmasi Persetujuan</h4>
                            <p class="text-sm text-gray-400 mb-4">Setujui pengajuan <span class="font-medium text-gray-200">{{ $pengajuan->nomor_pengajuan }}</span>? Tindakan ini akan mengubah status menjadi <span class="text-emerald-300 font-medium">Disetujui</span>.</p>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="openApprove=false" class="px-3.5 py-2 text-sm rounded border border-gray-600 text-gray-200 hover:bg-gray-800 cursor-pointer">Batal</button>
                                <form method="POST" action="{{ route('admin.pengajuan.status',$pengajuan) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="disetujui" />
                                    <button type="submit" class="px-3.5 py-2 text-sm rounded bg-emerald-600 hover:bg-emerald-500 text-white font-medium cursor-pointer">Setujui</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /Approve Modal -->

                    <!-- Reject Modal -->
                    <div x-show="openReject" x-cloak class="fixed inset-0 z-40 flex items-center justify-center" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="openReject=false"></div>
                        <div x-transition.opacity.scale.90 class="relative z-50 w-full max-w-sm bg-gray-900 border border-gray-700 rounded-lg shadow-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-100 mb-2">Konfirmasi Penolakan</h4>
                            <p class="text-sm text-gray-400 mb-4">Tolak pengajuan <span class="font-medium text-gray-200">{{ $pengajuan->nomor_pengajuan }}</span>? Status akan menjadi <span class="text-rose-300 font-medium">Ditolak</span>.</p>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="openReject=false" class="px-3.5 py-2 text-sm rounded border border-gray-600 text-gray-200 hover:bg-gray-800 cursor-pointer">Batal</button>
                                <form method="POST" action="{{ route('admin.pengajuan.status',$pengajuan) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="ditolak" />
                                    <button type="submit" class="px-3.5 py-2 text-sm rounded bg-rose-600 hover:bg-rose-500 text-white font-medium cursor-pointer">Tolak</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /Reject Modal -->
                @else
                    <div class="pt-6 border-t border-gray-800">
                        <a href="{{ route('admin.pengajuan.index') }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-200 text-sm font-medium px-4 py-2 rounded">Kembali</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

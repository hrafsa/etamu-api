@php
    $map = [
        'pending' => 'bg-gray-600/10 text-gray-300 border-gray-600/40',
        'disetujui' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30',
        'ditolak' => 'bg-rose-500/10 text-rose-300 border-rose-500/30',
    ];
    $label = [
        'pending' => 'Pending',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
    ][$status] ?? ucfirst($status);
@endphp
<span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium {{ $map[$status] ?? 'bg-gray-700/20 text-gray-300 border-gray-600/40' }}">{{ $label }}</span>


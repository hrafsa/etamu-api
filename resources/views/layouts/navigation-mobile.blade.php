@php
    $links = [
        ['label'=>'Dashboard','route'=> route('dashboard'),'active'=> request()->routeIs('dashboard'),'icon'=>'M3 10.5 12 4l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5Z'],
        ['label'=>'Pengajuan','route'=> route('admin.pengajuan.index'),'active'=> request()->routeIs('admin.pengajuan.*'),'icon'=>'M4 6h16M4 12h16M4 18h7'],
        ['label'=>'Categories','route'=> route('admin.categories.index'),'active'=> request()->routeIs('admin.categories.*'),'icon'=>'M4 6h16M4 12h10M4 18h7'],
        ['label'=>'User Management','route'=> url('/user-management'),'active'=> request()->is('user-management'),'icon'=>'M16 12a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm6 8a8 8 0 1 0-16 0h16Z'],
    ];
@endphp
<nav class="space-y-1 px-2" aria-label="Mobile navigation">
    @foreach($links as $l)
        <a href="{{ $l['route'] }}"
           @click="$dispatch('close-mobile-nav'); mobileNav=false"
           class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition {{ $l['active'] ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}"
           aria-current="{{ $l['active'] ? 'page':'false' }}">
            <svg class="w-5 h-5 {{ $l['active'] ? 'text-indigo-300' : 'text-indigo-400 group-hover:text-indigo-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $l['icon'] }}" />
            </svg>
            <span>{{ $l['label'] }}</span>
        </a>
    @endforeach
</nav>

